<?php
session_start();
require_once 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

$link = getDB();

// Handle delete operation
if(isset($_GET["delete"]) && !empty($_GET["delete"])){
    $delete_sql = "DELETE FROM Bookings WHERE booking_id = ?";
    
    if($delete_stmt = mysqli_prepare($link, $delete_sql)){
        mysqli_stmt_bind_param($delete_stmt, "i", $param_id);
        $param_id = $_GET["delete"];
        
        if(mysqli_stmt_execute($delete_stmt)){
            echo "<p>Booking successfully deleted.</p>";
        } else{
            echo "<p>Oops! Something went wrong. Please try again later.</p>";
        }
        mysqli_stmt_close($delete_stmt);
    }
}

// Handle form submission for adding/updating bookings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "INSERT INTO Bookings (table_id, user_id, booking_time, status) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE table_id = ?, booking_time = ?, status = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "iissiis", $param_table_id, $param_user_id, $param_booking_time, $param_status, $param_table_id, $param_booking_time, $param_status);

        // Set parameters
        $param_table_id = $_POST['table_id'];
        $param_user_id = $_SESSION['user_id'];  // Assuming the booking is made by the logged-in user
        $param_booking_time = $_POST['booking_time'];
        $param_status = $_POST['status'];

        if (mysqli_stmt_execute($stmt)) {
            echo "<p>Booking successfully saved.</p>";
        } else {
            echo "<p>Error saving booking: " . mysqli_error($link) . "</p>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all bookings
$fetch_sql = "SELECT booking_id, table_id, user_id, booking_time, status FROM Bookings";
$result = mysqli_query($link, $fetch_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Manage Bookings</h1>

    <!-- Booking Form for adding new bookings -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label>Table ID</label>
        <input type="number" name="table_id" required>
        <label>Booking Time</label>
        <input type="datetime-local" name="booking_time" required>
        <label>Status</label>
        <select name="status">
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
            <option value="completed">Completed</option>
        </select>
        <input type="submit" value="Submit">
    </form>

    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Table ID</th>
                <th>User ID</th>
                <th>Booking Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['booking_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['table_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['booking_time']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td><a href='bookings.php?delete=" . $row['booking_id'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No bookings found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</body>
</html>

<?php
mysqli_close($link);
?>
