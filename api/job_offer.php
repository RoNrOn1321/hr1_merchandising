<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db.php';

// Handle OPTIONS request (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case "GET":
            $stmt = $pdo->query("SELECT * FROM job_offers ORDER BY id DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) { throw new Exception("Invalid JSON input"); }

            $stmt = $pdo->prepare("INSERT INTO job_offers (candidate_name, position, salary, offer_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data["candidate_name"],
                $data["position"],
                $data["salary"],
                $data["offer_date"],
                $data["status"] ?? "Pending"
            ]);
            echo json_encode(["message" => "Job offer created"]);
            break;

        case "PUT":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data || !isset($data["id"])) { throw new Exception("Invalid data"); }

            $stmt = $pdo->prepare("UPDATE job_offers SET candidate_name=?, position=?, salary=?, offer_date=?, status=? WHERE id=?");
            $stmt->execute([
                $data["candidate_name"],
                $data["position"],
                $data["salary"],
                $data["offer_date"],
                $data["status"],
                $data["id"]
            ]);
            echo json_encode(["message" => "Job offer updated"]);
            break;

        case "DELETE":
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data || !isset($data["id"])) { throw new Exception(["Invalid ID"]); }

            $stmt = $pdo->prepare("DELETE FROM job_offers WHERE id=?");
            $stmt->execute([$data["id"]]);
            echo json_encode(["message" => "Job offer deleted"]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
