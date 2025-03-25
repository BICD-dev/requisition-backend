<?php
    require "../vendor/autoload.php";
    use Dotenv\Dotenv;

    // Load .env file
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Get JWT secret key from .env
    $jwt_secret = $_ENV["JWT_SECRET"];

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Content-Type: application/json");
    

    // Handle preflight (OPTIONS) requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    // error reporting
    // error_reporting(0); 
    // ini_set('display_errors', 0);
    
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // connect to the database
    $conn = mysqli_connect("localhost", "Bright", "solnigeria", "solnigerialogin");
    // create a secret key for JWT


    if (!$conn) {
        echo json_encode(["error" => "Database connection failed: " . mysqli_connect_error()]);
        exit();
    } else{
        // Get raw POST data
        $eData = file_get_contents('php://input');
        file_put_contents("debug_log.txt", $eData . "\n", FILE_APPEND); // Log request 
        $dData = json_decode($eData, true);
        // Ensure JSON decoding is successful
        if (!$dData) {
            echo json_encode(["error" => "Invalid JSON received"]);
            exit();
        }
        // Check if required fields exist
        $fullname = isset($dData["fullname"]) ? $dData["fullname"] : null;
        $email = isset($dData["email"]) ? $dData["email"] : null;
        $password = isset($dData["password"]) ? $dData["password"] : null;
        $mode = isset($dData["mode"]) ? $dData["mode"] : null;
        
        // Hash password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        if (!empty($fullname) && !empty($email) && !empty($password) && !empty($mode)) {
            // Determine the table name based on `mode`
            $allowedModes = ["admin", "staff"]; // Allowed table names for security
            if (!in_array($mode, $allowedModes)) {
                echo json_encode(["error" => "Invalid mode selected"]);
                exit();
            }

            $table = ($mode === "admin") ? "admin" : "staff"; // Only allow predefined tables
            // Check if the email already exists
            $stmt = $conn->prepare("SELECT email FROM $table WHERE email = ?");
            if (!$stmt) {
                echo json_encode(["error" => "Database error: " . $conn->error]);
                exit();
            }
            
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo json_encode(["error" => "User already exists"]);
                exit();
            }
            $stmt->close();

            // Use prepared statement to prevent SQL injection

            $stmt = $conn->prepare("INSERT INTO $table (fullname, email, password) VALUES (?, ?, ?)");
            if (!$stmt) {
                echo json_encode(["error" => "Database error: " . $conn->error]);
                exit();
            }
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);
            $stmt->execute();
            
            echo json_encode(["success" => "Account created successfully"]);
    }else {
        echo json_encode(["error" => "All fields are required"]);
        exit();
    }

    }
?>