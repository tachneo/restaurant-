<?php
session_start();
// Include the database configuration file
require_once 'config.php';

// Check if the user is logged in and is admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard">
        <h1>Admin Dashboard</h1>
        <div class="dashboard-items">
            <a href="manage_users.php" class="dashboard-item">
                <img src="icons/user.png" alt="Manage Users" />
                <span>Manage Users</span>
            </a>
            <a href="manage_bookings.php" class="dashboard-item">
                <img src="icons/booking.png" alt="Manage Bookings" />
                <span>Manage Bookings</span>
            </a>
            <a href="manage_menu.php" class="dashboard-item">
                <img src="icons/menu.png" alt="Manage Menu" />
                <span>Manage Menu</span>
            </a>
            <a href="manage_tables.php" class="dashboard-item">
                <img src="icons/table.png" alt="Manage Tables" />
                <span>Manage Tables</span>
            </a>
            <a href="view_reports.php" class="dashboard-item">
                <img src="icons/report.png" alt="View Reports" />
                <span>View Reports</span>
            </a>
        </div>
    </div>
</body>
</html>
