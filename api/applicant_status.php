<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'db.php';

// Ensure shortlists table exists (used when syncing shortlisted status)
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
    // ignore - fallback to pure applicant update if creation fails
}

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Get ID if provided
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Handle preflight OPTIONS request
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get request body
$input = json_decode(file_get_contents('php://input'), true);

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        // Get all applicants or single applicant if ID is provided
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM applicants WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                echo json_encode($result);
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
        // Create new applicant
        if (!empty($input['name']) && !empty($input['position'])) {
            $stmt = $pdo->prepare("INSERT INTO applicants (name, position, status) VALUES (?, ?, ?)");
            $status = $input['status'] ?? 'Under Review';
            $stmt->execute([$input['name'], $input['position'], $status]);
            $id = $pdo->lastInsertId();
            http_response_code(201);
            echo json_encode(['id' => $id, 'name' => $input['name'], 'position' => $input['position'], 'status' => $status]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Name and position are required']);
        }
        break;

    case 'PUT':
        // Update existing applicant
        if ($id) {
            $fields = [];
            $values = [];
            
            if (!empty($input['name'])) {
                $fields[] = 'name = ?';
                $values[] = $input['name'];
            }
            if (!empty($input['position'])) {
                $fields[] = 'position = ?';
                $values[] = $input['position'];
            }
            if (!empty($input['status'])) {
                $fields[] = 'status = ?';
                $values[] = $input['status'];
            }
            
            if (!empty($fields)) {
                $values[] = $id;
                $sql = "UPDATE applicants SET " . implode(', ', $fields) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
                
                if ($stmt->rowCount() > 0) {
                    // Sync to shortlists when status is updated
                    if (!empty($input['status'])) {
                        try {
                            $map = [
                                'Shortlisted' => 'Shortlisted',
                                'Interview Scheduled' => 'Pending Interview',
                                'Under Review' => 'Under Review',
                                'Rejected' => 'Rejected'
                            ];
                            $target = $map[$input['status']] ?? null;
                            // Fetch applicant for position
                            $a = $pdo->prepare("SELECT id, position FROM applicants WHERE id = ?");
                            $a->execute([$id]);
                            $app = $a->fetch(PDO::FETCH_ASSOC);
                            if ($app) {
                                if ($target === 'Shortlisted') {
                                    // Ensure a shortlist row exists; create if missing
                                    $sel = $pdo->prepare("SELECT id FROM shortlists WHERE applicant_id = ? LIMIT 1");
                                    $sel->execute([$id]);
                                    $existing = $sel->fetch(PDO::FETCH_ASSOC);
                                    if ($existing) {
                                        $upd = $pdo->prepare("UPDATE shortlists SET shortlist_status = 'Shortlisted' WHERE id = ?");
                                        $upd->execute([$existing['id']]);
                                    } else {
                                        $ins = $pdo->prepare("INSERT INTO shortlists (applicant_id, position, screening_score, shortlist_status) VALUES (?, ?, 0, 'Shortlisted')");
                                        $ins->execute([$id, $app['position']]);
                                    }
                                } elseif ($target) {
                                    // Update existing shortlist status if exists
                                    $upd = $pdo->prepare("UPDATE shortlists SET shortlist_status = ? WHERE applicant_id = ?");
                                    $upd->execute([$target, $id]);
                                }
                            }
                        } catch (PDOException $e) {
                            // ignore sync errors; keep applicant updated
                        }
                    }
                    echo json_encode(['success' => 'Applicant updated successfully']);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Applicant not found or no changes made']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'No fields to update']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Applicant ID is required']);
        }
        break;

    case 'DELETE':
        // Delete applicant
        if ($id) {
            // Fetch image_url before deletion
            $imgStmt = $pdo->prepare("SELECT image_url FROM applicants WHERE id = ?");
            $imgStmt->execute([$id]);
            $row = $imgStmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("DELETE FROM applicants WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                // Try to delete the image file if present
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