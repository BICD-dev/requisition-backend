<?php
    require "../../../vendor/autoload.php";
    use Dotenv\Dotenv;

    // Load .env file
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Get JWT secret key from .env
    $jwt_secret = $_ENV["JWT_SECRET"];

   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Methods: GET, POST");
   header("Access-Control-Allow-Headers: Content-Type, Authorization");
   header("Content-Type: application/json"); 

    // Show all errors (for debugging in development)
    error_reporting(0); 
    ini_set('display_errors', 0);
    
    // Connect to the database
    $conn = new mysqli("localhost", "Bright", "solnigeria", "solnigerialogin");
    
    if ($conn->connect_error) {
        echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
        exit();
    }else{
        // Get raw POST data
        $eData = file_get_contents('php://input');
        $dData = json_decode($eData, true);

    }


    // Ensure JSON decoding is successful
    if (!$dData) {
        echo json_encode(["error" => "Invalid JSON received"]);
        exit();
    }

    //   Fix: Check if required fields exist
    $email = isset($dData["email"]) ? $dData["email"] : null;
    $password = isset($dData["password"]) ? $dData["password"] : null;

    if (!$email || !$password) {
        echo json_encode(["error" => "Email and password are required"]);
        exit();
    }

    //   Fix: Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM staff WHERE email = ?");
    if (!$stmt) {
        echo json_encode(["error" => "Database error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        // Ensure JSON response is always returned
        
        
        echo json_encode(["error" => "Staff not found","result" => "Invalid credentials" ]);
        exit();
    }

    // Fetch the hashed password from the database
    $row = $res->fetch_assoc();
    $hashedPassword = $row["password"];
    // var_dump($password);
    // var_dump($hashedPassword);
    // var_dump(password_verify($password, $hashedPassword));

    //  Fix: Verify the password using password_verify()
    if (password_verify($password, $hashedPassword)) {
        $result = "Staff Log In successfully. Redirecting...";
    } else {
        $result = "Invalid credentials";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();

    echo json_encode(["result" => $result, "error" => null]);
    
?>
