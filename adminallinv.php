<?php
session_start();
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle delete request
if (isset($_POST['delete']) && !empty($_POST['InvoiceNum'])) {
    $invoiceNum = mysqli_real_escape_string($conn, $_POST['InvoiceNum']);

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete from billdetails table
        $deleteDetailsQuery = "DELETE FROM billdetails WHERE InvoiceNum = '$invoiceNum'";
        mysqli_query($conn, $deleteDetailsQuery);

        // Delete from bills table
        $deleteBillQuery = "DELETE FROM bills WHERE InvoiceNum = '$invoiceNum'";
        mysqli_query($conn, $deleteBillQuery);

        // Commit the transaction
        mysqli_commit($conn);

        // Redirect or display success message
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        mysqli_rollback($conn);
        echo "Error deleting data: " . $e->getMessage();
    }
}

// Determine query based on whether a date was provided
if (isset($_POST['serch'])) {
    $searchDate = $_POST['searchDate'];
    if (!empty($searchDate)) {
        $query = "SELECT * FROM bills WHERE Inv_date = '$searchDate' ORDER BY Inv_date DESC";
    } else {
        $query = "SELECT * FROM bills ORDER BY Inv_date DESC";
    }
} else {
    $query = "SELECT * FROM bills ORDER BY Inv_date DESC";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error fetching data: " . mysqli_error($conn);
} else {
    // Start HTML output
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Logged In User</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f0f0f0;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 1200px;
                margin: 20px auto;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }
            header {
                background-color: #45a049;
                padding: 15px 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: #fff;
                font-size: 24px;
                margin-bottom: 20px;
            }
            .logo {
                max-width: 150px;
                height: auto;
            }
            .user-info {
                text-align: left;
                padding-left: 20px;
            }
            .user-info p {
                margin: 5px 0;
            }
            .username, .phone {
                font-weight: bold;
                font-size: 18px;
                color: #fff;
                margin: 0;
            }
            .logout {
                font-style: italic;
                background-color: #4CAF50;
                color: #fff;
                padding: 10px 20px;
                border-radius: 5px;
                margin-left: 20px;
                text-decoration: none;
                transition: background-color 0.3s ease;
            }
            .logout:hover {
                background-color: #45a049;
            }
            h4 {
                text-decoration: underline double;
                color: black;
                font-weight: bold;
                text-align: center;
                font-size: 25px;
            }
            .table-container {
                max-height: 600px; /* Adjust based on your content height */
                overflow-y: auto; /* Add vertical scrollbar */
                margin-top: 20px;
                border: 2px solid #ddd;
                background-color: #fff;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            thead th {
                position: -webkit-sticky; /* For Safari */
                position: sticky;
                top: 0;
                background-color: #4CAF50;
                color: white;
                z-index: 2; /* Ensure header is above body rows */
            }
            th, td {
                padding: 12px 15px;
                text-align: center;
                border-bottom: 1px solid #ddd;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            tr:hover {
                background-color: #ddd;
            }
            @media screen and (max-width: 992px) {
                .container {
                    border-radius: 0;
                    box-shadow: none;
                }
                header {
                    font-size: 20px;
                    flex-direction: column;
                    align-items: flex-start;
                }
                .logo {
                    margin-bottom: 10px;
                }
                .user-info {
                    text-align: center;
                    padding: 0;
                    margin-top: 10px;
                    margin-left: 0;
                }
                .logout {
                    margin-left: 0;
                    margin-top: 10px;
                }
                table {
                    font-size: 14px;
                }
                th, td {
                    padding: 8px;
                }
            }
            @media screen and (max-width: 768px) {
                table {
                    font-size: 12px;
                }
                th, td {
                    padding: 6px;
                }
            }
            @media screen and (max-width: 576px) {
                table {
                    font-size: 10px;
                }
                th, td {
                    padding: 4px;
                }
                .username, .phone {
                    font-size: 16px;
                }
                .logout {
                    padding: 8px 16px;
                    font-size: 14px;
                }
            }
        </style>
        <script>
            function confirmDelete(invoiceNum) {
                var confirmation = confirm("Are you sure you want to delete invoice number " + invoiceNum + "?");
                if (confirmation) {
                    // If confirmed, set the form action to the delete endpoint and submit the form
                    document.getElementById('delete-form').action = '';
                    document.getElementById('delete-form').submit();
                }
                // If cancelled, do nothing
            }
        </script>
    </head>
    <body>

    <div class="container">
        <header>
            <img src="images/logosl.png" alt="logo" class="logo">
            <div class="user-info">
                <p class="username">Welcome: Admin</p>
            </div>
        </header>

        <form action="" method="POST" style="text-align: center; margin-bottom: 20px;">
            <input type="date" name="searchDate">
            <input type="submit" name="serch" value="Search">
        </form>

        <div class="table-container">
            <h4>!! All Invoices !!</h4>
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Name</th>
                    <th>Shop Name</th>
                    <th>Phone</th>
                    <th>Total Net Amount</th>
                    <th>Before Amount</th>
                    <th>Discount</th>
                    <th>Sub Total Amount</th>
                    <th>Detail</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial = 1;
                // Display fetched data in table rows
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    $date = date('d-m-Y', strtotime($row['Inv_date']));
                    echo "<td>" . $serial++ . "</td>";
                    echo "<td>" . $date . "</td>";
                    echo "<td style='font-weight: bold; color: black;'>" . htmlspecialchars($row['InvoiceNum']) . "</td>";
                    echo "<td style='font-weight: bold; color: black;'>" . htmlspecialchars($row['Customer_name']) . "</td>";
                    echo "<td style='font-weight: bold; color: black;'>" . htmlspecialchars($row['ShopName']) . "</td>";
                    echo "<td style='font-weight: bold; color: black;'>" . htmlspecialchars($row['Phone']) . "</td>";
                    echo "<td style='font-weight: bold; color: red;'>" . htmlspecialchars($row['totalnetamt']) . "</td>";
                    echo "<td style='font-weight: bold; color: black;'>" . htmlspecialchars($row['beforeamt']) . "</td>";
                    echo "<td style='font-weight: bold; color: blue;'>" . htmlspecialchars($row['discount']) . "</td>";
                    echo "<td style='font-weight: bold; color: green;'>" . htmlspecialchars($row['sub_total_Amount']) . "</td>";
                    echo "<td>";
                    echo "<form action='invdetails.php' method='POST'>";
                    echo "<input type='hidden' name='InvoiceNum' value='" . htmlspecialchars($row['InvoiceNum']) . "' />";
                    echo "<input type='submit' value='Details' />";
                    echo "</form>";
                    echo "</td>";
                    echo "<td>";
                    echo "<form action='ab.php' method='POST'>";
                    echo "<input type='hidden' name='InvoiceNum' value='" . htmlspecialchars($row['InvoiceNum']) . "' />";
                    echo "<input type='submit' value='Update' />";
                    echo "</form>";
                    echo "</td>";
                    echo "<td>";
                    // JavaScript confirmation
                    echo "<form id='delete-form' action='' method='POST' style='display: none;'>";
                    echo "<input type='hidden' name='InvoiceNum' value='" . htmlspecialchars($row['InvoiceNum']) . "' />";
                    echo "<input type='hidden' name='delete' value='1' />";
                    echo "</form>";
                    echo "<button onclick='confirmDelete(\"" . htmlspecialchars($row['InvoiceNum']) . "\")'>Delete</button>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

    </div>

    </body>
    </html>
    <?php
}

// Close the database connection
mysqli_close($conn);
?>
