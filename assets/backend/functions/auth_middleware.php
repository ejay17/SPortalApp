<?php
// Auth Middleware - Include this at the top of any protected page
// Usage: require '../assets/backend/functions/auth_middleware.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Determine the correct redirect path based on current file location
    $current_script = $_SERVER['SCRIPT_NAME'];
    
    if (strpos($current_script, '/pages/') !== false) {
        header("Location: ../index.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

function requireRole($required_role_id) {
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != $required_role_id) {
        $current_script = $_SERVER['SCRIPT_NAME'];
        
        if (strpos($current_script, '/pages/') !== false) {
            header("Location: ../index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }
}
?>
