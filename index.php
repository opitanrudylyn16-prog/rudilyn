<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    if (hasRole('admin')) {
        header('Location: ' . BASE_URL . 'admin/dashboard.php');
    } else {
        header('Location: ' . BASE_URL . 'employee/dashboard.php');
    }
    exit;
} else {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
?>