<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the InvoiceNum parameter is set via POST
if (isset($_POST['InvoiceNum'])) {
    $invoiceNum = mysqli_real_escape_string($conn, $_POST['InvoiceNum']);
    
    // Prepare and execute the statement
    $query = "SELECT * FROM billdetails WHERE InvoiceNum = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $invoiceNum);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result) {
        // Start HTML output
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bill Details</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #f0f0f0;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
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
                @media only screen and (max-width: 600px) {
                    table {
                        font-size: 14px;
                    }
                    th, td {
                        padding: 8px;
                    }
                }
                @media only screen and (max-width: 400px) {
                    table {
                        font-size: 12px;
                    }
                    th, td {
                        padding: 6px;
                    }
                }
                .total {
                    text-align: right;
                    margin-top: 20px;
                    font-size: 18px;
                    font-weight: bold;
                    color: red;
                }
            </style>
        </head>
        <body>
        <div class="container">
            <h2>Bill Details for Invoice Number: <?php echo htmlspecialchars($invoiceNum); ?></h2>
            <table>
                <tr><th>S.N.</th><th>Item Name</th><th>Quantity</th><th>Price (per item)</th><th>Amount</th></tr>
                <?php
                $itemNumber = 1;
                $totalAmount = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    if (!empty($row['itemName']) || $row['qty'] != 0 || $row['Price'] != 0 || $row['Amount'] != 0) {
                        echo "<tr>";
                        echo "<td>" . $itemNumber . "</td>";
                        echo "<td>" . htmlspecialchars($row['itemName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['qty']) . "</td>";
                        echo "<td>" . number_format($row['Price'], 2) . " ₹/-</td>";
                        echo "<td>" . number_format($row['Amount'], 2) . " ₹/-</td>";
                        echo "</tr>";
                        $totalAmount += $row['Amount'];
                        $itemNumber++;
                    }
                }
                ?>
            </table>
            <div class="total">Total Amount: <?php echo number_format($totalAmount, 2); ?> ₹/-</div>
        </div>
        </body>
        </html>
        <?php
    } else {
        echo "Error fetching bill details: " . mysqli_error($conn);
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
} else {
    echo "InvoiceNum parameter not specified.";
}

// Close database connection
mysqli_close($conn);
?>
