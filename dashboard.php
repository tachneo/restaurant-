<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

// Variables for user data
$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($username); ?></b>. Welcome to your dashboard.</h1>
    </div>
    <p>
        <a href="profile.php">View Profile</a> |
        <a href="menu.php">View Menu</a> |
        <a href="orders.php">View Orders</a> |
        <a href="logout.php">Sign Out</a>
    </p>
</body>
</html>
