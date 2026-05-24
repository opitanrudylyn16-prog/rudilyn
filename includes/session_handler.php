<?php
// Session Handler

session_start();

define('SESSION_TIMEOUT', 3600); // 1 hour

// Check if session exists and is not expired
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        header('Location: ' . BASE_URL . 'login.php?msg=Session expired. Please login again.');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Login function
function login($conn, $username, $password) {
    $username = $conn->real_escape_string($username);
    $result = $conn->query("SELECT * FROM users WHERE username = '$username' AND is_active = 1");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['last_activity'] = time();
            
            // Update last login
            $conn->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");
            return true;
        }
    }
    return false;
}

// Logout function
function logout() {
    session_destroy();
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!hasRole('admin')) {
        header('HTTP/1.1 403 Forbidden');
        exit('Access Denied');
    }
}

// Redirect if not employee
function requireEmployee() {
    requireLogin();
    if (!hasRole('employee')) {
        header('HTTP/1.1 403 Forbidden');
        exit('Access Denied');
    }
}

?>