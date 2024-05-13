<?php
session_start();
require_once 'config.php';

$link = getDB();

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? 'No Subject';
    $message = $_POST['message'] ?? '';

    // Prepare an SQL statement to insert the data into the database
    $sql = "INSERT INTO Messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        echo "<p>Message sent successfully!</p>";
    } else {
        echo "<p>Error: Could not prepare statement.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Contact Us</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject">

        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>

        <button type="submit" name="submit">Send Message</button>
    </form>
</body>
</html>
