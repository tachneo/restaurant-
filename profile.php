<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

// Get user information
$sql = "SELECT username, email FROM Users WHERE user_id = ?";
if ($stmt = getDB()->prepare($sql)) {
    $stmt->bind_param("i", $_SESSION["user_id"]);
    if ($stmt->execute()) {
        $stmt->bind_result($username, $email);
        if (!$stmt->fetch()) {
            echo "Error fetching user data.";
            exit;
        }
    }
    $stmt->close();
}
getDB()->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-header">
        <h1>Your Profile Details</h1>
    </div>
    <div>
        <p><b>Username:</b> <?php echo $username; ?></p>
        <p><b>Email:</b> <?php echo $email; ?></p>
        <p><a href="dashboard.php">Back to dashboard</a></p>
    </div>
</body>
</html>
