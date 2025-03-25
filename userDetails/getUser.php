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
    $dotenv->safeload();

    // ✅ Debugging: Check if JWT_SECRET exists
    if (!isset($_ENV["JWT_SECRET"])) {
        die(json_encode(["error" => "JWT_SECRET is missing in .env file"]));
    }

    // echo json_encode(["message" => "Dotenv loaded successfully!", "secret" => $_ENV["JWT_SECRET"]]);
    
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");

    // get authorization header
    $headers = getallheaders();

    if(!isset($headers["Authorization"])){
        echo json_encode(["error" => "Authorization header is required"]);
        exit();
    }
       // Extract Bearer token safely
       if (!preg_match('/Bearer\s(\S+)/', $headers["Authorization"], $matches)) {
        echo json_encode(["error" => "Invalid token format"]);
        exit();
    }
    $token = $matches[1];

    try {
        $jwt_secret = $_ENV["JWT_SECRET"] ?? null;
        if (!$jwt_secret) {
            throw new Exception("JWT_SECRET is missing in .env file");
        }

        $decoded = JWT::decode($token, new Key($jwt_secret, 'HS256'));
        echo json_encode(["user" => $decoded]);

    } catch (Exception $e) {
        
        echo json_encode(["token"=>$token,"error" => "Invalid token", "details" => $e->getMessage()]);
    }

?>