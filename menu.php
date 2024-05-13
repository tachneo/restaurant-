<?php
session_start();
require_once 'config.php';

$link = getDB();

// Ensure the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

// Fetch categories for filtering
$categoriesResult = mysqli_query($link, "SELECT category_id, name FROM Categories");
$categories = mysqli_fetch_all($categoriesResult, MYSQLI_ASSOC);

// Handle form submission for orders
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order'])) {
    $userId = $_SESSION['user_id'];  // Assuming user_id is stored in session
    $itemId = $_POST['item_id'];
    $tableId = $_POST['table_id'];
    $quantity = $_POST['quantity'];

    // Insert order into the database
    $orderSql = "INSERT INTO Orders (user_id, table_id, status) VALUES (?, ?, 'placed')";
    $stmt = mysqli_prepare($link, $orderSql);
    mysqli_stmt_bind_param($stmt, 'ii', $userId, $tableId);
    mysqli_stmt_execute($stmt);
    $orderId = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);

    // Insert order items
    $orderItemSql = "INSERT INTO OrderItems (order_id, item_id, quantity) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($link, $orderItemSql);
    mysqli_stmt_bind_param($stmt, 'iii', $orderId, $itemId, $quantity);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<script>alert('Order placed successfully!');</script>";
}

// Fetch all menu items
$menuItemsSql = "SELECT item_id, name, description, price, category_id FROM MenuItems";
$menuItemsResult = mysqli_query($link, $menuItemsSql);
$menuItems = mysqli_fetch_all($menuItemsResult, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#categoryFilter').change(function() {
                var categoryId = $(this).val();
                if (categoryId) {
                    $('.menu-item').hide();
                    $('.category-' + categoryId).show();
                } else {
                    $('.menu-item').show();
                }
            });
        });
    </script>
</head>
<body>
    <h1>Our Menu</h1>
    <select id="categoryFilter">
        <option value="">All Categories</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
        <?php endforeach; ?>
    </select>

    <div class="menu-grid">
        <?php foreach ($menuItems as $item): ?>
            <div class="menu-item category-<?php echo $item['category_id']; ?>">
                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                <p><?php echo htmlspecialchars($item['description']); ?></p>
                <p>$<?php echo number_format($item['price'], 2); ?></p>
                <form action="menu.php" method="post">
                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                    <label for="table_id">Table No:</label>
                    <input type="number" name="table_id" required>
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" min="1" value="1" required>
                    <button type="submit" name="order">Order</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
