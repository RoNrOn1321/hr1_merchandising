<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php"; // Your db.php with $pdo

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case "GET":
            $stmt = $pdo->query("SELECT * FROM feedback ORDER BY id DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) throw new Exception("Invalid JSON input");

            $stmt = $pdo->prepare("INSERT INTO feedback (employee_name, department, rating, feedback_text, evaluator, date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data["employee_name"],
                $data["department"],
                $data["rating"],
                $data["feedback_text"],
                $data["evaluator"] ?? "Admin",
                $data["date"] ?? date("Y-m-d")
            ]);

            echo json_encode(["message" => "Feedback created"]);
            break;

        case "PUT":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data || !isset($data["id"])) throw new Exception("Invalid data");

            $stmt = $pdo->prepare("UPDATE feedback SET employee_name=?, department=?, rating=?, feedback_text=?, evaluator=?, date=? WHERE id=?");
            $stmt->execute([
                $data["employee_name"],
                $data["department"],
                $data["rating"],
                $data["feedback_text"],
                $data["evaluator"] ?? "Admin",
                $data["date"] ?? date("Y-m-d"),
                $data["id"]
            ]);

            echo json_encode(["message" => "Feedback updated"]);
            break;

        case "DELETE":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data || !isset($data["id"])) throw new Exception("Invalid ID");

            $stmt = $pdo->prepare("DELETE FROM feedback WHERE id=?");
            $stmt->execute([$data["id"]]);

            echo json_encode(["message" => "Feedback deleted"]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
