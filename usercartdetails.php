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

// Fetch InvoiceNum and Mobile_Num from POST parameters
if (isset($_POST['ordernum']) && isset($_POST['mobile'])) {
    $ordernum = $_POST['ordernum'];
    $mobile = $_POST['mobile'];
    $username = $_SESSION['username']; // Assuming this is safe from SQL injection

    // Validate the order number and mobile number for the logged-in user
    $validationQuery = "SELECT Mobile_Num FROM orders WHERE CustomerName = ? AND ordernum = ? AND Mobile_Num = ?";
    $validationStmt = mysqli_prepare($conn, $validationQuery);
    mysqli_stmt_bind_param($validationStmt, "sis", $username, $ordernum, $mobile);
    mysqli_stmt_execute($validationStmt);
    $validationResult = mysqli_stmt_get_result($validationStmt);

    if (mysqli_num_rows($validationResult) > 0) {
        // Prepare statement for fetching order details
        $query = "SELECT * FROM orderdetails WHERE ordernum = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $ordernum);
        mysqli_stmt_execute($stmt);
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

            // Display bill details header with mobile number
            echo "<h2>!! <u>My Order Details Number: $ordernum</u> !!</h2>";
            echo "<p style='text-align: center;'>Mobile Number: " . htmlspecialchars($mobile) . "</p>";
            echo "<table>";
            echo "<tr><th>#</th><th>Item Name</th><th>Quantity</th><th>Description</th></tr>";

            $itemNumber = 1; // Initialize item number counter
            // Loop through fetched results and display in table rows
            while ($row = mysqli_fetch_assoc($result)) {
                // Skip rows where itemName is empty and qty, Price, Amount are 0
                if (!empty($row['items']) || $row['qtys'] != 0) {
                    echo "<tr>";
                    echo "<td>" . $itemNumber . "</td>"; // Display item number
                    echo "<td>" . htmlspecialchars($row['items']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['qtys']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['discription']) . "</td>";
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
            echo "Error fetching Order details: " . mysqli_error($conn);
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Invalid order number or mobile number.";
    }

    // Close the validation statement
    mysqli_stmt_close($validationStmt);
} else {
    echo "Order Number or Mobile Number parameter not specified.";
}

// Close the database connection
mysqli_close($conn);
?>
