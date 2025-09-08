<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

include 'db.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ensure applicants has image_url column
function ensureApplicantsImageColumn($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'applicants' AND COLUMN_NAME = 'image_url'");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($row) || (int)$row['cnt'] === 0) {
            $pdo->exec("ALTER TABLE applicants ADD COLUMN image_url VARCHAR(500) NULL AFTER status");
        }
    } catch (PDOException $e) {
        // If table doesn't exist yet, create minimally
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS applicants (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                position VARCHAR(255) NOT NULL,
                status VARCHAR(100) DEFAULT 'Under Review',
                image_url VARCHAR(500) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB");
        } catch (PDOException $inner) {
            http_response_code(500);
            echo json_encode(['error' => 'Schema preparation failed: ' . $inner->getMessage()]);
            exit;
        }
    }
}

ensureApplicantsImageColumn($pdo);

// Ensure shortlists table exists (for auto-adding upon resume upload)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS shortlists (
        id INT AUTO_INCREMENT PRIMARY KEY,
        applicant_id INT NOT NULL,
        position VARCHAR(255) NOT NULL,
        screening_score INT DEFAULT 0,
        shortlist_status ENUM('Shortlisted','Pending Interview','Under Review','Rejected') DEFAULT 'Under Review',
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_shortlists_applicant_id (applicant_id),
        CONSTRAINT fk_shortlists_applicant FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB");
} catch (PDOException $e) {
    // ignore if creation fails; main flow still works
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        // Mirror applicant_status behavior using applicants table
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM applicants WHERE id = ?");
            $stmt->execute([$id]);
            $applicant = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($applicant) {
                echo json_encode($applicant);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Applicant not found']);
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM applicants ORDER BY created_at DESC");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        }
        break;

    case 'POST':
        // Create new applicant; support JSON and multipart (image upload)
        $contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');
        $isMultipart = stripos($contentType, 'multipart/form-data') !== false;

        if ($isMultipart) {
            $name = trim($_POST['name'] ?? '');
            $position = trim($_POST['position'] ?? '');
            $status = trim($_POST['status'] ?? 'Under Review');
            if ($name === '' || $position === '') {
                http_response_code(400);
                echo json_encode(['error' => 'Name and position are required']);
                break;
            }

            $imageUrl = null;
            if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'applicants';
                if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
                if ($safeExt === '') { $safeExt = 'jpg'; }
                $filename = 'app_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($safeExt);
                $dest = $uploadDir . DIRECTORY_SEPARATOR . $filename;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to save uploaded image']);
                    break;
                }
                // Public URL path relative to project root
                $imageUrl = '/hr1_merchandising/uploads/applicants/' . $filename;
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO applicants (name, position, status, image_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $position, $status, $imageUrl]);
                $newId = $pdo->lastInsertId();
                // Auto-add to shortlists
                try {
                    $map = ['Shortlisted' => 'Shortlisted', 'Interview Scheduled' => 'Pending Interview', 'Under Review' => 'Under Review', 'Rejected' => 'Rejected'];
                    $shortStatus = $map[$status] ?? 'Under Review';
                    $sel = $pdo->prepare("SELECT id FROM shortlists WHERE applicant_id = ? LIMIT 1");
                    $sel->execute([$newId]);
                    $exists = $sel->fetch(PDO::FETCH_ASSOC);
                    if ($exists) {
                        $upd = $pdo->prepare("UPDATE shortlists SET shortlist_status = ? WHERE id = ?");
                        $upd->execute([$shortStatus, $exists['id']]);
                    } else {
                        $ins = $pdo->prepare("INSERT INTO shortlists (applicant_id, position, screening_score, shortlist_status) VALUES (?, ?, 0, ?)");
                        $ins->execute([$newId, $position, $shortStatus]);
                    }
                } catch (PDOException $e2) { /* ignore shortlist errors */ }
                http_response_code(201);
                echo json_encode(['id' => $newId, 'name' => $name, 'position' => $position, 'status' => $status, 'image_url' => $imageUrl]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create record: ' . $e->getMessage()]);
            }
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!is_array($data)) { $data = []; }
            if (!empty($data['name']) && !empty($data['position'])) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO applicants (name, position, status, image_url) VALUES (?, ?, ?, ?)");
                    $status = $data['status'] ?? 'Under Review';
                    $imageUrl = $data['image_url'] ?? null;
                    $stmt->execute([$data['name'], $data['position'], $status, $imageUrl]);
                    $newId = $pdo->lastInsertId();
                    // Auto-add to shortlists
                    try {
                        $map = ['Shortlisted' => 'Shortlisted', 'Interview Scheduled' => 'Pending Interview', 'Under Review' => 'Under Review', 'Rejected' => 'Rejected'];
                        $shortStatus = $map[$status] ?? 'Under Review';
                        $sel = $pdo->prepare("SELECT id FROM shortlists WHERE applicant_id = ? LIMIT 1");
                        $sel->execute([$newId]);
                        $exists = $sel->fetch(PDO::FETCH_ASSOC);
                        if ($exists) {
                            $upd = $pdo->prepare("UPDATE shortlists SET shortlist_status = ? WHERE id = ?");
                            $upd->execute([$shortStatus, $exists['id']]);
                        } else {
                            $ins = $pdo->prepare("INSERT INTO shortlists (applicant_id, position, screening_score, shortlist_status) VALUES (?, ?, 0, ?)");
                            $ins->execute([$newId, $data['position'], $shortStatus]);
                        }
                    } catch (PDOException $e2) { /* ignore shortlist errors */ }
                    http_response_code(201);
                    echo json_encode(['id' => $newId, 'name' => $data['name'], 'position' => $data['position'], 'status' => $status, 'image_url' => $imageUrl]);
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to create record: ' . $e->getMessage()]);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Name and position are required']);
            }
        }
        break;

    case 'PUT':
        // Support JSON updates and multipart for image update
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Applicant ID is required']);
            break;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');
        $isMultipart = stripos($contentType, 'multipart/form-data') !== false;

        if ($isMultipart) {
            $fields = [];
            $values = [];
            if (isset($_POST['name']) && $_POST['name'] !== '') { $fields[] = 'name = ?'; $values[] = $_POST['name']; }
            if (isset($_POST['position']) && $_POST['position'] !== '') { $fields[] = 'position = ?'; $values[] = $_POST['position']; }
            if (isset($_POST['status']) && $_POST['status'] !== '') { $fields[] = 'status = ?'; $values[] = $_POST['status']; }

            if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'applicants';
                if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
                if ($safeExt === '') { $safeExt = 'jpg'; }
                $filename = 'app_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($safeExt);
                $dest = $uploadDir . DIRECTORY_SEPARATOR . $filename;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to save uploaded image']);
                    break;
                }
                $imageUrl = '/hr1_merchandising/uploads/applicants/' . $filename;
                $fields[] = 'image_url = ?';
                $values[] = $imageUrl;
            }

            if (empty($fields)) {
                http_response_code(400);
                echo json_encode(['error' => 'No fields to update']);
                break;
            }

            $values[] = $id;
            $sql = "UPDATE applicants SET " . implode(', ', $fields) . " WHERE id = ?";
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
                echo json_encode(['success' => 'Applicant updated successfully']);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update record: ' . $e->getMessage()]);
            }
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!is_array($data)) { $data = []; }
            $fields = [];
            $values = [];
            if (!empty($data['name'])) { $fields[] = 'name = ?'; $values[] = $data['name']; }
            if (!empty($data['position'])) { $fields[] = 'position = ?'; $values[] = $data['position']; }
            if (!empty($data['status'])) { $fields[] = 'status = ?'; $values[] = $data['status']; }
            if (!empty($data['image_url'])) { $fields[] = 'image_url = ?'; $values[] = $data['image_url']; }
            if (empty($fields)) {
                http_response_code(400);
                echo json_encode(['error' => 'No fields to update']);
                break;
            }
            $values[] = $id;
            $sql = "UPDATE applicants SET " . implode(', ', $fields) . " WHERE id = ?";
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
                echo json_encode(['success' => 'Applicant updated successfully']);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update record: ' . $e->getMessage()]);
            }
        }
        break;

    case 'DELETE':
        if ($id) {
            try {
                // fetch image url
                $imgStmt = $pdo->prepare("SELECT image_url FROM applicants WHERE id = ?");
                $imgStmt->execute([$id]);
                $row = $imgStmt->fetch(PDO::FETCH_ASSOC);

                $stmt = $pdo->prepare("DELETE FROM applicants WHERE id = ?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() > 0) {
                    if (!empty($row['image_url'])) {
                        $projectRoot = dirname(__DIR__);
                        $path = parse_url($row['image_url'], PHP_URL_PATH);
                        if (is_string($path) && strpos($path, '/hr1_merchandising/') === 0) {
                            $localPath = $projectRoot . substr($path, strlen('/hr1_merchandising'));
                            if (is_file($localPath)) { @unlink($localPath); }
                        }
                    }
                    echo json_encode(['success' => 'Applicant deleted successfully']);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Applicant not found']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete record: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Applicant ID is required']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>