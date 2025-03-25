<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../generateRequisitionId.php";
require "../config.php";
require_once '../sendApprovalEmail.php';
require_once '../upload_invoice.php';

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Validate file upload
if (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["error" => "File upload failed."]);
    exit();
}

// Upload file to Google Drive
$tempFilePath = $_FILES['attachment']['tmp_name'];
$fileName = $_FILES['attachment']['name'];
$fileUrl = uploadToGoogleDrive($tempFilePath, $fileName);

// Extract form data
$fullname = $_POST["fullname"] ?? null;
$userId = $_POST["userId"] ?? null;
$accNumber = $_POST["accNumber"] ?? null;
$accName = $_POST["accName"] ?? null;
$accBank = $_POST["accBank"] ?? null;
$description = $_POST["description"] ?? null;
$amount = $_POST["amount"] ?? null;
$hodEmail = $_POST["hodEmail"] ?? null;
$cracoEmail = $_POST["cracoEmail"] ?? null;
$financeEmail = $_POST["financeEmail"] ?? null;

$requisition_id = getNextRequisitionId($conn, "payment_requisition");

if (!empty($fullname) && !empty($userId) && !empty($amount) && !empty($description) && !empty($accBank) && !empty($accName) && !empty($accNumber) && !empty($hodEmail) && !empty($cracoEmail) && !empty($financeEmail)) {

    $stmt = $conn->prepare("INSERT INTO payment_requisition (fullname, user_id, amount, account_bank, account_name, account_number, payment_description, requisition_id, attachment) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Database error (payment_requisition): " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssdssisss", $fullname, $userId, $amount, $accBank, $accName, $accNumber, $description, $requisition_id, $fileUrl);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Database error (payment_requisition execution): " . $stmt->error]);
        exit();
    }

    $stmt1 = $conn->prepare("INSERT INTO approvers (requisition_id, approver_email, approval_order, approval_status, category) 
                             VALUES (?, ?, 1, 'pending', 'payment requisition'),
                                    (?, ?, 2, 'pending', 'payment requisition'),
                                    (?, ?, 3, 'pending', 'payment requisition')");

    if (!$stmt1) {
        http_response_code(500);
        echo json_encode(["error" => "Database error (approvers): " . $conn->error]);
        exit();
    }

    $stmt1->bind_param("ssssss", $requisition_id, $hodEmail, $requisition_id, $cracoEmail, $requisition_id, $financeEmail);

    if (!$stmt1->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Database error (approvers execution): " . $stmt1->error]);
        exit();
    }

    $emailData = [
        "fullname" => $fullname,
        "description" => $description,
        "account_name" => $accName
    ];

    sendApprovalEmail($emailData, "payment_requisition", $requisition_id, $hodEmail, 1, $fileUrl);

    http_response_code(201);
    echo json_encode(["success" => "Request made successfully", "requisition_id" => $requisition_id, "file_url" => $fileUrl]);

} else {
    http_response_code(400);
    echo json_encode(["error" => "All fields are required"]);
    exit();
}

$stmt->close();
$stmt1->close();
$conn->close();
?>
