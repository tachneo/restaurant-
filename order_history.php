<?php
session_start();
require_once 'config.php';

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

$link = getDB();

// Prepare SQL based on user role
if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff') {
    $sql = "SELECT Orders.order_id, Orders.order_time, Orders.status, Users.username FROM Orders JOIN Users ON Orders.user_id = Users.user_id";
} else {
    $sql = "SELECT Orders.order_id, Orders.order_time, Orders.status FROM Orders WHERE user_id = ?";
}

if ($_SESSION['role'] == 'customer') {
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    }
} else {
    $stmt = mysqli_prepare($link, $sql);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Order History</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Time</th>
                <th>Status</th>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff') { echo "<th>User</th>"; } ?>
            </tr>
        </thead>
        <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['order_time']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff') {
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No orders found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</body>
</html>
<?php
mysqli_stmt_close($stmt);
mysqli_close($link);
?>
