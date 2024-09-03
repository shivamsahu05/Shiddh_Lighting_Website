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

// Fetch data from the billupdate table, sorted by date_paid in descending order
$query = "SELECT * FROM billupdate ORDER BY date_paid DESC";
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
        <title>Bill Update</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f9f9f9;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 1200px;
                margin: 20px auto;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }
            header {
                background-color: #007bff;
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
            .username {
                font-weight: bold;
                font-size: 18px;
                color: #fff;
                margin: 0;
            }
            .logout {
                font-style: italic;
                background-color: #0056b3;
                color: #fff;
                padding: 10px 20px;
                border-radius: 5px;
                margin-left: 20px;
                text-decoration: none;
                transition: background-color 0.3s ease;
            }
            .logout:hover {
                background-color: #004494;
            }
            h4 {
                text-decoration: underline;
                color: #333;
                font-weight: bold;
                text-align: center;
                font-size: 25px;
            }
            .table-container {
                max-height: 500px; /* Set a fixed height for the table container */
                overflow-y: auto; /* Enable vertical scrolling */
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
                background-color: #007bff;
                color: white;
                z-index: 2; /* Ensure header is above body rows */
            }
            th, td {
                padding: 12px 15px;
                text-align: center;
                border-bottom: 1px solid #ddd;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #e2e6ea;
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
                .username {
                    font-size: 16px;
                }
                .logout {
                    padding: 8px 16px;
                    font-size: 14px;
                }
            }
        </style>
    </head>
    <body>

    <div class="container">
        <header>
            <img src="images/logosl.png" alt="logo" class="logo">
            <div class="user-info">
                <p class="username">Welcome: Admin</p>
            </div>
        </header>

        <div class="table-container">
            <h4>!! All Updated Bills !!</h4>
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date Paid</th>
                    <th>Invoice Number</th>
                    <th>Customer Name</th>
                    <th>Shop Name</th>
                    <th>Phone</th>
                    <th>Added Amount</th>
                    <th>Discription</th>
                    <th>Paid</th>
                    <th>Card to Pay</th>
                    <th>Net Amount</th>
                   
                </tr>
                </thead>
                <tbody>
                <?php
                $serial = 1;
                // Display fetched data in table rows
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    $date_paid = date('d-m-Y', strtotime($row['date_paid']));
                    echo "<td>" . $serial++ . "</td>";
                    echo "<td>" . $date_paid . "</td>";
                    echo "<td style='font-weight: bold; color: #333;'>" . htmlspecialchars($row['InvoiceNum']) . "</td>";
                    echo "<td style='font-weight: bold; color: #333;'>" . htmlspecialchars($row['CustomerName']) . "</td>";
                    echo "<td style='font-weight: bold; color: #333;'>" . htmlspecialchars($row['ShopName']) . "</td>";
                    echo "<td style='font-weight: bold; color: #333;'>" . htmlspecialchars($row['Phone']) . "</td>";
                    echo "<td style='font-weight: bold; color: #333;'>" . htmlspecialchars($row['addedamount']) . "</td>";
                    echo "<td style='font-weight: bold; color: #333;'>" . htmlspecialchars($row['discription']) . "</td>";
                    echo "<td style='font-weight: bold; color: #333;'>" . htmlspecialchars($row['Paid']) . "</td>";
                    echo "<td style='font-weight: bold; color: #333;'>" . htmlspecialchars($row['cardtopay']) . "</td>";
                    echo "<td style='font-weight: bold; color: #d9534f;'>" . htmlspecialchars($row['Net_Amount']) . "</td>";
                 
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div> <!-- End of table-container div -->
    </div> <!-- End of container div -->

    </body>
    </html>
    <?php
}

// Close the database connection
mysqli_close($conn);
?>
