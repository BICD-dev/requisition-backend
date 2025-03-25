<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Hide errors in production (set to 1 for debugging)
// error_reporting(0); 
// ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../config.php"; // Database connection
// Get raw POST data

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT material, user_id, fullname, quantity, request_date, requisition_id, approval_status FROM `store-material` ");
if (!$stmt) {
    echo json_encode(["error" => "Database error: " . $conn->error]);
    exit();
}

// $stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["result" => "empty rows"]);
    exit();
}

// Fetch all data into an array
$requisitions = [];
while ($row = $res->fetch_assoc()) {
    // Convert request_date to formatted string
    $dateTime = new DateTime($row['request_date']);
    $row['request_date'] = $dateTime->format("Y-m-d H:i:s"); // Format as "YYYY-MM-DD HH:MM:SS"
    
    $requisitions[] = $row;
}
// Return JSON response
echo json_encode($requisitions);
?>
