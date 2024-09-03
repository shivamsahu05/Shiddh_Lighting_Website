<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and Update</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        #searchForm {
            text-align: center;
            margin-top: 20px;
        }

        #S {
            height: 30px;
            padding: 5px;
            background: #b1b1b1;
            border: none;
            margin-right: 5px;
            width: 200px;
            border-radius: 5px;
        }

        input[type="submit"] {
            padding: 8px 20px;
            background-color: #4CAF50;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .table-container {
            max-height: 400px;
            overflow-y: auto;
            margin: 20px;
            border: 1px solid #ddd; /* Optional border for better visibility */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid white; /* Border for the table */
        }

        th, td {
            border: 1px solid white;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: yellow;
            color: black;
            font-weight: bold;
            position: -webkit-sticky; /* For Safari */
            position: sticky;
            top: 0; /* Stick to the top of the container */
            z-index: 2; /* Ensure it stays above table rows */
        }

        tr:nth-child(even) {
            background-color: #444;
        }

        tr:nth-child(odd) {
            background-color: #333;
        }

        a.update-link {
            text-decoration: none;
            color: blue;
            font-weight: bold;
            cursor: pointer;
        }

        a.update-link:hover {
            color: darkblue;
        }

        .footer {
            font-weight: bold;
            background-color: #222;
            color: #fff;
            text-align: right;
            padding: 10px;
        }

        @media (max-width: 600px) {
            #S {
                width: 80%;
            }
            table {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div id="searchForm">
        <form action="feesupdate.php" method="POST">
            <img src="images/logosl.png" alt="Logo" width="200px"><br><br>
            <input type="text" name="btn1" placeholder="Search" id="S"> 
            <input type="submit" name="bs" value="Search">
        </form>
    </div>
    <?php
    // Establish database connection
    $con = mysqli_connect("localhost", "root", "", "shiddh");

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    // Process form submission
    if (isset($_POST['bs'])) {
        $se = mysqli_real_escape_string($con, $_POST['btn1']);
        
        // Fetch the latest records for each unique combination of Customer_name, Phone, and ShopName
        $query = "
            SELECT b.Customer_name, b.Phone, b.ShopName, b.Address, b.InvoiceNum, b.Inv_date, b.totalnetamt
            FROM bills b
            INNER JOIN (
                SELECT Phone, ShopName, MAX(Inv_date) AS max_date
                FROM bills
                WHERE Customer_name LIKE '%$se%' 
                OR ShopName LIKE '%$se%' 
                OR Address LIKE '%$se%'
                GROUP BY Phone, ShopName
            ) latest
            ON b.Phone = latest.Phone
            AND b.ShopName = latest.ShopName
            AND b.Inv_date = latest.max_date
            ORDER BY b.Inv_date DESC
        ";

        $r = mysqli_query($con, $query);

        // Initialize total amount variable
        $totalPendingAmount = 0;

        echo "<div class='table-container'>";
        echo "<table>";
        echo "<tr>";
        echo "<th>#</th>";
        echo "<th>C_Name</th>";
        echo "<th>C_Phone</th>";
        echo "<th>C_ShopName</th>";
        echo "<th>C_Address</th>";
        echo "<th>I_Number</th>";
        echo "<th>Invoice_Date</th>";
        echo "<th>Pending_Amount</th>";
        echo "<th>Update</th>";
        echo "</tr>";

        $serialNumber = 1;
        while ($r1 = mysqli_fetch_array($r)) {
            // Add the pending amount to total
            $totalPendingAmount += $r1['totalnetamt'];

            echo "<tr>";
            echo "<td>", $serialNumber++, "</td>";
            echo "<td>", htmlspecialchars($r1['Customer_name']), "</td>";
            echo "<td>", htmlspecialchars($r1['Phone']), "</td>";
            echo "<td>", htmlspecialchars($r1['ShopName']), "</td>";
            echo "<td>", htmlspecialchars($r1['Address']), "</td>";
            echo "<td>", htmlspecialchars($r1['InvoiceNum']), "</td>";

            // Convert date format from yyyy-mm-dd to dd-mm-yyyy
            $invoiceDate = date('d-m-Y', strtotime($r1['Inv_date']));
            echo "<td>", $invoiceDate, "</td>";

            echo "<td style='font-weight: bold; color:red'>", htmlspecialchars($r1['totalnetamt']), "  ₹</td>";
            echo "<td>
                    <form action='tria.php' method='POST' style='display:inline;'>   
                        <input type='hidden' name='invoiceNum' value='", htmlspecialchars($r1['InvoiceNum']), "'>
                        <button type='submit' class='update-link'>Update</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        // feesupdate_update.php
        // Display total pending amount in footer
        echo "<tr class='footer'>";
        echo "<td colspan='7'>Total Pending Amount</td>";
        echo "<td style='font-weight: bold; color:yellow;'>", number_format($totalPendingAmount, 2), "</td>";
        echo "<td></td>";
        echo "</tr>";

        echo "</table>";
        echo "</div>";
    }
    ?>
</body>
</html>
