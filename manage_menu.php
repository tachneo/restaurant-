<?php
session_start();
require_once 'config.php';

$link = getDB();

// Check if the user is logged in and is an admin or staff
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('location: login.php');
    exit;
}

// Handling POST request for adding/updating menu items
function handlePostRequest($link) {
    if (isset($_POST['name'], $_POST['description'], $_POST['price'], $_POST['category_id'])) {
        $itemId = isset($_POST['item_id']) ? $_POST['item_id'] : NULL;

        $sql = $itemId ? "UPDATE MenuItems SET name = ?, description = ?, price = ?, category_id = ? WHERE item_id = ?"
                       : "INSERT INTO MenuItems (name, description, price, category_id) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            $itemId ? mysqli_stmt_bind_param($stmt, 'ssdii', $_POST['name'], $_POST['description'], $_POST['price'], $_POST['category_id'], $itemId)
                    : mysqli_stmt_bind_param($stmt, 'ssdi', $_POST['name'], $_POST['description'], $_POST['price'], $_POST['category_id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

// Handling POST request for adding categories
function handleCategoryPost($link) {
    if (isset($_POST['category_name'])) {
        $sql = "INSERT INTO Categories (name) VALUES (?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, 's', $_POST['category_name']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

// Handling delete requests for items and categories
function handleDeleteRequest($link, $type, $id) {
    $sql = ($type === 'item') ? "DELETE FROM MenuItems WHERE item_id = ?" : "DELETE FROM Categories WHERE category_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Fetch categories to use in the dropdown and tables
$categories = [];
$categoriesResult = mysqli_query($link, "SELECT category_id, name FROM Categories");
if ($categoriesResult) {
    while ($category = mysqli_fetch_assoc($categoriesResult)) {
        $categories[$category['category_id']] = $category['name'];
    }
    mysqli_free_result($categoriesResult);
}

// Fetch all menu items for display
$menuItemsResult = mysqli_query($link, "SELECT item_id, name, description, price, category_id FROM MenuItems");

// Process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePostRequest($link);
    handleCategoryPost($link);
}

// Process GET requests for deletions
if (isset($_GET['delete_item'])) {
    handleDeleteRequest($link, 'item', $_GET['delete_item']);
}
if (isset($_GET['delete_category'])) {
    handleDeleteRequest($link, 'category', $_GET['delete_category']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Manage Menu Items and Categories</h1>

    <!-- Category Management Form -->
    <h2>Add New Category</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="category_name">Category Name:</label>
        <input type="text" name="category_name" required>
        <input type="submit" value="Add Category">
    </form>

    <!-- Display Categories -->
    <h2>Current Categories</h2>
    <table>
        <tr>
            <th>Category ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($categories as $id => $name): ?>
            <tr>
                <td><?php echo htmlspecialchars($id); ?></td>
                <td><?php echo htmlspecialchars($name); ?></td>
                <td><a href="?delete_category=<?php echo $id; ?>">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
 
    <!-- Form to add or update menu items -->
    <h2>Add or Update Menu Items</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="item_id" value="<?php echo isset($_GET['edit']) ? $_GET['edit'] : ''; ?>">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <label for="description">Description:</label>
        <textarea name="description" required></textarea>
        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" required>
        <label for="category_id">Category:</label>
        <select name="category_id" required>
            <?php foreach ($categories as $id => $name): ?>
                <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Save Item</button>
    </form>

    <!-- Display Menu Items -->
    <h2>Menu Items</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($menuItemsResult): ?>
            <?php while ($item = mysqli_fetch_assoc($menuItemsResult)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($categories[$item['category_id']]); ?></td>
                    <td>
                        <a href="?edit=<?php echo $item['item_id']; ?>">Edit</a> |
                        <a href="?delete_item=<?php echo $item['item_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No menu items found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <?php mysqli_close($link); ?>
</body>
</html>
