<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

// Get orders from database
$orders = [];
$sql = "SELECT order_id, order_time, status FROM Orders WHERE user_id = ?";
if ($stmt = getDB()->prepare($sql)) {
    $stmt->bind_param("i", $_SESSION["user_id"]);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
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
    <title>Your Orders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-header">
        <h1>Your Order History</h1>
    </div>
    <?php foreach ($orders as $order): ?>
        <div class="order">
            <p>Order #<?php echo $order['order_id']; ?> - Placed on <?php echo $order['order_time']; ?> - Status: <?php echo $order['status']; ?></p>
        </div>
    <?php endforeach; ?>
    <p><a href="dashboard.php">Back to dashboard</a></p>
</body>
</html>
