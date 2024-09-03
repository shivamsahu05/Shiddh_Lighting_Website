<?php
// deleteorder.php

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "shiddh");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if ordernum is set and is not empty
if (isset($_POST['ordernum']) && !empty($_POST['ordernum'])) {
    $ordernum = mysqli_real_escape_string($conn, $_POST['ordernum']);

    // Prepare the DELETE statement
    $query = "DELETE FROM orders WHERE ordernum = '$ordernum'";

    if (mysqli_query($conn, $query)) {
        // Redirect back to the orders page with a success message
        header("Location: orderspage.php?message=Order deleted successfully");
    } else {
        // Redirect back to the orders page with an error message
        header("Location: orderspage.php?message=Error deleting order&error=true");
    }
} else {
    // Redirect back to the orders page with an error message if ordernum is not provided
    header("Location: orderspage.php?message=Invalid order number&error=true");
}

// Close the database connection
mysqli_close($conn);
?>
