<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if InvoiceNum is provided
$invoiceNum = isset($_POST['InvoiceNum']) ? $_POST['InvoiceNum'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $bill_id = isset($_POST['bill_id']) ? $_POST['bill_id'] : '';
    $itemName = isset($_POST['itemName']) ? $_POST['itemName'] : '';
    $qty = isset($_POST['qty']) ? $_POST['qty'] : '';
    $Price = isset($_POST['Price']) ? $_POST['Price'] : '';
    $Amount = isset($_POST['Amount']) ? $_POST['Amount'] : '';

    if (isset($_POST['update'])) {
        // Prepare update query
        $query = "UPDATE billdetails SET itemName=?, qty=?, Price=?, Amount=? WHERE bill_id=?";
        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            die("Error preparing statement: " . mysqli_error($conn));
        }

        // We need to ensure that the parameters and types match
        mysqli_stmt_bind_param($stmt, "sdds", $itemName, $qty, $Price, $Amount);

        // Execute statement
        if (mysqli_stmt_execute($stmt)) {
            header("Location: your_list_page.php"); // Replace with actual list page
            exit();
        } else {
            $error_message = "Error updating item details: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }

    if (isset($_POST['delete'])) {
        // Prepare SQL statement to delete item
        $query = "DELETE FROM billdetails WHERE bill_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $bill_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Item deleted successfully.";
            header("Location: your_list_page.php"); // Replace with actual list page
            exit();
        } else {
            echo "Error deleting item: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}

// Fetch bill details for the specific InvoiceNum
$query = "SELECT * FROM billdetails WHERE InvoiceNum = ? AND qty > 0 ORDER BY bill_id";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Error preparing statement: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $invoiceNum);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    // Calculate the total amount
    $totalAmount = 0;

    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Bill Details</title>";
    echo "<style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .form-container {
            background-color: #f9f9f9;
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
    </style>";
    echo "<script>
        function calculateAmount() {
            var qty = parseFloat(document.getElementById('qty').value) || 0;
            var price = parseFloat(document.getElementById('Price').value) || 0;
            var amount = qty * price;
            document.getElementById('Amount').value = amount.toFixed(2);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            var qtyField = document.getElementById('qty');
            var priceField = document.getElementById('Price');
            
            qtyField.addEventListener('input', calculateAmount);
            priceField.addEventListener('input', calculateAmount);
        });
    </script>";
    echo "</head>";
    echo "<body>";

    echo "<div class='container'>";
    echo "<h2>Bill Details for Invoice Number: $invoiceNum</h2>";
    echo "<table>";
    echo "<tr><th>Bill ID</th><th>Item Name</th><th>Quantity</th><th>Price</th><th>Amount</th><th>Actions</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $totalAmount += $row['Amount']; // Calculate total amount
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['bill_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['itemName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['qty']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Amount']) . "</td>";
        echo "<td>";

        // Update form
        echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to update this item?\");'>";
        echo "<input type='hidden' name='bill_id' value='" . htmlspecialchars($row['bill_id']) . "'>";
        echo "<input type='hidden' name='InvoiceNum' value='" . htmlspecialchars($invoiceNum) . "'>";
        echo "<div class='form-container'>";
        echo "<h3>Update This Item</h3>";
        echo "<label for='itemName'>Item Name:</label>";
        echo "<input type='text' id='itemName' name='itemName' value='" . htmlspecialchars($row['itemName']) . "' required>";
        echo "<label for='qty'>Quantity:</label>";
        echo "<input type='number' id='qty' name='qty' value='" . htmlspecialchars($row['qty']) . "' required>";
        echo "<label for='Price'>Price:</label>";
        echo "<input type='number' step='0.01' id='Price' name='Price' value='" . htmlspecialchars($row['Price']) . "' required>";
        echo "<label for='Amount'>Amount:</label>";
        echo "<input type='text' id='Amount' name='Amount' value='" . htmlspecialchars($row['Amount']) . "' readonly>";
        echo "<input type='submit' name='update' value='Update'>";
        echo "<input type='submit' name='delete' value='Delete' class='delete-button'>";
        echo "</div>";
        echo "</form>";

        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Display total amount
    echo "<div class='total-container'>";
    echo "<label>Total Amount:</label>";
    echo "<input type='text' value='" . number_format($totalAmount, 2) . "' readonly>";
    echo "</div>";

    echo "</div>";
    echo "</body>";
    echo "</html>";
} else {
    echo "No details found for the specified invoice number.";
}

// Close connections
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
