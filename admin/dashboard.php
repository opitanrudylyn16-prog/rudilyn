<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireAdmin();

// Get Dashboard Statistics
$stats = [];

// Total Employees
$result = $conn->query("SELECT COUNT(*) as count FROM employees");
$stats['total_employees'] = $result->fetch_assoc()['count'];

// Active Employees
$result = $conn->query("SELECT COUNT(*) as count FROM employees WHERE status = 'Active'");
$stats['active_employees'] = $result->fetch_assoc()['count'];

// Pending Leave Requests
$result = $conn->query("SELECT COUNT(*) as count FROM leave_requests WHERE status = 'Pending'");
$stats['pending_leaves'] = $result->fetch_assoc()['count'];

// Pending Expenses
$result = $conn->query("SELECT COUNT(*) as count FROM expense_requests WHERE status = 'Pending'");
$stats['pending_expenses'] = $result->fetch_assoc()['count'];

// Total Departments
$result = $conn->query("SELECT COUNT(*) as count FROM departments");
$stats['total_departments'] = $result->fetch_assoc()['count'];

// Today's Absent
$result = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date = CURDATE() AND status = 'Absent'");
$stats['todays_absent'] = $result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EMS</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css">
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
        
        .sidebar-header h2 {
            font-size: 20px;
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
        
        .topbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
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
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 5px;
        }
        
        .page-header p {
            color: #7f8c8d;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #3498db;
        }
        
        .stat-card.success {
            border-left-color: #27ae60;
        }
        
        .stat-card.warning {
            border-left-color: #f39c12;
        }
        
        .stat-card.danger {
            border-left-color: #e74c3c;
        }
        
        .stat-card-number {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-card-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background: #d68910;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>EMS Admin</h2>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="active">📊 Dashboard</a>
            <a href="departments.php">🏢 Departments</a>
            <a href="employees.php">👥 Employees</a>
            <a href="onboarding.php">➕ Onboarding</a>
            <a href="attendance.php">📋 Attendance</a>
            <a href="leave_approvals.php">🏖️ Leave Approvals</a>
            <a href="expense_approvals.php">💰 Expense Approvals</a>
            <a href="payroll.php">💳 Payroll</a>
            <a href="tasks.php">📝 Tasks</a>
            <a href="announcements.php">📢 Announcements</a>
            <a href="audit_log.php">📜 Audit Log</a>
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1 style="color: #2c3e50;">Admin Dashboard</h1>
            <div class="topbar-right">
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
                    <span><?php echo ucfirst($_SESSION['username']); ?></span>
                </div>
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="page-header">
            <h1>Welcome Back, Admin!</h1>
            <p>Here's an overview of your organization</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card success">
                <div class="stat-card-number"><?php echo $stats['total_employees']; ?></div>
                <div class="stat-card-label">Total Employees</div>
            </div>
            <div class="stat-card success">
                <div class="stat-card-number"><?php echo $stats['active_employees']; ?></div>
                <div class="stat-card-label">Active Employees</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-card-number"><?php echo $stats['pending_leaves']; ?></div>
                <div class="stat-card-label">Pending Leave Requests</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-card-number"><?php echo $stats['pending_expenses']; ?></div>
                <div class="stat-card-label">Pending Expenses</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number"><?php echo $stats['total_departments']; ?></div>
                <div class="stat-card-label">Total Departments</div>
            </div>
            <div class="stat-card danger">
                <div class="stat-card-number"><?php echo $stats['todays_absent']; ?></div>
                <div class="stat-card-label">Today's Absences</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
            <h2 style="margin-bottom: 20px; color: #2c3e50;">Quick Actions</h2>
            <div class="action-buttons">
                <a href="onboarding.php" class="btn btn-primary">➕ Add New Employee</a>
                <a href="leave_approvals.php" class="btn btn-warning">🏖️ Review Leaves</a>
                <a href="attendance.php" class="btn btn-primary">📋 Update Attendance</a>
                <a href="expense_approvals.php" class="btn btn-warning">💰 Review Expenses</a>
                <a href="payroll.php" class="btn btn-success">💳 Process Payroll</a>
                <a href="announcements.php" class="btn btn-primary">📢 Post Announcement</a>
            </div>
        </div>
    </div>
</body>
</html>