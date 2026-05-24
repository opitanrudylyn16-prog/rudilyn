<?php
// Helper Functions

require_once 'session_handler.php';

// Hash Password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify Password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Generate Unique Employee ID
function generateEmployeeID($conn, $dept_id) {
    $year = date('Y');
    $dept = getLastDeptCode($conn, $dept_id);
    $count = getEmpCountInDept($conn, $dept_id);
    return 'EMP' . $year . $dept . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
}

// Get Department Code
function getLastDeptCode($conn, $dept_id) {
    $result = $conn->query("SELECT dept_name FROM departments WHERE id = $dept_id");
    $row = $result->fetch_assoc();
    return substr(strtoupper($row['dept_name']), 0, 2);
}

// Get Employee Count in Department
function getEmpCountInDept($conn, $dept_id) {
    $result = $conn->query("SELECT COUNT(*) as count FROM employees WHERE dept_id = $dept_id");
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Generate Random Password
function generateRandomPassword($length = 12) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Send Email
function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= 'From: <noreply@employeemgmt.com>' . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Calculate Leave Balance
function getLeaveBalance($conn, $emp_id, $leave_type_id, $year) {
    $result = $conn->query(
        "SELECT (allocated_days - used_days) as balance FROM leave_balance 
        WHERE emp_id = $emp_id AND leave_type_id = $leave_type_id AND year = $year"
    );
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['balance'];
    }
    return 0;
}

// Calculate Attendance Percentage
function getAttendancePercentage($conn, $emp_id, $month, $year) {
    $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    $result = $conn->query(
        "SELECT COUNT(*) as present_days FROM attendance 
        WHERE emp_id = $emp_id AND MONTH(attendance_date) = $month AND YEAR(attendance_date) = $year 
        AND status IN ('Present', 'Half-Day', 'WFH')"
    );
    
    $row = $result->fetch_assoc();
    $present = $row['present_days'];
    
    return $total_days > 0 ? round(($present / $total_days) * 100, 2) : 0;
}

// Calculate Net Salary
function calculateNetSalary($basic, $overtime, $deductions, $penalties, $bonuses) {
    return $basic + $overtime + $bonuses - $deductions - $penalties;
}

// Check Leave Balance Before Approval
function hasLeaveBalance($conn, $emp_id, $leave_type_id, $num_days, $year) {
    $balance = getLeaveBalance($conn, $emp_id, $leave_type_id, $year);
    return $balance >= $num_days;
}

// Log Audit
function logAudit($conn, $user_id, $action, $entity_type, $entity_id, $old_value = null, $new_value = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $old_val = $old_value ? $conn->real_escape_string(json_encode($old_value)) : null;
    $new_val = $new_value ? $conn->real_escape_string(json_encode($new_value)) : null;
    
    $query = "INSERT INTO audit_log (user_id, action, entity_type, entity_id, old_value, new_value, ip_address) 
             VALUES ($user_id, '$action', '$entity_type', $entity_id, '$old_val', '$new_val', '$ip')";
    
    return $conn->query($query);
}

// Format Date
function formatDate($date, $format = 'Y-m-d') {
    return date($format, strtotime($date));
}

// Get Department Name by ID
function getDepartmentName($conn, $dept_id) {
    $result = $conn->query("SELECT dept_name FROM departments WHERE id = $dept_id");
    $row = $result->fetch_assoc();
    return $row['dept_name'] ?? 'N/A';
}

// Get Designation Name by ID
function getDesignationName($conn, $desig_id) {
    $result = $conn->query("SELECT designation_name FROM designations WHERE id = $desig_id");
    $row = $result->fetch_assoc();
    return $row['designation_name'] ?? 'N/A';
}

// Get Employee Name by ID
function getEmployeeName($conn, $emp_id) {
    $result = $conn->query("SELECT CONCAT(first_name, ' ', last_name) as name FROM employees WHERE id = $emp_id");
    $row = $result->fetch_assoc();
    return $row['name'] ?? 'N/A';
}

// Sanitize Input
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

?>