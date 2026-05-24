<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireAdmin();

// Get all employees
$employees = [];
$result = $conn->query(
    "SELECT e.*, d.dept_name, dg.designation_name FROM employees e 
    JOIN departments d ON e.dept_id = d.id 
    JOIN designations dg ON e.designation_id = dg.id 
    ORDER BY e.created_at DESC"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees - EMS Admin</title>
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
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .table thead {
            background: #2c3e50;
            color: white;
        }
        
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
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
            <h2>EMS Admin</h2>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="departments.php">🏢 Departments</a>
            <a href="employees.php" class="active">👥 Employees</a>
            <a href="onboarding.php">➕ Onboarding</a>
            <a href="attendance.php">📋 Attendance</a>
            <a href="leave_approvals.php">🏖️ Leave Approvals</a>
            <a href="expense_approvals.php">💰 Expense Approvals</a>
            <a href="payroll.php">💳 Payroll</a>
            <a href="tasks.php">📝 Tasks</a>
            <a href="announcements.php">📢 Announcements</a>
            <a href="audit_log.php">📄 Audit Log</a>
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="topbar">
            <h1 style="color: #2c3e50;">Employee Directory</h1>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="page-header">
            <h1>Total Employees: <?php echo count($employees); ?></h1>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Joining Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><strong><?php echo $emp['emp_id']; ?></strong></td>
                        <td><?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?></td>
                        <td><?php echo $emp['email']; ?></td>
                        <td><?php echo $emp['dept_name']; ?></td>
                        <td><?php echo $emp['designation_name']; ?></td>
                        <td><span class="badge badge-success"><?php echo $emp['status']; ?></span></td>
                        <td><?php echo date('d-m-Y', strtotime($emp['joining_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>