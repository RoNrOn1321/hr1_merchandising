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

            // Validate rating is numeric and between 1-5
            if (!isset($data["rating"]) || !is_numeric($data["rating"]) || $data["rating"] < 1 || $data["rating"] > 5) {
                throw new Exception("Rating must be a number between 1 and 5");
            }

            // Convert rating to integer
            $data["rating"] = intval($data["rating"]);

            // Validate required fields
            $requiredFields = ["employee_name", "department", "rating", "feedback_text"];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            $stmt = $pdo->prepare("INSERT INTO feedback (employee_name, department, rating, feedback_text, evaluator, date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data["employee_name"],
                $data["department"],
                $data["rating"],
                $data["feedback_text"],
                $data["evaluator"] ?? "Admin",
                $data["date"] ?? date("Y-m-d")
            ]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Failed to insert feedback");
            }

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

            if ($stmt->rowCount() === 0) {
                throw new Exception("Failed to update feedback");
            }

            echo json_encode(["message" => "Feedback updated"]);
            break;

        case "DELETE":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data || !isset($data["id"])) throw new Exception("Invalid ID");

            $stmt = $pdo->prepare("DELETE FROM feedback WHERE id=?");
            $stmt->execute([$data["id"]]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Failed to delete feedback");
            }

            echo json_encode(["message" => "Feedback deleted"]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
