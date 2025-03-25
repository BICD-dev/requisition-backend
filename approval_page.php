<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Actions</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #e3f2fd, #90caf9);
            color: #333;
        }
        .container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            max-width: 500px;
        }
        h1 {
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            border-radius: 8px;
            font-size: 18px;
            text-decoration: none;
            color: white;
            transition: transform 0.3s ease;
        }
        .button:hover {
            transform: translateY(-3px);
        }
        .approve {
            background: #2ecc71;
        }
        .reject {
            background: #e74c3c;
        }
        .download {
            background: #3498db;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Solnigeria Approval System</h1>

        <?php
        $fullname = htmlspecialchars($_GET['fname'] ?? '');
        $category = htmlspecialchars($_GET['category'] ?? '');
        $request_id = htmlspecialchars($_GET['request_id'] ?? '');
        $approver_order = htmlspecialchars($_GET['approver_order'] ?? '');
        $invoiceLink = htmlspecialchars($_GET['invoice_url'] ?? '');
        
        $approvalLink = "https://1aa2-41-184-162-33.ngrok-free.app/approve.php?&fname=$fullname&category=$category&request_id=$request_id&approver_order=$approver_order&action=approve";
        $rejectionLink = "https://1aa2-41-184-162-33.ngrok-free.app/approve.php?&fname=$fullname&category=$category&request_id=$request_id&approver_order=$approver_order&action=reject";
        $fileUrl = $invoiceLink; // Replace with your invoice URL
        ?>

        <a href="<?php echo $approvalLink; ?>" class="button approve">✅ Approve</a>
        <a href="<?php echo $rejectionLink; ?>" class="button reject">❌ Reject</a>
        <a href="<?php echo $fileUrl; ?>" class="button download" download>📥 Download Invoice</a>
    </div>
</body>
</html>
