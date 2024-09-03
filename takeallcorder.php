<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Received Orders</title>
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        header img {
            width: 200px;
        }

        h1 {
            color: #333;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .orders {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        td {
            background-color: #f9f9f9;
            color: #333;
            vertical-align: middle;
        }

        .action-btn {
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
            margin-right: 4px;
            transition: background-color 0.3s ease;
        }

        .action-btn:hover {
            opacity: 0.8;
        }

        .update-btn {
            background-color: #28a745;
            color: white;
        }

        .reset-btn {
            background-color: #dc3545;
            color: white;
        }

        .details-btn {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
            padding: 8px 16px;
            font-size: 14px;
            text-transform: uppercase;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        .details-btn:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .details-btn:active {
            transform: translateY(1px);
        }

        .details-btn:focus {
            outline: none;
        }

        .no-orders {
            text-align: center;
            padding: 20px;
            color: #777;
        }

        /* Style for table rows */
        tbody tr {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }

        tbody tr:hover {
            transform: translateY(-2px);
        }

        /* Style for dropdown content */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            padding: 10px;
        }

        .dropdown-content ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .dropdown-content li {
            margin-bottom: 5px;
        }

        .dropdown-content li span {
            font-weight: bold;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="images/logob21.png" alt="Logo" width="200">
            <h1>Received Orders</h1>
        </header>

        <main>
            <div class="orders">
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Price</th>
                            <th>Mobile</th>
                            <th>Address</th>
                            <th>To Pay</th>
                            <th>Order Date</th>
                            <th>Action</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       
                       
                        // Connect to the database
                        $conn = mysqli_connect("localhost", "root", "", "shiddh");

                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        // Fetch orders from database ordered by Date in descending order
                        $query = "SELECT * FROM orderscustomer ORDER BY send_date DESC";
                        $result = mysqli_query($conn, $query);

                        // Check if query executed successfully
                        if (!$result) {
                            die("Query failed: " . mysqli_error($conn));
                        }

                        // Initialize variables to track previous mobile number
                        $prev_mobile = null;
                        $dropdown_open = false;

                        // Check if there are any orders
                        if (mysqli_num_rows($result) > 0) {
                            // Output data of each row
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['itemName']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['mobileNumber']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['paymentMethod']) . "</td>";
                                echo "<td>" . htmlspecialchars(date('d-m-Y', strtotime($row['orderDate']))) . "</td>";
                                echo "<td>";

                                // Check if current mobile number is the same as previous
                                if ($row['mobileNumber'] === $prev_mobile) {
                                    // If same, add to existing dropdown
                                    echo "<div class='dropdown'>";
                                    echo "<button class='details-btn' onclick='toggleDropdown(this)'>Details &#9660;</button>";
                                    echo "<div class='dropdown-content' id='dropdown_" . $row['mobileNumber'] . "'>";
                                    echo "<ul>";
                                    echo "<li><span>Item Name:</span>" . htmlspecialchars($row['itemName']) . "</li>";
                                    echo "<li><span>Price:</span>" . htmlspecialchars($row['price']) . "</li>";
                                    echo "</ul>";
                                    echo "</div>";
                                    echo "</div>";
                                } else {
                                    // If different, close previous dropdown (if any) and start new button
                                    if ($dropdown_open) {
                                        echo "</div>"; // close previous dropdown
                                    }
                                    echo "<div class='dropdown'>";
                                    echo "<button class='details-btn' onclick='toggleDropdown(this)'>Details &#9660;</button>";
                                    echo "<div class='dropdown-content' id='dropdown_" . $row['mobileNumber'] . "'>";
                                    echo "<ul>";
                                    echo "<li><span>Item Name:</span>" . htmlspecialchars($row['itemName']) . "</li>";
                                    echo "<li><span>Price:</span>" . htmlspecialchars($row['price']) . "</li>";
                                    echo "</ul>";
                                    echo "</div>";
                                    echo "</div>";
                                    $dropdown_open = true;
                                }

                                echo "</td>";
                                echo "</tr>";

                                // Update previous mobile number
                                $prev_mobile = $row['mobileNumber'];
                            }

                            // Close last dropdown if any
                            if ($dropdown_open) {
                                echo "</div>"; // close last dropdown
                            }
                        } else {
                            echo "<tr><td colspan='7' class='no-orders'>No orders found.</td></tr>";
                        }

                        // Close database connection
                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function toggleDropdown(btn) {
            var dropdownContent = btn.nextElementSibling;
            dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
        }
    </script>
</body>
</html>
