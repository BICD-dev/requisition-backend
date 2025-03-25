<?php
require dirname(__DIR__, 1) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config.php'; // Database connection

function sendApprovalEmail($emailData, $category, $request_id, $approver_email, $approver_order, $fileUrl) {
    global $conn; // Ensure database connection is available

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; 
    $mail->SMTPAuth   = true;
    $mail->Username   = 'brightiheagwam1@gmail.com'; 
    $mail->Password   = 'bnmhzzmuvyclnmls'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Extract variables from $emailData
    $fullname = $emailData['fullname'];
    $description = $emailData['description'];
    $account_name = $emailData['account_name'];

    // Construct approval page URL with query parameters
    // $approvalPage = "https://meet.google.com/mhm-tivk-aox";
    $approvalPage = "https://1aa2-41-184-162-33.ngrok-free.app/api/approval_page.php?" . http_build_query([
        'fname' => $fullname,
        'category' => $category,
        'request_id' => $request_id,
        'approver_order' => $approver_order,
        'invoice_url' => $fileUrl
    ]);

   
    // Email settings
    $mail->setFrom('brightiheagwam1@gmail.com', 'Store Approval System');
    $mail->addAddress($approver_email);
    $mail->Subject = 'Approval Request';
    $mail->isHTML(true);

    // Email Body
    $mail->Body = "
    <div style='font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background: #ffffff;'>
        <h2 style='color: #2C3E50; text-align: center;'>🔔 Approval Request Notification</h2>
        
        <p style='font-size: 16px; color: #34495E;'>
            You have a <strong style='color: #3498DB;'>$category</strong> approval request from <strong>$fullname</strong>.
        </p>

        <div style='background: #F9F9F9; padding: 15px; border-left: 4px solid #3498DB; margin: 20px 0; border-radius: 8px;'>
            <p><strong>📌 Request Details:</strong></p>
            <p><strong>Request ID:</strong> $request_id</p>
            <p><strong>Name of Requester:</strong> $fullname</p>
            <p><strong>Description:</strong> $description</p>
            <p><strong>Account Name:</strong> $account_name</p>
            <p><strong>Category:</strong> $category</p>
        </div>

        <p style='text-align: center;'>
            <a href='$approvalPage' style='text-decoration: none; background: #2ECC71; color: #ffffff; padding: 12px 24px; border-radius: 8px; margin-right: 10px; display: inline-block;'>Approval Page</a>
        </p>


        <p style='font-size: 14px; color: #7F8C8D; text-align: center;'>
            If you have any questions, please contact the support team.
        </p>
    </div>
    ";

    // Send email and log result
    if ($mail->send()) {
        $stmt = $conn->prepare("INSERT INTO email_logs (requisition_id, approver_email, approval_status, sent_at) VALUES (?, ?, 'sent', NOW())");
        $stmt->bind_param("ss", $request_id, $approver_email);
        $stmt->execute();
        $stmt->close();
        return true;
    } else {
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>
