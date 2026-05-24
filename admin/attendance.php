<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireAdmin();

// Get today's attendance
$result = $conn->query("SELECT a.*, e.first_name, e.last_name, e.emp_id FROM attendance a JOIN employees e ON a.emp_id = e.id WHERE a.attendance_date = CURDATE() ORDER BY a.created_at DESC");
$today_attendance = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $today_attendance[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - EMS Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
        .sidebar { width: 250px; background: #2c3e50; height: 100vh; position: fixed; left: 0; top: 0; overflow-y: auto; color: white; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid #34495e; text-align: center; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: block; padding: 15px 20px; color: #ecf0f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #34495e; border-left-color: #3498db; }
        .main-content { margin-left: 250px; padding: 20px; }
        .topbar { background: white; padding: 15px 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .table thead { background: #2c3e50; color: white; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .table tbody tr:hover { background: #f8f9fa; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-present { background: #d4edda; color: #155724; }
        .badge-absent { background: #f8d7da; color: #721c24; }
        .logout-btn { padding: 8px 15px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h2>EMS Admin</h2></div>
        <div class="sidebar-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="departments.php">Departments</a>
            <a href="employees.php">Employees</a>
            <a href="onboarding.php">Onboarding</a>
            <a href="attendance.php" class="active">Attendance</a>
            <a href="leave_approvals.php">Leave Approvals</a>
            <a href="expense_approvals.php">Expense Approvals</a>
            <a href="payroll.php">Payroll</a>
            <a href="tasks.php">Tasks</a>
            <a href="announcements.php">Announcements</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h1 style="color: #2c3e50;">Attendance Management</h1>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($today_attendance as $att): ?>
                    <tr>
                        <td><?php echo $att['emp_id']; ?></td>
                        <td><?php echo $att['first_name'] . ' ' . $att['last_name']; ?></td>
                        <td><?php echo $att['check_in_time'] ? date('h:i A', strtotime($att['check_in_time'])) : '-'; ?></td>
                        <td><?php echo $att['check_out_time'] ? date('h:i A', strtotime($att['check_out_time'])) : '-'; ?></td>
                        <td><span class="badge badge-<?php echo strtolower($att['status']); ?>"><?php echo $att['status']; ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>