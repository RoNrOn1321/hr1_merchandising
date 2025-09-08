<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php"; // your db.php file with $pdo connection

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case "GET":
            $stmt = $pdo->query("SELECT * FROM performance_records ORDER BY id DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) throw new Exception("Invalid JSON input");

            $stmt = $pdo->prepare("INSERT INTO performance_records (employee_name, position, department, score, performance_level) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data["employee_name"],
                $data["position"],
                $data["department"],
                $data["score"],
                $data["performance_level"]
            ]);
            echo json_encode(["message" => "Performance record created"]);
            break;

        case "PUT":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data || !isset($data["id"])) throw new Exception("Invalid data");

            $stmt = $pdo->prepare("UPDATE performance_records SET employee_name=?, position=?, department=?, score=?, performance_level=? WHERE id=?");
            $stmt->execute([
                $data["employee_name"],
                $data["position"],
                $data["department"],
                $data["score"],
                $data["performance_level"],
                $data["id"]
            ]);
            echo json_encode(["message" => "Performance record updated"]);
            break;

        case "DELETE":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data || !isset($data["id"])) throw new Exception("Invalid ID");

            $stmt = $pdo->prepare("DELETE FROM performance_records WHERE id=?");
            $stmt->execute([$data["id"]]);
            echo json_encode(["message" => "Performance record deleted"]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
