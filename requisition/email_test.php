<?php

require '../vendor/autoload.php'; // Ensure this path is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; 
    $mail->SMTPAuth   = true;
    $mail->Username   = 'brightiheagwam1@gmail.com'; 
    $mail->Password   = 'bnmhzzmuvyclnmls'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Sender & Recipient
    $mail->setFrom('brightiheagwam1@gmail.com', 'Test Mail');
    $mail->addAddress('bright06iheagwam@gmail.com', 'Receiver Name');

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = '<h2>Hello! This is a test email.</h2><p>Sent using PHPMailer with gmail SMTP.</p>';

    // Send Email
    if ($mail->send()) {
        echo '✅ Test email sent successfully!';
    } else {
        echo '❌ DAMN Email failed: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo '❌ OH BOY Email failed: ' . $mail->ErrorInfo;
}
?>
