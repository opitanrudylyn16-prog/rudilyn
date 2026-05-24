<?php
/**
 * Employee Management System - Setup Script
 * This script will help you fix the database and admin credentials
 */

require_once 'config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['fix_admin'])) {
        // Drop and recreate the admin user with correct password
        $username = 'admin';
        $email = 'admin@employeemgmt.com';
        $password = 'Admin@123';
        
        // Create proper bcrypt hash
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Delete existing admin user
        $conn->query("DELETE FROM users WHERE username = '$username'");
        
        // Insert new admin with correct hash
        $query = "INSERT INTO users (username, email, password_hash, role, is_active) 
                 VALUES ('$username', '$email', '$password_hash', 'admin', 1)";
        
        if ($conn->query($query)) {
            $message = '✅ Admin credentials have been fixed! <br>Username: <strong>admin</strong><br>Password: <strong>Admin@123</strong><br><br>You can now <a href="login.php" style="color: #27ae60; font-weight: bold;">login here</a>';
        } else {
            $error = '❌ Error updating admin user: ' . $conn->error;
        }
    }
}

// Check if admin exists
$result = $conn->query("SELECT * FROM users WHERE username = 'admin'");
$admin_exists = $result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS Setup - Employee Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .setup-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }
        
        .setup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .setup-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .setup-header p {
            color: #666;
            font-size: 14px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .status-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        
        .status-box strong {
            color: #2c3e50;
        }
        
        .status-box p {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            transform: none;
        }
        
        .instructions {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #3498db;
        }
        
        .instructions h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .instructions ol {
            margin-left: 20px;
            color: #7f8c8d;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .instructions li {
            margin-bottom: 8px;
        }
        
        a {
            color: #3498db;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1>🔧 EMS Setup</h1>
            <p>Employee Management System - Admin Credential Fix</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="status-box">
            <strong>📊 Database Status</strong>
            <p>
                <?php 
                // Check database connection
                if ($conn->connect_error) {
                    echo '❌ Database connection failed: ' . $conn->connect_error;
                } else {
                    echo '✅ Database connected successfully';
                }
                ?>
            </p>
        </div>
        
        <div class="status-box">
            <strong>👤 Admin User Status</strong>
            <p>
                <?php 
                if ($admin_exists) {
                    echo '✅ Admin user exists in database';
                } else {
                    echo '❌ Admin user does NOT exist in database';
                }
                ?>
            </p>
        </div>
        
        <?php if (!$message && !$error): ?>
            <form method="POST" action="">
                <input type="hidden" name="fix_admin" value="1">
                <button type="submit" class="btn">🔐 Fix Admin Credentials Now</button>
            </form>
        <?php endif; ?>
        
        <div class="instructions">
            <h3>📝 Alternative Manual Fix:</h3>
            <ol>
                <li>Open <strong>phpMyAdmin</strong> at <code>http://localhost/phpmyadmin</code></li>
                <li>Go to <strong>employee_management</strong> database → <strong>users</strong> table</li>
                <li>Delete the existing admin user (if any)</li>
                <li>Click <strong>"Insert"</strong> and add:<br>
                    <ul style="margin-top: 8px; margin-left: 20px;">
                        <li><strong>username:</strong> admin</li>
                        <li><strong>email:</strong> admin@employeemgmt.com</li>
                        <li><strong>password_hash:</strong> <code>$2y$10$8L/hI.9CQ5PGfO5qo9Y0zuQU0XGzxu5L8j5dJX8L0xNE3L6dXJTVa</code></li>
                        <li><strong>role:</strong> admin</li>
                        <li><strong>is_active:</strong> 1</li>
                    </ul>
                </li>
                <li>Click <strong>"Go"</strong> to save</li>
                <li>Login with: <strong>admin</strong> / <strong>Admin@123</strong></li>
            </ol>
        </div>
    </div>
</body>
</html>