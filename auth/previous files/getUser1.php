<?php
    require "../../../vendor/autoload.php"; // Ensure correct path to autoload.php

    use Dotenv\Dotenv;
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    // ✅ Check if class exists to verify autoloading
    if (!class_exists('Dotenv\Dotenv')) {
        echo json_encode(["error" => "Dotenv class not found!"]);
        exit();
    }

    // ✅ Load .env from the current directory (userDetails)
    $dotenv = Dotenv::createImmutable(__DIR__); 
    $dotenv->load();

    // ✅ Debugging: Check if JWT_SECRET exists
    if (!isset($_ENV["JWT_SECRET"])) {
        die(json_encode(["error" => "JWT_SECRET is missing in .env file"]));
    }

    echo json_encode(["message" => "Dotenv loaded successfully!", "secret" => $_ENV["JWT_SECRET"]]);
?>
