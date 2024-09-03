<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            padding: 20px;
            background-color: #333;
        }
        
        .header img {
            max-width: 100%;
            height: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #4CAF50; /* Green background */
            color: white;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:nth-child(odd) {
            background-color: #f1f1f1;
        }

        tr:hover {
            background-color: #ddd;
        }

        .total-row {
            font-weight: bold;
            background-color: #ffeb3b; /* Light yellow */
            color: #333;
        }

        .total-row th {
            background-color: #ffc107; /* Darker yellow */
            color: #333;
        }

        @media (max-width: 768px) {
            th, td {
                font-size: 14px;
                padding: 8px;
            }
        }

        @media (max-width: 480px) {
            table {
                font-size: 12px;
                margin: 10px;
            }

            th, td {
                padding: 6px;
            }
        }

        .header-button {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .header-button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .header-button:active {
            background-color: #388e3c;
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/logosl.png" alt="Logo" width="200">
        <!-- Optional button example -->
        <!-- <a href="#" class="header-button">Some Action</a> -->
    </div>

    <?php
    error_reporting(0);
    session_start();
    // Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
    $con = mysqli_connect("localhost", "root", "", "shiddh");

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    function formatDate($date) {
        return date('d-m-Y', strtotime($date));
    }

    $username = $_SESSION['username'];
    $query_user = mysqli_query($con, "SELECT * FROM registration WHERE FullName = '$username'");
    $user_details = mysqli_fetch_assoc($query_user);

    echo "<table>";
    echo "<tr class='header-row'>";
    echo "<th>C_Name</th>";
    echo "<th>C_Mobile</th>";
    echo "<th>Shop_Name</th>";
    echo "<th>Address</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($user_details['FullName']) . "</td>";
    echo "<td>" . htmlspecialchars($user_details['Phone']) . "</td>";
    echo "<td>" . htmlspecialchars($user_details['ShopName']) . "</td>";
    echo "<td>" . htmlspecialchars($user_details['Address']) . "</td>";
    echo "</tr>";
    echo "</table>";

    $query_bills = mysqli_query($con, "SELECT * FROM bills WHERE Customer_name = '$username' OR ShopName = '$username' ORDER BY STR_TO_DATE(Inv_date, '%Y-%m-%d') DESC");

    $rows = [];
    $netAmountTotal = [];

    while ($row = mysqli_fetch_assoc($query_bills)) {
        $formattedDate = formatDate($row['Inv_date']);
        $rows[$formattedDate][] = $row;
        if (!isset($netAmountTotal[$formattedDate])) {
            $netAmountTotal[$formattedDate] = $row['totalnetamt'];
        } else {
            $netAmountTotal[$formattedDate] += $row['totalnetamt'];
        }
    }

    $latestDate = reset(array_keys($rows));

    echo "<table>";
    echo "<tr class='total-row'>";
    echo "<th colspan='5'></th>";
    echo "<th>Pending Amount </th>";
    echo "<th>" . number_format($netAmountTotal[$latestDate], 2) . " ₹</th>";
    echo "</tr>";

    echo "<tr class='header-row'>";
    echo "<th>Date</th>";
    echo "<th>Invoice Number</th>";
    echo "<th>Amount ₹/-</th>";
    echo "<th>Before Amount ₹/-</th>";
    echo "<th>Charge ₹/-</th>";
    echo "<th>Total Amount ₹/-</th>";
    echo "<th>Paid Amount ₹/-</th>";
    echo "<th>Discount</th>";
    echo "<th>Total Pending Amount ₹/-</th>";
    echo "</tr>";

    foreach ($rows as $date => $rowList) {
        foreach ($rowList as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($date) . "</td>";
            echo "<td>" . htmlspecialchars($row['InvoiceNum']) . "</td>";
            echo "<td style='font-weight: bold;'>" . htmlspecialchars($row['sub_total_Amount']) . "</td>";
            echo "<td style='font-weight: bold;'>" . htmlspecialchars($row['beforeamt']) . "</td>";
            echo "<td style='font-weight: bold; color: black'>" . htmlspecialchars($row['Charge']) . "</td>";
            echo "<td style='font-weight: bold; color: blue'>" . htmlspecialchars($row['totalamt']) . "</td>";
            echo "<td style='font-weight: bold; color: black'>" . htmlspecialchars($row['Paid']) . "</td>";
            echo "<td style='font-weight: bold; color:#388e3c'>" . htmlspecialchars($row['discount']) . "</td>";
            echo "<td style='font-weight: bold; color:red'>" . htmlspecialchars($row['totalnetamt']) . "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";

    mysqli_close($con);
    ?>
</body>
</html>
