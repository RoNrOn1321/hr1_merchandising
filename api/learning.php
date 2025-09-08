<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php"; // your PDO connection

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch($method) {
        case "GET":
            $stmt = $pdo->query("SELECT * FROM learning_modules ORDER BY id DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!$data) throw new Exception("Invalid JSON input");

            $stmt = $pdo->prepare("INSERT INTO learning_modules (module_name, category, duration, start_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['module_name'],
                $data['category'],
                $data['duration'],
                $data['start_date'],
                $data['status'] ?? 'Upcoming'
            ]);
            echo json_encode(["message" => "Module created successfully"]);
            break;

        case "PUT":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!$data || !isset($data["id"])) throw new Exception("Invalid data");

            $stmt = $pdo->prepare("UPDATE learning_modules SET module_name=?, category=?, duration=?, start_date=?, status=? WHERE id=?");
            $stmt->execute([
                $data['module_name'],
                $data['category'],
                $data['duration'],
                $data['start_date'],
                $data['status'],
                $data['id']
            ]);
            echo json_encode(["message" => "Module updated successfully"]);
            break;

        case "DELETE":
            $data = json_decode(file_get_contents("php://input"), true);
            if(!$data || !isset($data["id"])) throw new Exception("Invalid ID");

            $stmt = $pdo->prepare("DELETE FROM learning_modules WHERE id=?");
            $stmt->execute([$data["id"]]);
            echo json_encode(["message" => "Module archived successfully"]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
