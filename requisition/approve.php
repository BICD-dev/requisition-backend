<?php
require 'config.php'; // Database connection
require 'sendApprovalEmail.php';

$fullname = $_GET['fname'];
$category = $_GET['category'];
$requisition_id = $_GET['request_id'];
$approver_order = (int)$_GET['approver_order'];
$action = $_GET['action'];

if ($action === 'reject') {
    $updateRequest = "UPDATE `$category` SET approval_status = 'rejected' WHERE requisition_id = ?";
    $stmt = $conn->prepare($updateRequest);
    $stmt->bind_param("s", $requisition_id);
    $stmt->execute();
    $stmt->close();
    echo "Request Rejected.";
    exit;
} 

// Approve current approver
$stmt =$conn->prepare("UPDATE approvers SET approval_status = 'approved' WHERE requisition_id = ? AND approval_order = ?");

$stmt->bind_param("si", $requisition_id, $approver_order);
$stmt->execute();
$stmt->close();

// Get next approver
$stmt = $conn->prepare("SELECT approver_email FROM approvers WHERE requisition_id = ? AND approval_order = ?");
$next_order = $approver_order + 1;
$stmt->bind_param("si", $requisition_id, $next_order);
$stmt->execute();
$result = $stmt->get_result();
$nextApprover = $result->fetch_assoc();
$stmt->close();

if ($nextApprover) {
    sendApprovalEmail($fullname,$category,$requisition_id, $nextApprover['approver_email'], $next_order);
} else {
    // No more approvers, mark request as approved
    $updateRequest = "UPDATE `$category` SET approval_status = 'approved' WHERE requisition_id  = ?";
    $stmt = $conn->prepare($updateRequest);
    $stmt->bind_param("s", $requisition_id);
    $stmt->execute();
    $stmt->close();
}

echo "Approval processed.";
?>
