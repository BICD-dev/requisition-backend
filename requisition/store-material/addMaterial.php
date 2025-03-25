<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");
    
    // error reporting
    // error_reporting(0); 
    // ini_set('display_errors', 0);
    
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require "../config.php"; // Database connection
    require_once "../generateRequisitionId.php"; // Include function file
    require_once '../sendApprovalEmail.php';  // Include the function file

    
    // Handle preflight (OPTIONS) requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    
    
    // Check for connection errors
    if ($conn->connect_error) {
        echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
        exit();
    } else{
        // Get raw POST data
        $eData = file_get_contents('php://input');
        file_put_contents("material_log.txt", $eData . "\n", FILE_APPEND); // Log request 
        $dData = json_decode($eData, true);
        // Ensure JSON decoding is successful
        if (!$dData) {
            echo json_encode(["error" => "Invalid JSON received"]);
            exit();
        }
        // Check if required fields exist
        $fullname = isset($dData["fullname"]) ? $dData["fullname"] : null;
        $userId = isset($dData["userId"]) ? $dData["userId"] : null;
        $material = isset($dData["material"]) ? $dData["material"] : null;
        $remark = isset($dData["remark"]) ? $dData["remark"] : null;
        $quantity = isset($dData["quantity"]) ? $dData["quantity"] : null;
        $hodEmail = isset($dData["hodEmail"]) ? $dData["hodEmail"] : null;
        $cracoEmail = isset($dData["cracoEmail"]) ? $dData["cracoEmail"] : null;
        $financeEmail = isset($dData["financeEmail"]) ? $dData["financeEmail"] : null;
        
        // Generate requisition ID
        $requisition_id = getNextRequisitionId($conn, 'store-material');
        
        if (!empty($fullname) && !empty($userId) && !empty($material) && !empty($remark) && !empty($quantity) && !empty($hodEmail) && !empty($cracoEmail) && !empty($financeEmail)) {
            // Use prepared statement to prevent SQL injection

            $stmt = $conn->prepare("INSERT INTO `store-material` (fullname, user_id, material, quantity, remark, requisition_id) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                echo json_encode(["error" => "Database error: " . $conn->error]);
                exit();
            }
            $stmt->bind_param("sssiss", $fullname, $userId, $material, $quantity, $remark, $requisition_id);
            $stmt->execute();
            
            // Insert into `approvers`
        $stmt1 = $conn->prepare("INSERT INTO approvers (requisition_id, approver_email, approval_order, approval_status, category) 
                                VALUES (?, ?, 1, 'pending', 'store requisition'),
                                        (?, ?, 2, 'pending', 'store requisition'),
                                        (?, ?, 3, 'pending', 'store requisition')");

        if (!$stmt1) {
            http_response_code(500);
            echo json_encode(["error" => "Database error (approvers): " . $conn->error]);
            exit();
        }

        // Bind parameters and execute the second query
        $stmt1->bind_param("ssssss", 
            $requisition_id, $hodEmail, 
            $requisition_id, $cracoEmail, 
            $requisition_id, $financeEmail
        );
        // ✅ Call the sendApprovalEmail() function for each approver
        

        if (!$stmt1->execute()) {
            http_response_code(500);
            echo json_encode(["error" => "Database error (approvers execution): " . $stmt1->error]);
            exit();
        }
        
        sendApprovalEmail($fullname,"store-material",$requisition_id, $hodEmail, 1);
        http_response_code(201);
        echo json_encode(["success" => "Request made successfully","requisition_id" => $requisition_id]);
    }else {
        echo json_encode(["error" => "All fields are required"]);
        exit();
    }
    }
    $stmt->close();
    $stmt1->close();
    $conn->close();

?>