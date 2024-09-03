<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("Unauthorized access.");
}

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Validate input
if (isset($_POST['ordernum'])) {
    $orderNum = mysqli_real_escape_string($conn, $_POST['ordernum']);
    
    // Check if there are items associated with this order
    $checkItemsQuery = "SELECT COUNT(*) AS item_count FROM orderdetails WHERE ordernum = '$orderNum'";
    $checkItemsResult = mysqli_query($conn, $checkItemsQuery);
    $checkItemsRow = mysqli_fetch_assoc($checkItemsResult);
    
    if ($checkItemsRow['item_count'] == 0) {
        // No items associated, proceed with deletion
        $query = "DELETE FROM orders WHERE ordernum = '$orderNum'";
        if (mysqli_query($conn, $query)) {
            // Redirect back to the orders page with a success message
            header("Location: orders.php?message=Order deleted successfully");
            exit();
        } else {
            // Redirect back to the orders page with an error message
            header("Location: orders.php?message=Error deleting order&error=true");
            exit();
        }
    } else {
        // There are items associated, do not delete
        header("Location: orders.php?message=Order cannot be deleted because it contains items&error=true");
        exit();
    }
} else {
    echo "Order number not specified.";
}

// Close database connection
mysqli_close($conn);
?>
