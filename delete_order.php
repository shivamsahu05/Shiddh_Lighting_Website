<?php
session_start();
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shiddh";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the order number from query parameter
$ordernum = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($ordernum > 0) {
    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete related data from ordertocmpdetails table
        $deleteDetailsQuery = "DELETE FROM ordertocmpdetails WHERE ordernum = ?";
        $stmt = $conn->prepare($deleteDetailsQuery);
        $stmt->bind_param("i", $ordernum);
        $stmt->execute();
        $stmt->close();

        // Delete data from orderbycompany table
        $deleteOrderQuery = "DELETE FROM orderbycompany WHERE ordernum = ?";
        $stmt = $conn->prepare($deleteOrderQuery);
        $stmt->bind_param("i", $ordernum);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Redirect to the order list page with a success message
        header("Location: view_orders.php?message=Order+deleted+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback transaction in case of an error
        $conn->rollback();
        // Redirect to the order list page with an error message
        header("Location: view_orders.php?message=Error+deleting+order");
        exit();
    }
} else {
    // Redirect to the order list page with an invalid ID message
    header("Location: view_orders.php?message=Invalid+Order+ID");
    exit();
}

// Close connection
$conn->close();
?>
