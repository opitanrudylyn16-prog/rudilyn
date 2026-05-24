<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $dept_id = intval($_POST['dept_id']);
    $designation_id = intval($_POST['designation_id']);
    $joining_date = sanitizeInput($_POST['joining_date']);
    $phone = sanitizeInput($_POST['phone']);
    $salary = floatval($_POST['salary']);
    
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'First name, last name, and email are required!';
    } else {
        // Generate employee ID
        $emp_id = generateEmployeeID($conn, $dept_id);
        
        // Generate random password
        $password = generateRandomPassword();
        $password_hash = hashPassword($password);
        
        // Create username
        $username = strtolower(substr($first_name, 0, 1) . $last_name . rand(100, 999));
        
        // Escape strings
        $first_name_esc = $conn->real_escape_string($first_name);
        $last_name_esc = $conn->real_escape_string($last_name);
        $email_esc = $conn->real_escape_string($email);
        $phone_esc = $conn->real_escape_string($phone);
        
        // Insert user
        $user_query = "INSERT INTO users (username, email, password_hash, role, is_active) 
                      VALUES ('$username', '$email_esc', '$password_hash', 'employee', 1)";
        
        if ($conn->query($user_query)) {
            $user_id = $conn->insert_id;
            
            // Insert employee
            $emp_query = "INSERT INTO employees (user_id, emp_id, first_name, last_name, email, phone, dept_id, designation_id, joining_date, salary, status) 
                         VALUES ($user_id, '$emp_id', '$first_name_esc', '$last_name_esc', '$email_esc', '$phone_esc', $dept_id, $designation_id, '$joining_date', $salary, 'Active')";
            
            if ($conn->query($emp_query)) {
                $success = "Employee onboarded successfully!<br><strong>Employee ID:</strong> $emp_id<br><strong>Username:</strong> $username<br><strong>Password:</strong> $password";
            } else {
                $error = 'Error creating employee record: ' . $conn->error;
            }
        } else {
            $error = 'Error creating user account: ' . $conn->error;
        }
    }
}

// Get departments
$departments = [];
$result = $conn->query("SELECT * FROM departments ORDER BY dept_name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Get designations
$designations = [];
$result = $conn->query("SELECT * FROM designations ORDER BY designation_name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $designations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding - EMS Admin</title>
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
        
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 800px;
        }
        
        .form-card h2 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
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
            <a href="employees.php">👥 Employees</a>
            <a href="onboarding.php" class="active">➕ Onboarding</a>
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
            <h1 style="color: #2c3e50;">Employee Onboarding</h1>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-card">
            <h2>➕ Add New Employee</h2>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="dept_id">Department *</label>
                        <select id="dept_id" name="dept_id" required>
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo $dept['dept_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="designation_id">Designation *</label>
                        <select id="designation_id" name="designation_id" required>
                            <option value="">-- Select Designation --</option>
                            <?php foreach ($designations as $desig): ?>
                                <option value="<?php echo $desig['id']; ?>"><?php echo $desig['designation_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="joining_date">Joining Date *</label>
                        <input type="date" id="joining_date" name="joining_date" required>
                    </div>
                    <div class="form-group">
                        <label for="salary">Salary</label>
                        <input type="number" id="salary" name="salary" step="0.01">
                    </div>
                </div>
                
                <button type="submit" class="btn">➕ Onboard Employee</button>
            </form>
        </div>
    </div>
</body>
</html>