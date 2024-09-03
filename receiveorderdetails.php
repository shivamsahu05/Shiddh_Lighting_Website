<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch InvoiceNum from query parameter
if (isset($_GET['ordernum'])) {
    $orderNum = $_GET['ordernum'];
    $username = $_SESSION['username']; // Assuming this is safe from SQL injection

    // Prepare statement
    $query = "SELECT * FROM orderdetails WHERE ordernum = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "s", $orderNum);

    // Execute statement
    mysqli_stmt_execute($stmt);

    // Get result
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        // Start HTML output
        echo "<!DOCTYPE html>";
        echo "<html lang='en'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Order Details Customer</title>";
        echo "<style>
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
        </style>";
        echo "</head>";
        echo "<body>";

        // Container for better centering and padding
        echo "<div class='container'>";

        // Display bill details header
        echo "<h2>Bill Details for Order Number: $orderNum</h2>";
        echo "<table>";
        echo "<tr><th>Item Number</th><th>Item Name</th><th>Quantity</th><th>Description</th></tr>";

        $itemNumber = 1; // Initialize item number counter

        // Loop through fetched results and display in table rows
        while ($row = mysqli_fetch_assoc($result)) {
            // Skip rows where 'items' is empty and 'qtys' is 0
            if (!empty($row['items']) || $row['qtys'] != 0) {
                echo "<tr>";
                echo "<td>" . $itemNumber . "</td>"; // Display item number
                echo "<td>" . htmlspecialchars($row['items']) . "</td>";
                echo "<td>" . htmlspecialchars($row['qtys']) . "</td>";
                echo "<td>" . htmlspecialchars($row['discription']) . "</td>"; // Display description directly
                echo "</tr>";

                $itemNumber++; // Increment item number for the next valid row
            }
        }

        echo "</table>";

        // End container
        echo "</div>";

        // End HTML output
        echo "</body>";
        echo "</html>";
    } else {
        echo "Error fetching bill details: " . mysqli_error($conn);
    }

    // Close statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo "OrderNum parameter not specified.";
}
?>
