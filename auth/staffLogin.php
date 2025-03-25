<?php
require "../vendor/autoload.php";
use Dotenv\Dotenv;
use Firebase\JWT\JWT;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$jwt_secret = $_ENV["JWT_SECRET"]; 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Hide errors in production (set to 1 for debugging)
// error_reporting(0); 
// ini_set('display_errors', 0);


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to the database
$conn = new mysqli("localhost", "Bright", "solnigeria", "solnigerialogin");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get raw POST data
$eData = file_get_contents('php://input');
$dData = json_decode($eData, true);

if (!$dData) {
    echo json_encode(["error" => "Invalid JSON received"]);
    exit();
}

// Extract email & password
$email = $dData["email"] ?? null;
$password = $dData["password"] ?? null;

if (!$email || !$password) {
    echo json_encode(["error" => "Email and password are required"]);
    exit();
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT uuid, fullname, email, password FROM staff WHERE email = ?");
if (!$stmt) {
    echo json_encode(["error" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["result" => "Invalid credentials"]);
    exit();
}

// Fetch user data
$user = $res->fetch_assoc();
$hashedPassword = $user["password"];

if (!password_verify($password, $hashedPassword)) {
    echo json_encode(["error" => "Invalid credentials"]);
    exit();
}

// Generate JWT Token
$payload = [
    "iss" => "http://localhost",  // Change this to your API domain
    "iat" => time(),
    "exp" => time() + 3600, // Token expires in 1 hour
    "user" => [
        "uid" => $user["uuid"],
        "name" => $user["fullname"],
        "email" => $user["email"],
        "role" => "staff"
    ]
];

$jwt = JWT::encode($payload, $jwt_secret, 'HS256');

// Close database connection
$stmt->close();
$conn->close();

// Return success message & JWT token
echo json_encode([
    "result" => "staff Log In successfully. Redirecting...",
    "token" => $jwt,
    "secret" => $jwt_secret,
    "error" => null
]);
?>
