<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once 'db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ensure base applicants table exists (for FK)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS applicants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        position VARCHAR(255) NOT NULL,
        status VARCHAR(100) DEFAULT 'Under Review',
        image_url VARCHAR(500) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed ensuring applicants table: ' . $e->getMessage()]);
    exit;
}

// Create shortlists table
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
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create shortlists table: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        // List or single
        if ($id) {
            $stmt = $pdo->prepare("SELECT s.*, a.name, a.image_url FROM shortlists s JOIN applicants a ON a.id = s.applicant_id WHERE s.id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) echo json_encode($row); else { http_response_code(404); echo json_encode(['error' => 'Not found']); }
        } else {
            $stmt = $pdo->query("SELECT s.*, a.name, a.image_url FROM shortlists s JOIN applicants a ON a.id = s.applicant_id ORDER BY s.created_at DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) { $data = []; }

        $score = (int)($data['screening_score'] ?? 0);
        $status = $data['shortlist_status'] ?? 'Under Review';
        $notes = $data['notes'] ?? null;

        // Resolve applicant_id: either provided directly and must exist, or create/find via name+position
        $applicantId = $data['applicant_id'] ?? null;
        $position = trim($data['position'] ?? '');

        try {
            if ($applicantId) {
                $chk = $pdo->prepare("SELECT id, position FROM applicants WHERE id = ?");
                $chk->execute([$applicantId]);
                $app = $chk->fetch(PDO::FETCH_ASSOC);
                if (!$app) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Applicant not found for applicant_id']);
                    break;
                }
                if ($position === '') { $position = $app['position']; }
            } else {
                // Try resolve by name+position
                $name = trim($data['name'] ?? '');
                if ($name === '' || ($position === '')) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Provide either applicant_id or name and position']);
                    break;
                }
                // Find existing applicant
                $find = $pdo->prepare("SELECT id FROM applicants WHERE name = ? AND position = ? LIMIT 1");
                $find->execute([$name, $position]);
                $found = $find->fetch(PDO::FETCH_ASSOC);
                if ($found) {
                    $applicantId = (int)$found['id'];
                } else {
                    // Create applicant
                    $ins = $pdo->prepare("INSERT INTO applicants (name, position, status) VALUES (?, ?, 'Under Review')");
                    $ins->execute([$name, $position]);
                    $applicantId = (int)$pdo->lastInsertId();
                }
            }

            // Final validation
            if ($position === '') {
                http_response_code(400);
                echo json_encode(['error' => 'Position is required']);
                break;
            }

            $stmt = $pdo->prepare("INSERT INTO shortlists (applicant_id, position, screening_score, shortlist_status, notes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$applicantId, $position, $score, $status, $notes]);
            // Optional: reflect shortlist status to applicants.status
            try {
                $map = ['Shortlisted' => 'Shortlisted', 'Pending Interview' => 'Interview Scheduled', 'Under Review' => 'Under Review', 'Rejected' => 'Rejected'];
                $appStatus = $map[$status] ?? null;
                if ($appStatus) {
                    $upd = $pdo->prepare("UPDATE applicants SET status = ? WHERE id = ?");
                    $upd->execute([$appStatus, $applicantId]);
                }
            } catch (PDOException $e2) { /* ignore */ }
            $newId = $pdo->lastInsertId();
            http_response_code(201);
            echo json_encode(['id' => $newId, 'message' => 'Shortlist entry created']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Create failed: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) { $data = []; }
        $rowId = $id ?: ($data['id'] ?? null);
        if (!$rowId) { http_response_code(400); echo json_encode(['error' => 'ID is required']); exit; }
        $fields = [];
        $params = [];
        foreach (['applicant_id','position','screening_score','shortlist_status','notes'] as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (empty($fields)) { http_response_code(400); echo json_encode(['error' => 'No fields to update']); exit; }
        $params[] = $rowId;
        $sql = "UPDATE shortlists SET " . implode(', ', $fields) . " WHERE id = ?";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            // If status changed, reflect to applicants.status
            if (array_key_exists('shortlist_status', $data)) {
                $map = ['Shortlisted' => 'Shortlisted', 'Pending Interview' => 'Interview Scheduled', 'Under Review' => 'Under Review', 'Rejected' => 'Rejected'];
                $appStatus = $map[$data['shortlist_status']] ?? null;
                if ($appStatus) {
                    $upd = $pdo->prepare("UPDATE applicants a JOIN shortlists s ON s.applicant_id = a.id SET a.status = ? WHERE s.id = ?");
                    $upd->execute([$appStatus, $rowId]);
                }
            }
            echo json_encode(['message' => 'Shortlist updated']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed: ' . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID is required']); exit; }
        try {
            $stmt = $pdo->prepare("DELETE FROM shortlists WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['message' => 'Shortlist deleted']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Delete failed: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>


