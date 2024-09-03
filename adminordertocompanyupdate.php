<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shiddh";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order ID from session or URL
$ordernum = isset($_GET['id']) ? intval($_GET['id']) : (isset($_SESSION['orderId']) ? intval($_SESSION['orderId']) : 0);

// Fetch order details
$sql = "SELECT * FROM orderbycompany WHERE ordernum = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $ordernum);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();

if (!$order) {
    die("No order found with ID: " . htmlspecialchars($ordernum));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $itemName = isset($_POST['itemName']) ? $_POST['itemName'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $qty = isset($_POST['qty']) ? floatval($_POST['qty']) : 0;
    $Price = isset($_POST['Price']) ? floatval($_POST['Price']) : 0;
    $Amount = isset($_POST['Amount']) ? floatval($_POST['Amount']) : 0;

    if (isset($_POST['update'])) {
        $query = "UPDATE ordertocmpdetails SET itemname=?, description=?, qty=?, price=?, amount=? WHERE order_id=?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("ssdssi", $itemName, $description, $qty, $Price, $Amount, $order_id);
        if ($stmt->execute()) {
            updateTotals($conn, $ordernum);
            header("Location: adminordertocompanyupdate.php?id=$ordernum");
            exit();
        } else {
            echo "<div class='error'>Error updating item details: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }

    if (isset($_POST['delete'])) {
        $query = "SELECT amount FROM ordertocmpdetails WHERE order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $deleted_amount = isset($row['amount']) ? floatval($row['amount']) : 0;

        $query = "DELETE FROM ordertocmpdetails WHERE order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            updateTotals($conn, $ordernum, $deleted_amount);
            header("Location: adminordertocompanyupdate.php?id=$ordernum");
            exit();
        } else {
            echo "<div class='error'>Error deleting item: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }

    if (isset($_POST['add'])) {
        $query = "INSERT INTO ordertocmpdetails (ordernum, description, itemname, qty, price, amount) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("ssddsd", $ordernum, $description, $itemName, $qty, $Price, $Amount);
        if ($stmt->execute()) {
            updateTotals($conn, $ordernum);
            header("Location: adminordertocompanyupdate.php?id=$ordernum");
            exit();
        } else {
            echo "<div class='error'>Error adding new item: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

function updateTotals($conn, $ordernum, $deleted_amount = 0) {
    $query = "SELECT SUM(amount) AS totalAmount FROM ordertocmpdetails WHERE ordernum = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ordernum);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $sub_amt = isset($row['totalAmount']) ? floatval($row['totalAmount']) : 0;

    $query = "SELECT chrgeauto FROM orderbycompany WHERE ordernum = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ordernum);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $chrgeauto = isset($row['chrgeauto']) ? floatval($row['chrgeauto']) : 0;

    $totalamt = $sub_amt + $chrgeauto;
    $gst = $totalamt * 0.18;
    $totalWithGst = $totalamt + $gst;

    $query = "UPDATE orderbycompany SET sub_amount=?, totalamount=?, totalamtwithgst=? WHERE ordernum=?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ddds", $sub_amt, $totalamt, $totalWithGst, $ordernum);
    $stmt->execute();
    $stmt->close();
}

$query = "SELECT * FROM ordertocmpdetails WHERE ordernum = ? AND qty > 0 ORDER BY order_id";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $ordernum);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $totalAmount = 0;
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta http-equiv='refresh' content='180'>"; // Auto-refresh every 2 minutes
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Bill Details</title>";
    echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css'>";
    echo "<style>/* Custom CSS */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0f7fa; /* Light blue background for better contrast */
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            background-color: #4CAF50; /* Green background */
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            color: white;
            text-transform: uppercase;
        }
        .table-container {
            max-height: 500px; /* Adjust height as needed */
            overflow-y: auto;
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #388E3C; /* Darker green for header */
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h3 {
            margin-bottom: 15px;
            color: #333;
            text-transform: uppercase;
            text-align: center;
        }
        .form-container label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .form-container input[type='text'], 
        .form-container input[type='number'] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container input[type='submit'] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container input[type='submit']:hover {
            background-color: #45a049;
        }
        .delete-button {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
        }
        .delete-button:hover {
            background-color: #e53935;
        }
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .total-container {
            margin-top: 20px;
            text-align: center;
        }
        .total-container label {
            font-weight: bold;
            font-size: 18px;
        }
        .total-container input[type='text'] {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            border: none;
            background: #e0e0e0;
            padding: 10px;
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
        }
        @media (max-width: 768px) {
            th, td {
                padding: 8px;
                font-size: 14px;
            }
            .form-container input[type='text'],
            .form-container input[type='number'],
            .form-container input[type='submit'],
            .delete-button {
                width: 100%;
                box-sizing: border-box;
            }
        }
        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }
            .total-container input[type='text'] {
                font-size: 16px;
            }
        }</style>";
        echo "<script>
        function calculateAmount(input) {
            var form = input.closest('form');
            var qty = parseFloat(form.querySelector('input[name=\"qty\"]').value) || 0;
            var price = parseFloat(form.querySelector('input[name=\"Price\"]').value) || 0;
            var amount = qty * price;
            form.querySelector('input[name=\"Amount\"]').value = amount.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function() {
            var qtyFields = document.querySelectorAll('input[name=\"qty\"]');
            var priceFields = document.querySelectorAll('input[name=\"Price\"]');
            
            qtyFields.forEach(function(field) {
                field.addEventListener('input', function() { calculateAmount(this); });
            });

            priceFields.forEach(function(field) {
                field.addEventListener('input', function() { calculateAmount(this); });
            });
        });
    </script>";
    echo "</head>";
    echo "<body>";
    echo "<div class='container'>";
    echo "<h2>Order Details for Order Number: $ordernum</h2>";
    echo "<div class='table-container'>";
    echo "<table>";
    echo "<thead><tr><th>#</th><th>ID</th><th>Item Name</th><th>Quantity</th><th>Price</th><th>Amount</th><th>Actions</th></tr></thead>";
    echo "<tbody>";

    $serial_number = 1;
    while ($row = $result->fetch_assoc()) {
        $totalAmount += $row['amount'];
        echo "<tr>";
        echo "<td>" . $serial_number++ . "</td>";
        echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['itemname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['qty']) . "</td>";
        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
        echo "<td>";
        echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to update this item?\");'>";
        echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>";
        echo "<div class='form-container'>";
        echo "<h3>Update This Item</h3>";
        echo "<label for='itemName'>Item Name:</label>";
        echo "<input type='text' id='itemName' name='itemName' value='" . htmlspecialchars($row['itemname']) . "' required>";
        echo "<label for='description'>Description:</label>"; 
        echo "<input type='text' id='description' name='description' value='" . htmlspecialchars($row['description']) . "'>";
        echo "<label for='qty'>Quantity: <span style='color: red;'>*</span></label>";
        echo "<input type='number' id='qty' name='qty' value='" . htmlspecialchars($row['qty']) . "' required>";
        echo "<label for='Price'>Price: <span style='color: red;'>*</span></label>";
        echo "<input type='number' step='0.01' id='Price' name='Price' value='" . htmlspecialchars($row['price']) . "' required>";
        echo "<label for='Amount'>Amount: <span style='color: red;'>*</span></label>";
        echo "<input type='text' id='Amount' name='Amount' value='" . htmlspecialchars($row['amount']) . "' readonly>";
        echo "<input type='submit' name='update' value='Update'>";
        echo "<input type='submit' name='delete' value='Delete' class='delete-button'>";
        echo "</div>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    echo "<div class='total-container'>";
    echo "<label>Total Amount Without GST:</label>";
    echo "<input type='text' value='" . number_format($totalAmount, 2) . "' readonly>";
    echo "</div>";

    echo "<div class='form-container'>";
    echo "<h3>Add New Item</h3>";
    echo "<form method='post' action=''>";
    echo "<label for='itemName'>Item Name: <span style='color: red;'>*</span></label>";
    echo "<input type='text' id='itemName' name='itemName' required>";
    echo "<label for='description'>Description:</label>"; 
    echo "<input type='text' id='description' name='description'>";
    echo "<label for='qty'>Quantity: <span style='color: red;'>*</span></label>";
    echo "<input type='number' id='qty' name='qty' required>";
    echo "<label for='Price'>Price: <span style='color: red;'>*</span></label>";
    echo "<input type='number' step='0.01' id='Price' name='Price' required>";
    echo "<label for='Amount'>Amount: <span style='color: red;'>*</span></label>";
    echo "<input type='text' id='Amount' name='Amount' readonly>";
    echo "<input type='submit' name='add' value='Add Item'>";
    echo "</form>";
    echo "</div>";

    echo "</div>";
    echo "</body>";
    echo "</html>";
} else {
    echo "No details found for the specified Bill number.";
}

$stmt->close();
$conn->close();
?>
