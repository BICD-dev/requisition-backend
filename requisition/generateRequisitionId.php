<?php
function getNextRequisitionId($conn,$table) {
    // Get current year
    $currentYear = date("Y");

    // Query to find the last inserted requisition ID for the current year
    $query = "SELECT requisition_id FROM `$table`
              WHERE requisition_id LIKE ? 
              ORDER BY requisition_id DESC LIMIT 1";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die(json_encode(["error" => "Database error: " . $conn->error]));
    }

    // Define the pattern (e.g., "Req2024%")
    $yearPattern = "Req{$currentYear}%";
    $stmt->bind_param("s", $yearPattern);
    $stmt->execute();
    $stmt->bind_result($lastId);
    $stmt->fetch();
    $stmt->close();

    if ($lastId) {
        // Extract the numeric part and increment
        $numericPart = (int)substr($lastId, 7); // Extract last 5 digits
        $newNumericPart = str_pad($numericPart + 1, 5, "0", STR_PAD_LEFT);
    } else {
        // If no previous ID exists, start with "00000"
        $newNumericPart = "00000";
    }

    // Generate the new requisition ID
    return "Req{$currentYear}{$newNumericPart}";
}
?>
