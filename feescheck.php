<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management System</title>
    <style>
        body {
            background-color: #1a1a1a;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        #container {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
            border: 2px solid #b1b1b1;
            border-radius: 8px;
            background-color: #333;
        }
        #container img {
            width: 200px;
            display: block;
            margin: 0 auto;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        form input[type="date"], form input[type="submit"] {
            padding: 8px;
            margin: 4px;
            border: none;
            border-radius: 4px;
            background-color: #b1b1b1;
            color: black;
            cursor: pointer;
        }
        form input[type="date"]:focus, form input[type="submit"]:hover {
            background-color: #808080;
        }
        .table-container {
            max-height: 400px; /* Adjust based on your content height */
            overflow-y: auto; /* Add vertical scrollbar */
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #b1b1b1;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #b1b1b1;
            color: black;
        }
        table tr:nth-child(even) {
            background-color: #555;
        }
        table tr:hover {
            background-color: #777;
        }
        .total-row {
            background-color: #ffe9e9;
            font-weight: bold;
            color: blue;
        }
        .total-row td {
            text-align: right;
        }
        @media (max-width: 600px) {
            #container {
                width: 90%;
                margin: 10px auto;
                padding: 5px;
            }
            #container img {
                width: 150px;
            }
            form input[type="date"], form input[type="submit"] {
                padding: 6px;
                margin: 3px;
                font-size: 14px;
            }
            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div id="container">
        <form action="#" method="POST">
            <img src="images/logosl.png" alt="Logo">
            <br><br>
            <input type="date" name="btn2">
            <input type="submit" name="bs" value="Search">
        </form>
        <?php
        session_start();
        // Check if the user is not logged in, redirect to login page
        if (!isset($_SESSION['username'])) {
            header('Location: login.php'); // Redirect to login page if not logged in
            exit;
        }
// Establish connection
$con = mysqli_connect("localhost", "root", "", "shiddh");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Initialize variables
$totalPaid = 0;
$totalPending = 0;

// Determine if search button is clicked and handle date input
if (isset($_POST['bs'])) {
    $se = $_POST['btn2']; // Get selected date from form

    // Query to calculate total paid amount for selected date
    if (!empty($se)) {
        $queryPaidTotal = "SELECT SUM(Paid) AS totalPaid FROM bills WHERE Inv_date = '$se' AND Paid > 0";
        // Execute query to get total paid amount
        $resultPaidTotal = mysqli_query($con, $queryPaidTotal);
        $rowPaidTotal = mysqli_fetch_assoc($resultPaidTotal);
        $totalPaid = $rowPaidTotal['totalPaid'];
    }
}

// Query to calculate total pending amount based on the latest entry for each unique customer-shop-phone combination
$queryPendingTotal = "
    SELECT SUM(totalnetamt) AS totalPending 
    FROM (
        SELECT * 
        FROM bills 
        WHERE (ShopName, Phone, Inv_date) IN (
            SELECT ShopName, Phone, MAX(Inv_date)
            FROM bills
            WHERE totalnetamt > 0
            GROUP BY ShopName, Phone
        )
    ) AS latestBills
    WHERE totalnetamt > 0
";

$resultPendingTotal = mysqli_query($con, $queryPendingTotal);
$rowPendingTotal = mysqli_fetch_assoc($resultPendingTotal);
$totalPending = $rowPendingTotal['totalPending'];

// Display total pending amount row
echo "<div class='table-container'>";
echo "<table>";
echo "<tr>";
echo "<th>#</th>"; // Serial Number
echo "<th>Customer Name</th>";
echo "<th>Shop Name</th>";
echo "<th>Phone</th>";
echo "<th>Address</th>";
echo "<th>Date</th>";
echo "<th>Card</th>";
echo "<th style='font-weight: bold; color: blue;'>Paid</th>";
echo "<th style='font-weight: bold; color: red;'>Total Net Amount</th>";
echo "</tr>";

// Query to fetch invoice details based on search or default
if (isset($_POST['bs'])) {
    $se = $_POST['btn2'];
    if (!empty($se)) {
        $query = "SELECT * FROM bills WHERE Inv_date='$se' AND Paid > 0 ORDER BY Inv_date DESC";
    } else {
        $query = "
            SELECT * 
            FROM (
                SELECT * 
                FROM bills
                WHERE (ShopName, Phone, Inv_date) IN (
                    SELECT ShopName, Phone, MAX(Inv_date)
                    FROM bills
                    WHERE totalnetamt > 0
                    GROUP BY ShopName, Phone
                )
            ) AS latestBills
            ORDER BY Inv_date DESC
        ";
    }
} else {
    // Default query to get the latest invoice for each unique combination
    $query = "
        SELECT * 
        FROM (
            SELECT * 
            FROM bills
            WHERE (ShopName, Phone, Inv_date) IN (
                SELECT ShopName, Phone, MAX(Inv_date)
                FROM bills
                WHERE totalnetamt > 0
                GROUP BY ShopName, Phone
            )
        ) AS latestBills
        ORDER BY Inv_date DESC
    ";
}

// Execute main query
$result = mysqli_query($con, $query);

if (!$result) {
    echo "Error: " . mysqli_error($con);
} else {
    // Array to keep track of displayed entries
    $serial = 1; // Initialize serial number

    while ($row = mysqli_fetch_array($result)) {
        $cName = $row['Customer_name'];
        $shopName = $row['ShopName'];
        $phone = $row['Phone'];
        $invDate = $row['Inv_date'];
        $totalnetamt = $row['totalnetamt'];
        $paid = isset($row['Paid']) ? $row['Paid'] : 0; // Fetch paid amount
        $cardtopay = isset($row['cardtopay']) ? $row['cardtopay'] : ''; // Fetch cardtopay

        // Display the row
        echo "<tr>";
        echo "<td>$serial</td>"; // Serial Number
        echo "<td>$cName</td>";
        echo "<td>$shopName</td>";
        echo "<td>$phone</td>";
        echo "<td>{$row['Address']}</td>";
        echo "<td>" . date('d-m-Y', strtotime($invDate)) . "</td>";
        echo "<td>$cardtopay</td>";

        // Display paid amount if search button clicked
        echo "<td style='font-weight: bold; color: blue;'>$paid</td>";

        // Display total net amount
        echo "<td style=' color: red;'>$totalnetamt</td>";

        echo "</tr>";

        $serial++; // Increment serial number
    }

    // Display total pending amount row
    echo "<tr class='total-row'>";
    echo "<td colspan='8'>Total Pending</td>";
    echo "<td style='font-weight: bold; color: red;'>$totalPending ₹</td>";
    echo "</tr>";

    // Display total paid amount row if search button clicked
    if (isset($_POST['bs']) && !empty($_POST['btn2'])) {
        echo "<tr class='total-row'>";
        echo "<td colspan='7'>Total Paid ₹ on $se</td>";
        echo "<td style='font-weight: bold; color: blue;'>$totalPaid</td>";
        echo "</tr>";
    }
}

echo "</table>";
echo "</div>"; // End of table-container div

// Close connection
mysqli_close($con);
?>



    </div>
</body>
</html>
