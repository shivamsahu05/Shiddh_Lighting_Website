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
        .table-container {
            max-height: 600px; /* Adjust based on your content height */
            overflow-y: auto; /* Add vertical scrollbar */
            margin-top: 20px;
            border: 2px solid #b1b1b1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #333;
            position: relative; /* Ensure header and body can be controlled independently */
        }
        thead th {
            position: -webkit-sticky; /* For Safari */
            position: sticky;
            top: 0;
            background-color: #b1b1b1;
            color: black;
            z-index: 2; /* Ensure header is above body rows */
        }
        table th, table td {
            border: 1px solid #b1b1b1;
            padding: 10px;
            text-align: center;
            font-weight: normal;
            box-sizing: border-box;
        }
        table tr:nth-child(even) {
            background-color: #555;
        }
        table tr:hover {
            background-color: #777;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        img {
            width: 200px;
            display: block;
            margin: 20px auto;
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

        /* Responsive Styles */
        @media only screen and (max-width: 1200px) {
            table th, table td {
                padding: 8px;
                font-size: 14px;
            }
        }

        @media only screen and (max-width: 992px) {
            table th, table td {
                padding: 6px;
                font-size: 12px;
            }
        }

        @media only screen and (max-width: 768px) {
            table th, table td {
                padding: 5px;
                font-size: 11px;
            }
        }

        @media only screen and (max-width: 576px) {
            table th, table td {
                padding: 4px;
                font-size: 10px;
            }
            table {
                font-size: 12px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <form action="#" method="POST">
        <img src="images/logosl.png" alt="Logo">
        <input type="date" name="searchDate">
        <input type="submit" name="bs" value="Search">
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr style='background:yellow; color:black;'>
                    <th>#</th>
                    <th>C_Name</th>
                    <th>S_Name</th>
                    <th>Address</th>
                    <th>Pending ₹</th>
                    <th>Paid ₹</th>
                    <th>Disc.</th>
                    <th>Present ₹</th>
                    <th>Before ₹</th>
                    <th>Date</th>
                    <th>inv No.</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Establish connection
                $con = mysqli_connect("localhost", "root", "", "shiddh");

                // Check connection
                if (mysqli_connect_errno()) {
                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                    exit();
                }

                // Determine query based on whether a date was provided
                if (isset($_POST['bs'])) {
                    $searchDate = $_POST['searchDate'];
                    if (!empty($searchDate)) {
                        $query = "SELECT b.*, bu.Net_Amount AS pending_amount
                                  FROM bills b
                                  LEFT JOIN billupdate bu ON b.InvoiceNum = bu.InvoiceNum
                                  WHERE b.Inv_date = '$searchDate'
                                  ORDER BY b.Customer_name ASC, b.Inv_date DESC";
                    } else {
                        // If no date is provided, show all data sorted by Customer_name and Inv_date
                        $query = "SELECT b.*, bu.Net_Amount AS pending_amount
                                  FROM bills b
                                  LEFT JOIN billupdate bu ON b.InvoiceNum = bu.InvoiceNum
                                  ORDER BY b.Customer_name ASC, b.Inv_date DESC";
                    }
                } else {
                    // Default query when the form is not submitted
                    $query = "SELECT b.*, bu.Net_Amount AS pending_amount
                              FROM bills b
                              LEFT JOIN billupdate bu ON b.InvoiceNum = bu.InvoiceNum
                              ORDER BY b.Customer_name ASC, b.Inv_date DESC";
                }

                $result = mysqli_query($con, $query);

                $serial = 1;
                // Loop through each row and display data in table rows
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr style='color:white'>";
                    echo "<td>" . $serial++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['Customer_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ShopName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Address']) . "</td>";

                    // Display pending amount based on availability in billupdate table or use totalnetamt from bills table
                    if (!empty($row['pending_amount'])) {
                        echo "<td style='font-weight: bold; color:red'>" . htmlspecialchars($row['pending_amount']) . "</td>";
                    } else {
                        echo "<td style='font-weight: bold; color:red'>" . htmlspecialchars($row['totalnetamt']) . "</td>";
                    }

                    echo "<td style='font-weight: bold'>" . htmlspecialchars($row['Paid']) . "</td>";
                    echo "<td style='font-weight: bold'>" . htmlspecialchars($row['discount']) . "</td>";
                    echo "<td style='font-weight: bold; color:blue'>" . htmlspecialchars($row['sub_total_Amount']) . "</td>";
                    echo "<td style='font-weight: bold; color:yellow'>" . htmlspecialchars($row['beforeamt']) . "</td>";

                    // Format date from yyyy-mm-dd to dd-mm-yyyy
                    $date = date('d-m-Y', strtotime($row['Inv_date']));
                    echo "<td>" . htmlspecialchars($date) . "</td>";

                    echo "<td style='font-weight: bold; color:yellow'>" . htmlspecialchars($row['InvoiceNum']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                    echo "</tr>";
                }

                // Close connection
                mysqli_close($con);
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
