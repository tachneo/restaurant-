<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'customer') {
    header('location: login.php');
    exit;
}

// Could include a file that handles database connection
require_once 'config.php';

$username = $_SESSION['username']; // Assuming username is stored in session

echo "Debug: User role is " . $_SESSION['role'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Ensure the stylesheet path is correct -->
</head>
<body>
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
        <nav>
            <ul>
                <li><a href="menu.php">View Menu</a></li>
                <li><a href="bookings.php">Manage Bookings</a></li>
                <li><a href="order_history.php">Order History</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <section class="dashboard-main">
        <!-- Dynamic content can be loaded here based on the page actions -->
    </section>
</body>
</html>
