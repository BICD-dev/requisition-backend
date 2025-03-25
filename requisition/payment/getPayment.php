<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../config.php"; // Database connection

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT user_id, fullname, amount, account_bank, account_name, account_number, 
                                payment_description, transaction_date, requisition_id, approval_status 
                        FROM payment_requisition");

if (!$stmt) {
    echo json_encode(["error" => "Database error: " . $conn->error]);
    exit();
}

$stmt->execute();
$res = $stmt->get_result();

// Fetch all data into an array
$requisitions = [];
while ($row = $res->fetch_assoc()) {
    // Convert request_date to formatted string
    if (!empty($row['transaction_date'])) {
        $dateTime = new DateTime($row['transaction_date']);
        $row['request_date'] = $dateTime->format("Y-m-d H:i:s"); // Format as "YYYY-MM-DD HH:MM:SS"
    }
    
    $requisitions[] = $row;
}

// Return JSON response
echo json_encode($requisitions);

// Close resources
$stmt->close();
$conn->close();
?>
