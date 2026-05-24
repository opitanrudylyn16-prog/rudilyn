<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireAdmin();

$error = '';
$success = '';
$departments = [];

// Handle Add Department
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_dept'])) {
    $dept_name = sanitizeInput($_POST['dept_name']);
    $description = sanitizeInput($_POST['description']);
    
    if (empty($dept_name)) {
        $error = 'Department name is required!';
    } else {
        $dept_name_escaped = $conn->real_escape_string($dept_name);
        $description_escaped = $conn->real_escape_string($description);
        
        $query = "INSERT INTO departments (dept_name, description) VALUES ('$dept_name_escaped', '$description_escaped')";
        if ($conn->query($query)) {
            $success = 'Department added successfully!';
        } else {
            $error = 'Error adding department: ' . $conn->error;
        }
    }
}

// Handle Update Department
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_dept'])) {
    $id = intval($_POST['dept_id']);
    $dept_name = sanitizeInput($_POST['dept_name']);
    $description = sanitizeInput($_POST['description']);
    
    if (empty($dept_name)) {
        $error = 'Department name is required!';
    } else {
        $dept_name_escaped = $conn->real_escape_string($dept_name);
        $description_escaped = $conn->real_escape_string($description);
        
        $query = "UPDATE departments SET dept_name = '$dept_name_escaped', description = '$description_escaped' WHERE id = $id";
        if ($conn->query($query)) {
            $success = 'Department updated successfully!';
        } else {
            $error = 'Error updating department: ' . $conn->error;
        }
    }
}

// Handle Delete Department
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $query = "DELETE FROM departments WHERE id = $id";
    if ($conn->query($query)) {
        $success = 'Department deleted successfully!';
    } else {
        $error = 'Error deleting department: ' . $conn->error;
    }
}

// Get all departments
$result = $conn->query("SELECT * FROM departments ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}

$edit_dept = null;
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $result = $conn->query("SELECT * FROM departments WHERE id = $id");
    $edit_dept = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments - EMS Admin</title>
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
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 5px;
        }
        
        .alert {
            padding: 12px;
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
        
        .form-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
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
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
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
        
        .action-buttons {
            display: flex;
            gap: 10px;
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
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>EMS Admin</h2>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="departments.php" class="active">🏢 Departments</a>
            <a href="employees.php">👥 Employees</a>
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
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1 style="color: #2c3e50;">Departments Management</h1>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Form Card -->
        <div class="form-card">
            <h2 style="margin-bottom: 20px; color: #2c3e50;"><?php echo $edit_dept ? 'Edit Department' : 'Add New Department'; ?></h2>
            <form method="POST" action="">
                <?php if ($edit_dept): ?>
                    <input type="hidden" name="dept_id" value="<?php echo $edit_dept['id']; ?>">
                    <input type="hidden" name="update_dept" value="1">
                <?php else: ?>
                    <input type="hidden" name="add_dept" value="1">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="dept_name">Department Name</label>
                    <input type="text" id="dept_name" name="dept_name" value="<?php echo $edit_dept['dept_name'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?php echo $edit_dept['description'] ?? ''; ?></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary"><?php echo $edit_dept ? '✏️ Update' : '➕ Add'; ?></button>
                    <?php if ($edit_dept): ?>
                        <a href="departments.php" class="btn btn-secondary" style="text-align: center;">❌ Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Departments Table -->
        <div style="background: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
            <h2 style="padding: 20px 20px 0; color: #2c3e50;">All Departments</h2>
            <table class="table" style="margin: 20px 0 0;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Department Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $dept): ?>
                        <tr>
                            <td><?php echo $dept['id']; ?></td>
                            <td><?php echo $dept['dept_name']; ?></td>
                            <td><?php echo substr($dept['description'], 0, 50) . '...'; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($dept['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_id=<?php echo $dept['id']; ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 12px;">✏️ Edit</a>
                                    <a href="?delete_id=<?php echo $dept['id']; ?>" class="btn btn-danger" style="font-size: 12px; padding: 8px 12px;" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>