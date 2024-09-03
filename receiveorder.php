<?php
// Start output buffering
ob_start();
session_start();
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle delete operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ordernum']) && !empty($_POST['ordernum'])) {
    $orderNum = mysqli_real_escape_string($conn, $_POST['ordernum']);
    
    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete associated items from orderdetails
        $deleteItemsQuery = "DELETE FROM orderdetails WHERE ordernum = '$orderNum'";
        if (!mysqli_query($conn, $deleteItemsQuery)) {
            throw new Exception("Error deleting items from orderdetails.");
        }

        // Delete the order from orders table
        $deleteOrderQuery = "DELETE FROM orders WHERE ordernum = '$orderNum'";
        if (!mysqli_query($conn, $deleteOrderQuery)) {
            throw new Exception("Error deleting order from orders.");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Redirect with success flag and order number
        header("Location: " . $_SERVER['PHP_SELF'] . "?refresh=1&deleted=1&ordernum=$orderNum");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction in case of error
        mysqli_rollback($conn);
        
        // Redirect with error flag and order number
        header("Location: " . $_SERVER['PHP_SELF'] . "?refresh=1&deleted=0&ordernum=$orderNum");
        exit();
    }
}

// Fetch orders from database ordered by Date in descending order
$query = "SELECT * FROM orders ORDER BY Date DESC";
$result = mysqli_query($conn, $query);

mysqli_close($conn);

// End output buffering and flush output
ob_end_flush();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Received Orders</title>
    <script>
         window.addEventListener('DOMContentLoaded', (event) => {
        const urlParams = new URLSearchParams(window.location.search);
        const deleted = urlParams.get('deleted');
        const ordernum = urlParams.get('ordernum');
        if (deleted === '1') {
            alert(`Order ${ordernum} deleted successfully`);
        } else if (deleted === '0') {
            alert(`Error deleting order ${ordernum}`);
        }
        
        // Clear the query parameters after showing the alert
        if (urlParams.has('refresh')) {
            // Replace the URL without query parameters
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
    </script>
    <style>
        /* Your existing styles here */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 100%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        header img {
            width: 150px;
            height: auto;
        }
        h1 {
            color: #333;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 24px;
            text-transform: uppercase;
        }
        .orders {
            overflow: hidden;
            position: relative;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            position: relative;
        }
        thead {
            background-color: #007bff;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            text-transform: uppercase;
            font-size: 14px;
        }
        td {
            background-color: #fff;
            color: #333;
            border-right: 3px solid transparent;
            vertical-align: middle;
        }
        .action-btn {
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            font-size: 14px;
            margin-right: 5px;
        }
        .action-btn:hover {
            opacity: 0.8;
        }
        .details-btn {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        .update-btn {
            background-color: #28a745;
            color: white;
            border: 1px solid #28a745;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: 1px solid #dc3545;
        }
        tbody tr {
            transition: background-color 0.3s ease;
        }
        tbody tr:hover {
            background-color: #f0f0f0;
        }
        @media (max-width: 1200px) {
            .container {
                padding: 10px;
            }
        }
        @media (max-width: 992px) {
            header img {
                width: 120px;
            }
        }
        @media (max-width: 768px) {
            header img {
                width: 100px;
            }
            h1 {
                font-size: 20px;
            }
            th, td {
                padding: 10px;
            }
            .action-btn {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
        .table-wrapper {
            max-height: 500px;
            overflow-y: auto;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #eaf1e0;
            color: #333;
            text-align: center;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('deleted') === '1') {
                alert('Order deleted successfully');
            } else if (urlParams.get('deleted') === '0') {
                alert('Error deleting order');
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <header>
            <img src="images/logob21.png" alt="Logo">
            <h1>Received Orders</h1>
        </header>

        <main>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Shop Name</th>
                            <th>Cust. Name</th>
                            <th>Mobile</th>
                            <th>Address</th>
                            <th>Date</th>
                            <th>Ord Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reconnect to the database
                        $conn = mysqli_connect("localhost", "root", "", "shiddh");

                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        // Fetch orders from database ordered by Date in descending order
                        $query = "SELECT * FROM orders ORDER BY Date DESC";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            $serial = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $serial++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['ShopName']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['CustomerName']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Mobile_Num']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Address']) . "</td>";

                                $dateTime = date('d-m-Y H:i:s', strtotime($row['Date']));
                                echo "<td>" . htmlspecialchars($dateTime) . "</td>";

                                echo "<td>" . htmlspecialchars($row['ordernum']) . "</td>";
                                echo "<td>";

                                // Details Button
                                echo "<form action='receiveorderdetails.php' method='get' style='display:inline;'>";
                                echo "<input type='hidden' name='ordernum' value='" . htmlspecialchars($row['ordernum']) . "'>";
                                echo "<button type='submit' class='action-btn details-btn'>Details</button>";
                                echo "</form>";

                                // Update Button
                                echo "<form action='receiveorderadmin.php' method='get' style='display:inline;'>";
                                echo "<input type='hidden' name='ordernum' value='" . htmlspecialchars($row['ordernum']) . "'>";
                                echo "<button type='submit' class='action-btn update-btn'>Update</button>";
                                echo "</form>";

                                // Delete Button
                                echo "<form action='' method='post' style='display:inline;'>";
                                echo "<input type='hidden' name='ordernum' value='" . htmlspecialchars($row['ordernum']) . "'>";
                                echo "<button type='submit' class='action-btn delete-btn'>Delete</button>";
                                echo "</form>";

                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No orders found.</td></tr>";
                        }

                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
