<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'db.php';

// Create job_postings table if not exists
function createJobPostingsTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS job_postings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        requirements TEXT NOT NULL,
        location VARCHAR(100) NOT NULL,
        department VARCHAR(100) NOT NULL,
        employment_type ENUM('Full-time', 'Part-time', 'Contract', 'Temporary') NOT NULL,
        salary_range VARCHAR(50),
        status ENUM('Open', 'Closed', 'Draft') DEFAULT 'Draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        die("Error creating table: " . $e->getMessage());
    }
}

// Initialize table
createJobPostingsTable($pdo);

// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Get ID if provided
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Handle the request
switch ($method) {
    case 'GET':
        // Get all job postings or single if ID is provided
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM job_postings WHERE id = ?");
            $stmt->execute([$id]);
            $job = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($job) {
                http_response_code(200);
                echo json_encode($job);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Job posting not found']);
            }
        } else {
            $status = isset($_GET['status']) ? $_GET['status'] : null;
            
            $query = "SELECT * FROM job_postings";
            $params = [];
            
            if ($status) {
                $query .= " WHERE status = ?";
                $params[] = $status;
            }
            
            $query .= " ORDER BY created_at DESC";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode($jobs);
        }
        break;
        
    case 'POST':
        // Create new job posting
        $data = json_decode(file_get_contents('php://input'), true);
        
        $required = ['title', 'description', 'requirements', 'location', 'department', 'employment_type'];
        $errors = [];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "$field is required";
            }
        }
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            exit;
        }
        
        $sql = "INSERT INTO job_postings 
                (title, description, requirements, location, department, employment_type, salary_range, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['requirements'],
                $data['location'],
                $data['department'],
                $data['employment_type'],
                $data['salary_range'] ?? null,
                $data['status'] ?? 'Draft'
            ]);
            
            $id = $pdo->lastInsertId();
            http_response_code(201);
            echo json_encode(['id' => $id, 'message' => 'Job posting created successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create job posting: ' . $e->getMessage()]);
        }
        break;
        
        case 'PUT':
            // Read input
            $data = json_decode(file_get_contents('php://input'), true);
        
            // Support both: ?id=123 OR {"id":123} in body
            $jobId = $id ?: ($data['id'] ?? null);
        
            if (!$jobId) {
                http_response_code(400);
                echo json_encode(['error' => 'Job ID is required']);
                exit;
            }
        
            $updates = [];
            $params = [];
        
            $fields = [
                'title', 'description', 'requirements', 'location',
                'department', 'employment_type', 'salary_range', 'status'
            ];
        
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
        
            if (empty($updates)) {
                http_response_code(400);
                echo json_encode(['error' => 'No fields to update']);
                exit;
            }
        
            $params[] = $jobId;
        
            $sql = "UPDATE job_postings SET " . implode(', ', $updates) . " WHERE id = ?";
        
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
        
                if ($stmt->rowCount() > 0) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Job posting updated successfully']);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Job posting not found or no changes made']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update job posting: ' . $e->getMessage()]);
            }
            break;
        
        
    case 'DELETE':
        // Delete job posting
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Job ID is required']);
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("DELETE FROM job_postings WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo json_encode(['message' => 'Job posting deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Job posting not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete job posting: ' . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}