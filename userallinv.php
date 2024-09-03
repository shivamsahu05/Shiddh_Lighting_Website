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

// Fetch data related to the logged-in user, sorted by date in descending order
$query = "SELECT * FROM bills WHERE Customer_name = ? ORDER BY Inv_date DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo "Error fetching data: " . mysqli_error($conn);
} else {
    // Start HTML output
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Logged In User</title>";
    echo "<style>
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
            font-size: 25px
        }
        .content {
            padding: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        @media screen and (max-width: 768px) {
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
        }
    </style>";
    echo "</head>";
    echo "<body>";

    // Logo, welcome message, and logout link
    echo "<div class='container'>";
    echo "<header>";
    echo "<img src='images/logosl.png' alt='logo' class='logo'>";
    echo "<div class='user-info'>";
    echo "<p class='username'>Welcome: " . $_SESSION['username'] . "</p>";

    // Fetch and display phone number (assuming only one row for the user)
    $phoneQuery = "SELECT Phone, ShopName FROM bills WHERE Customer_name = ? LIMIT 1";
    $phoneStmt = mysqli_prepare($conn, $phoneQuery);
    mysqli_stmt_bind_param($phoneStmt, "s", $_SESSION['username']);
    mysqli_stmt_execute($phoneStmt);
    $phoneResult = mysqli_stmt_get_result($phoneStmt);
    $phoneRow = mysqli_fetch_assoc($phoneResult);
    if ($phoneRow) {
        echo "<p class='phone'>Number: " . $phoneRow['Phone'] . "</p>";
        echo "<p class='phone'>Shop_Name: " . $phoneRow['ShopName'] . "</p>";
    }

    echo "</div>"; // End of user-info div
    echo "<a href='logout.php' class='logout'>Log-Out</a>";
    echo "</header>";

    // Table for displaying fetched data
    echo "<div class='content'>";
    echo "<h4>!! My Bills !!  </h4> ";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Date</th>";
    echo "<th>Invoice Number</th>";
    echo "<th>Before Amount</th>";
    echo "<th>Total Sub Amount</th>";
    echo "<th>Paid</th>";
    echo "<th>CardToPay</th>";
    echo "<th>Discount</th>";
    echo "<th>Net Amount Total</th>";
    echo "<th>Detail</th>"; // Changed from "Details" to "Detail" as per your request
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    // Display fetched data in table rows
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        $date = date('d-m-Y', strtotime($row['Inv_date']));
        echo "<td>" . $date . "</td>";
        echo "<td style='font-weight: bold; color: black;'>" . $row['InvoiceNum'] . "</td>";
        echo "<td style='font-weight: bold; color: olive;'>" . $row['beforeamt'] . "</td>";
        echo "<td style='font-weight: bold; color: green;'>" . $row['sub_total_Amount'] . "</td>";
        echo "<td style='font-weight: bold; color: blue;''>" . $row['Paid'] . "</td>";
        echo "<td style='font-weight: bold; color: blue;''>" . $row['cardtopay'] . "</td>";
        echo "<td style='font-weight: bold; color: blue;''>" . $row['discount'] . "</td>";
        echo "<td style='font-weight: bold; color: red;'>" . $row['totalnetamt'] . "</td>";
        echo "<td>
        <form action='invdetails.php' method='post'>
            <input type='hidden' name='InvoiceNum' value='" . htmlspecialchars($row['InvoiceNum']) . "'>
            <input type='submit' value='Details'>
        </form>
      </td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>"; // End of content div
    echo "</div>"; // End of container div

    echo "</body>";
    echo "</html>";
}

// Close the database connections
mysqli_stmt_close($stmt);
mysqli_stmt_close($phoneStmt);
mysqli_close($conn);
?>
