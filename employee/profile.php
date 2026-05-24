<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireEmployee();

$user_id = $_SESSION['user_id'];

// Get employee info
$result = $conn->query(
    "SELECT e.*, d.dept_name, dg.designation_name FROM employees e 
    JOIN departments d ON e.dept_id = d.id 
    JOIN designations dg ON e.designation_id = dg.id 
    JOIN users u ON e.user_id = u.id WHERE u.id = $user_id"
);

$employee = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - EMS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .sidebar {
            width: 250px;
            background: #2c3e50;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            color: white;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #34495e;
            text-align: center;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #34495e;
            border-left-color: #3498db;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .topbar {
            background: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-card {
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: bold;
        }
        
        .profile-info h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .profile-info p {
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-group {
            display: flex;
            flex-direction: column;
        }
        
        .info-group label {
            color: #7f8c8d;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-group p {
            color: #2c3e50;
            font-size: 16px;
            font-weight: 500;
        }
        
        .logout-btn {
            padding: 8px 15px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>EMS Employee</h2>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="profile.php" class="active">👤 My Profile</a>
            <a href="timesheet.php">📋 Timesheet</a>
            <a href="leave_requests.php">🏖️ Leave Requests</a>
            <a href="expense_requests.php">💰 Expenses</a>
            <a href="my_tasks.php">📝 My Tasks</a>
            <a href="announcements.php">📢 Announcements</a>
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="topbar">
            <h1 style="color: #2c3e50;">My Profile</h1>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar"><?php echo strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)); ?></div>
                <div class="profile-info">
                    <h2><?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></h2>
                    <p><strong>Employee ID:</strong> <?php echo $employee['emp_id']; ?></p>
                    <p><strong>Email:</strong> <?php echo $employee['email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $employee['phone'] ?? 'N/A'; ?></p>
                </div>
            </div>
            
            <h3 style="color: #2c3e50; margin-bottom: 20px; margin-top: 30px;">Employment Details</h3>
            <div class="info-row">
                <div class="info-group">
                    <label>Department</label>
                    <p><?php echo $employee['dept_name']; ?></p>
                </div>
                <div class="info-group">
                    <label>Designation</label>
                    <p><?php echo $employee['designation_name']; ?></p>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-group">
                    <label>Joining Date</label>
                    <p><?php echo date('d-m-Y', strtotime($employee['joining_date'])); ?></p>
                </div>
                <div class="info-group">
                    <label>Employment Type</label>
                    <p><?php echo $employee['employment_type']; ?></p>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-group">
                    <label>Salary</label>
                    <p>₹ <?php echo number_format($employee['salary'], 2); ?></p>
                </div>
                <div class="info-group">
                    <label>Status</label>
                    <p><?php echo $employee['status']; ?></p>
                </div>
            </div>
            
            <h3 style="color: #2c3e50; margin-bottom: 20px; margin-top: 30px;">Personal Information</h3>
            <div class="info-row">
                <div class="info-group">
                    <label>Date of Birth</label>
                    <p><?php echo $employee['date_of_birth'] ? date('d-m-Y', strtotime($employee['date_of_birth'])) : 'N/A'; ?></p>
                </div>
                <div class="info-group">
                    <label>Gender</label>
                    <p><?php echo $employee['gender'] ?? 'N/A'; ?></p>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-group">
                    <label>Address</label>
                    <p><?php echo $employee['address'] ?? 'N/A'; ?></p>
                </div>
                <div class="info-group">
                    <label>City</label>
                    <p><?php echo $employee['city'] ?? 'N/A'; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>