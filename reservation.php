<?php
// Start the session
session_start();

// Include the database configuration file
require_once 'config.php';

// Define variables and initialize with empty values
$date = $time = $guests = $contact_name = $contact_email = "";
$date_err = $time_err = $guests_err = $contact_name_err = $contact_email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate date
    if(empty(trim($_POST["date"]))){
        $date_err = "Please enter a date.";
    } else{
        $date = trim($_POST["date"]);
    }

    // Validate time
    if(empty(trim($_POST["time"]))){
        $time_err = "Please enter a time.";
    } else{
        $time = trim($_POST["time"]);
    }

    // Validate number of guests
    if(empty(trim($_POST["guests"]))){
        $guests_err = "Please enter the number of guests.";
    } else{
        $guests = trim($_POST["guests"]);
    }

    // Validate contact name
    if(empty(trim($_POST["contact_name"]))){
        $contact_name_err = "Please enter a contact name.";
    } else{
        $contact_name = trim($_POST["contact_name"]);
    }

    // Validate contact email
    if(empty(trim($_POST["contact_email"]))){
        $contact_email_err = "Please enter a contact email.";
    } elseif (!filter_var(trim($_POST["contact_email"]), FILTER_VALIDATE_EMAIL)) {
        $contact_email_err = "Please enter a valid email address.";
    } else{
        $contact_email = trim($_POST["contact_email"]);
    }

    // Check input errors before inserting in database
    if(empty($date_err) && empty($time_err) && empty($guests_err) && empty($contact_name_err) && empty($contact_email_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO Reservations (date, time, guests, contact_name, contact_email) VALUES (?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssiss", $param_date, $param_time, $param_guests, $param_contact_name, $param_contact_email);
            
            // Set parameters
            $param_date = $date;
            $param_time = $time;
            $param_guests = $guests;
            $param_contact_name = $contact_name;
            $param_contact_email = $contact_email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to booking success page
                header("location: reservation_success.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make a Reservation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div>
        <h2>Book a Table</h2>
        <p>Please fill this form to make a reservation.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Date</label>
                <input type="date" name="date" value="<?php echo $date; ?>">
                <span><?php echo $date_err; ?></span>
            </div>    
            <div>
                <label>Time</label>
                <input type="time" name="time" value="<?php echo $time; ?>">
                <span><?php echo $time_err; ?></span>
            </div>
            <div>
                <label>Number of Guests</label>
                <input type="number" name="guests" value="<?php echo $guests; ?>">
                <span><?php echo $guests_err; ?></span>
            </div>
            <div>
                <label>Contact Name</label>
                <input type="text" name="contact_name" value="<?php echo $contact_name; ?>">
                <span><?php echo $contact_name_err; ?></span>
            </div>
            <div>
                <label>Contact Email</label>
                <input type="email" name="contact_email" value="<?php echo $contact_email; ?>">
                <span><?php echo $contact_email_err; ?></span>
            </div>
            <div>
                <button type="submit">Submit</button>
                <button type="reset">Reset</button>
            </div>
        </form>
    </div>    
</body>
</html>
