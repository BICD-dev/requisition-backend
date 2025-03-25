<?php
    // Database credentials
    $servername = "localhost";
    $username = "Bright";
    $password = "solnigeria";
    $database = "requisition_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    putenv("SMTP_HOST=smtp.gmail.com");
    putenv("SMTP_USERNAME=bright06iheagwam@gmail.com");
    putenv("SMTP_PASSWORD=bnmhzzmuvyclnmls");
    putenv("SMTP_PORT=587");
    putenv("SMTP_SECURE=tls");

?>