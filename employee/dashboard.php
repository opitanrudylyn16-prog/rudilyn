<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireEmployee();

// Get employee info
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT e.id, e.first_name, e.last_name, e.emp_id, e.dept_id, e.designation_id FROM employees e JOIN users u ON e.user_id = u.id WHERE u.id = $user_id");
$employee = $result->fetch_assoc();
$emp_id = $employee['id'];

// Get today's attendance
$result = $conn->query("SELECT * FROM attendance WHERE emp_id = $emp_id AND attendance_date = CURDATE()");
$today_attendance = $result->fetch_assoc();

// Get pending leave requests
$result = $conn->query("SELECT COUNT(*) as count FROM leave_requests WHERE emp_id = $emp_id AND status = 'Pending'");
$pending_leaves = $result->fetch_assoc()['count'];

// Get pending expenses
$result = $conn->query("SELECT COUNT(*) as count FROM expense_requests WHERE emp_id = $emp_id AND status = 'Pending'");
$pending_expenses = $result->fetch_assoc()['count'];

// Get assigned tasks
$result = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE assigned_to = $emp_id AND status != 'Completed'");
$active_tasks = $result->fetch_assoc()['count'];

// Get current month attendance percentage
$current_month = date('m');
$current_year = date('Y');
$attendance_percentage = getAttendancePercentage($conn, $emp_id, $current_month, $current_year);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - EMS</title>
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
        
        .attendance-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        
        .attendance-status {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }
        
        .attendance-button {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-checkin {
            background: #27ae60;
            color: white;
        }
        
        .btn-checkin:hover {
            background: #229954;
        }
        
        .btn-checkout {
            background: #e74c3c;
            color: white;
        }
        
        .btn-checkout:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>EMS Employee</h2>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="active">📊 Dashboard</a>
            <a href="profile.php">👤 My Profile</a>
            <a href="timesheet.php">📋 Timesheet</a>
            <a href="leave_requests.php">🏖️ Leave Requests</a>
            <a href="expense_requests.php">💰 Expenses</a>
            <a href="my_tasks.php">📝 My Tasks</a>
            <a href="announcements.php">📢 Announcements</a>
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1 style="color: #2c3e50;">Employee Dashboard</h1>
            <div class="topbar-right">
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($employee['first_name'], 0, 1)); ?></div>
                    <span><?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></span>
                </div>
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="page-header">
            <h1>Welcome, <?php echo $employee['first_name']; ?>!</h1>
            <p>Employee ID: <?php echo $employee['emp_id']; ?></p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card success">
                <div class="stat-card-number"><?php echo round($attendance_percentage, 1); ?>%</div>
                <div class="stat-card-label">Attendance (This Month)</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number"><?php echo $active_tasks; ?></div>
                <div class="stat-card-label">Active Tasks</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-card-number"><?php echo $pending_leaves; ?></div>
                <div class="stat-card-label">Pending Leave Requests</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-card-number"><?php echo $pending_expenses; ?></div>
                <div class="stat-card-label">Pending Expenses</div>
            </div>
        </div>
        
        <!-- Attendance Card -->
        <div class="attendance-card">
            <h2 style="color: #2c3e50; margin-bottom: 15px;">⏱️ Clock In/Out</h2>
            <?php if (!$today_attendance): ?>
                <p style="color: #7f8c8d; margin-bottom: 15px;">You haven't clocked in today yet.</p>
            <?php else: ?>
                <p style="color: #7f8c8d; margin-bottom: 15px;">
                    Status: <strong><?php echo $today_attendance['status']; ?></strong>
                    <?php if ($today_attendance['check_in_time']): ?>
                        | Clock In: <strong><?php echo date('h:i A', strtotime($today_attendance['check_in_time'])); ?></strong>
                    <?php endif; ?>
                    <?php if ($today_attendance['check_out_time']): ?>
                        | Clock Out: <strong><?php echo date('h:i A', strtotime($today_attendance['check_out_time'])); ?></strong>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <div class="attendance-status">
                <button class="attendance-button btn-checkin" onclick="climbIn()">✓ Clock In</button>
                <button class="attendance-button btn-checkout" onclick="climbOut()">✕ Clock Out</button>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); margin-top: 30px;">
            <h2 style="margin-bottom: 20px; color: #2c3e50;">Quick Actions</h2>
            <div class="action-buttons">
                <a href="profile.php" class="btn btn-primary">👤 View Profile</a>
                <a href="timesheet.php" class="btn btn-primary">📋 My Timesheet</a>
                <a href="leave_requests.php" class="btn btn-warning">🏖️ Request Leave</a>
                <a href="expense_requests.php" class="btn btn-warning">💰 Submit Expense</a>
                <a href="my_tasks.php" class="btn btn-primary">📝 View Tasks</a>
                <a href="announcements.php" class="btn btn-primary">📢 Announcements</a>
            </div>
        </div>
    </div>
    
    <script>
        function climbIn() {
            // Implementation for clock in
            alert('Clock in functionality to be implemented');
        }
        
        function climbOut() {
            // Implementation for clock out
            alert('Clock out functionality to be implemented');
        }
    </script>
</body>
</html>