<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php'; // your db connection

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all interviews
        $stmt = $pdo->query("SELECT * FROM interviews ORDER BY interview_date, interview_time");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rows);
        break;

    case 'POST':
        // Add new interview
        $data = json_decode(file_get_contents("php://input"), true);

        // Debugging: Log the received data
        error_log(print_r($data, true));

        // Ensure 'status' is set to 'Pending' if not provided
        $data['status'] = $data['status'] ?? 'Pending';

        $sql = "INSERT INTO interviews (candidate_name, position, interview_date, interview_time, status)
                VALUES (:candidate_name, :position, :interview_date, :interview_time, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':candidate_name' => $data['candidate_name'],
            ':position' => $data['position'],
            ':interview_date' => $data['interview_date'],
            ':interview_time' => $data['interview_time'],
            ':status' => $data['status']
        ]);

        echo json_encode(["message" => "Interview scheduled successfully"]);
        break;

    case 'PUT':
        // Update interview
        $data = json_decode(file_get_contents("php://input"), true);

        $sql = "UPDATE interviews SET candidate_name=:candidate_name, position=:position,
                interview_date=:interview_date, interview_time=:interview_time, status=:status
                WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':candidate_name' => $data['candidate_name'],
            ':position' => $data['position'],
            ':interview_date' => $data['interview_date'],
            ':interview_time' => $data['interview_time'],
            ':status' => $data['status'] ?? 'Pending', // Use null coalescing operator for fallback
            ':id' => $data['id']
        ]);

        echo json_encode(["message" => "Interview updated successfully"]);
        break;

    case 'DELETE':
        // Delete interview
        $data = json_decode(file_get_contents("php://input"), true);

        $sql = "DELETE FROM interviews WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":id" => $data['id']]);

        echo json_encode(["message" => "Interview deleted successfully"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request"]);
        break;
}
