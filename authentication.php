<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

// Redirect user to the appropriate dashboard based on role
switch ($_SESSION['role']) {
    case 'admin':
        header('location: admin_dashboard.php');
        exit;
    case 'staff':
        header('location: staff_dashboard.php');
        exit;
    case 'customer':
        header('location: customer_dashboard.php');
        exit;
    default:
        // Log out the user if role is not valid or defined
        $_SESSION = array();
        session_destroy();
        header('location: login.php');
        exit;
}
?>
