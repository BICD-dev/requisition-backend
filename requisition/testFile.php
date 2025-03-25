<?php
require 'config.php'; // Database connection

// Verify if autoload.php exists before requiring
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Error: autoload.php not found at $autoloadPath");
}

require $autoloadPath;

// use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Load .env file
// $dotenv = Dotenv::createImmutable(__DIR__ . '/');
// $dotenv->load();

// Debugging outputs
echo "✅ Autoload file loaded successfully!<br>";

// Check if PHPMailer is available
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer class is accessible.<br>";
} else {
    echo "❌ PHPMailer class not found!<br>";
}
// check if .env file exists
if (!file_exists(__DIR__ . '/.env')) {
    die("❌ Error: .env file not found in " . __DIR__);
}else{
    echo "✅ .env file found in " . __DIR__ . "<br>";
}
// Check if dotenv variables are loaded
$smtpHost = getenv('SMTP_HOST');
if ($smtpHost) {
    echo "✅ Dotenv loaded successfully. SMTP_HOST: $smtpHost<br>";
} else {
    echo "❌ Dotenv failed to load environment variables.<br>";
}
?>
