<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle Update Requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $order_id = isset($_POST['ord_id']) ? $_POST['ord_id'] : '';
    $items = isset($_POST['items']) ? $_POST['items'] : '';
    $qtys = isset($_POST['qtys']) ? $_POST['qtys'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    if (isset($_POST['update'])) {
        // Prepare update query
        $query = "UPDATE orderdetails SET items=?, qtys=?, discription=? WHERE ord_id=?";
        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            die("Error preparing statement: " . mysqli_error($conn));
        }

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sssi", $items, $qtys, $description, $order_id);

        // Execute statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to the same page after successful update
            header("Location: " . $_SERVER['PHP_SELF'] . "?ordernum=" . htmlspecialchars($_GET['ordernum']));
            exit();
        } else {
            $error_message = "Error updating item details: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }

    if (isset($_POST['delete'])) {
        // Prepare SQL statement to delete item
        $query = "DELETE FROM orderdetails WHERE ord_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);

        // Check if deletion was successful
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Item deleted successfully.";
            header("Location: " . $_SERVER['PHP_SELF'] . "?ordernum=" . htmlspecialchars($_GET['ordernum']));
            exit();
        } else {
            echo "Error deleting item: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}

// Fetch InvoiceNum from query parameter
$orderNum = isset($_GET['ordernum']) ? $_GET['ordernum'] : '';

if ($orderNum) {
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
        echo "<title>Order Details</title>";
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
            .form-container textarea {
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
            /* Media Queries */
            @media (max-width: 1200px) {
                .container {
                    padding: 15px;
                }
                table th, table td {
                    font-size: 14px;
                    padding: 10px;
                }
                .form-container input[type='text'],
                .form-container textarea {
                    padding: 6px;
                }
                .delete-button {
                    font-size: 13px;
                }
            }
            @media (max-width: 992px) {
                .container {
                    padding: 10px;
                }
                table th, table td {
                    font-size: 12px;
                    padding: 8px;
                }
                .form-container input[type='text'],
                .form-container textarea {
                    padding: 5px;
                }
                .delete-button {
                    font-size: 12px;
                }
            }
            @media (max-width: 768px) {
                .container {
                    padding: 5px;
                }
                table {
                    font-size: 12px;
                }
                table th, table td {
                    padding: 6px;
                }
                .form-container input[type='text'],
                .form-container textarea {
                    padding: 4px;
                }
                .delete-button {
                    font-size: 11px;
                }
            }
            @media (max-width: 576px) {
                table th, table td {
                    font-size: 10px;
                    padding: 5px;
                }
                .form-container {
                    padding: 10px;
                }
                .form-container input[type='text'],
                .form-container textarea {
                    padding: 4px;
                }
                .delete-button {
                    font-size: 10px;
                }
            }
        </style>";
        echo "</head>";
        echo "<body>";

        // Container for better centering and padding
        echo "<div class='container'>";

        // Display bill details header
        echo "<h2>Bill Details for Order Number: $orderNum</h2>";
        echo "<table>";
        echo "<tr><th>Item Number</th><th>Item Name</th><th>Quantity</th><th>Description</th><th>Actions</th></tr>";

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

                // Update form
                echo "<td>";
                echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to Update this item?\");'>";
                echo "<input type='hidden' name='ord_id' value='" . htmlspecialchars($row['ord_id']) . "'>";
                echo "<input type='hidden' name='ordernum' value='" . htmlspecialchars($orderNum) . "'>";

                echo "<div class='form-container'>";
                echo "<h3>Update This Item</h3>";
                echo "<label for='items'>Item Name:</label>";
                echo "<input type='text' id='items' name='items' value='" . htmlspecialchars($row['items']) . "' required>";
                echo "<label for='qtys'>Quantity:</label>";
                echo "<input type='text' id='qtys' name='qtys' value='" . htmlspecialchars($row['qtys']) . "' required>";
                echo "<label for='description'>Description:</label>";
                echo "<textarea id='description' name='description' required>" . htmlspecialchars($row['discription']) . "</textarea>";
                echo "<input type='submit' name='update' value='Update'>";
                echo "</div>";

                echo "</form>";

                // Delete form
                echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to delete this item?\");'>";
                echo "<input type='hidden' name='ord_id' value='" . htmlspecialchars($row['ord_id']) . "'>";
                echo "<input type='hidden' name='ordernum' value='" . htmlspecialchars($orderNum) . "'>";
                echo "<input type='submit' name='delete' value='Delete' class='delete-button'>";
                echo "</form>";
                echo "</td>";

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

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    echo "OrderNum parameter not specified.";
}

// Close connection
mysqli_close($conn);
?>
