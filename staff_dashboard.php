<?php
require_once 'authentication.php';  // Ensure the user is authenticated and has the role of 'staff'

// You may need to handle form submissions or database queries here
// For example, retrieving open table bookings, processing new orders, etc.

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Ensure the path to CSS is correct -->
</head>
<body>
    <div class="dashboard-header">
        <h1>Staff Dashboard</h1>
        <nav>
            <ul>
                <li><a href="take_order.php">Take Order</a></li>
                <li><a href="manage_bookings.php">Manage Bookings</a></li>
                <li><a href="view_orders.php">View Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <section class="dashboard-main">
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! Use the navigation menu to manage orders and bookings.</p>
        <!-- Content specific to staff functions -->
        <!-- You could dynamically include PHP files based on a query parameter or form action to manage content here -->
    </section>
</body>
</html>
