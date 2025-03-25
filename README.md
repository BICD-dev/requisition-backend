# Solnigeria Requisition Management System - Backend

This backend application is responsible for handling user authentication, managing requisitions, and sending approval emails via **PHPMailer**. It is built using **PHP** and connects to a MySQL database.

## Features

- **User Authentication**: Secure login and registration system
- **PHPMailer Integration**: Sends approval and rejection emails
- **Requisition Management**: Create and track requisitions
- **Approval System**: Handles multi-level approval processes

## Prerequisites

Ensure you have the following installed:

- PHP (>= 8.0)
- MySQL
- Composer (for dependency management)
- XAMPP (for local development, optional)

## Project Structure

```
.
├── requisition-backend/
│    ├── approval_page.php      # Displays approval buttons
│    ├── requisition/           # Handles requisition CRUD operations
│    └── user/                  # User authentication endpoints
├── config.php                  # Database connection
├── vendor/                     # Composer dependencies
└── README.md                   # Project documentation
```

## Setup Instructions

1. **Clone the Repository**

```bash
git clone https://github.com/BICD-dev/your-repo.git
cd your-repo
```

2. **Install Dependencies**

```bash
composer install
```

3. **Database Configuration**

Update your `config.php` file:

```php
<?php
$host = 'localhost';
$username = 'your_db_user';
$password = 'your_db_password';
$database = 'requisition_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

4. **Database Migration**

Run the following SQL script to set up the necessary tables:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE requisitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(100),
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approver_order INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requisition_id INT,
    approver_email VARCHAR(100),
    approval_status ENUM('sent', 'approved', 'rejected'),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Authentication System

### User Registration (POST `/api/user/register.php`)

**Request:**

```json
{
    "fullname": "John Doe",
    "email": "john@example.com",
    "password": "securepassword"
}
```

**Response:**

```json
{
    "message": "User registered successfully"
}
```

### User Login (POST `/api/user/login.php`)

**Request:**

```json
{
    "email": "john@example.com",
    "password": "securepassword"
}
```

**Response:**

```json
{
    "token": "your_jwt_token"
}
```

## Email Service with PHPMailer

Ensure you have configured **PHPMailer** in the `sendApprovalEmail()` function.

**Example Configuration:**

```php
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```

### Sending Approval Emails

The `sendApprovalEmail()` function sends an email to approvers with approve and reject links.

Usage:

```php
sendApprovalEmail($emailData, $category, $request_id, $approver_email, $approver_order, $fileUrl);
```

## Requisition Management

### Create Requisition (POST `/api/requisition/create.php`)

**Request:**

```json
{
    "user_id": 1,
    "category": "Office Supplies",
    "description": "Purchase of new chairs"
}
```

**Response:**

```json
{
    "message": "Requisition created successfully",
    "request_id": 123
}
```

## Approval Page

The `approval_page.php` displays dynamic buttons for approvers.

Example link in email:

```
https://your-ngrok-url/api/approval_page.php?request_id=123&approver_order=1
```

## Security Considerations

- Use **password_hash()** for storing passwords securely.
- Implement CSRF protection for sensitive actions.
- Ensure **HTTPS** is enabled in production.

## Troubleshooting

1. **Emails not being sent?**
   - Ensure you have enabled **Less Secure Apps** in Gmail or use **App Passwords**.

2. **Database errors?**
   - Verify database credentials and schema.

## License

This project is licensed under the MIT License.

