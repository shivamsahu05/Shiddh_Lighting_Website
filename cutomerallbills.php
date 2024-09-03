<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Establish connection
$con = mysqli_connect("localhost", "root", "", "shiddh");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Handle form submission for updating customer details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Phone'])) {
    // Retrieve and sanitize input
    $phone = mysqli_real_escape_string($con, $_POST['Phone']);
    $customer_name = mysqli_real_escape_string($con, $_POST['Customer_name']);
    $shop_name = mysqli_real_escape_string($con, $_POST['ShopName']);
    $address = mysqli_real_escape_string($con, $_POST['Address']);

    // Prepare and execute update statement
    $query = "UPDATE bills SET Customer_name = ?, ShopName = ?, Address = ? WHERE Phone = ?";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "ssss", $customer_name, $shop_name, $address, $phone);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        // Fetch updated details for display
        $query = "SELECT Customer_name, ShopName, Address FROM bills WHERE Phone = ?";
        $stmt = mysqli_prepare($con, $query);
        
        mysqli_stmt_bind_param($stmt, "s", $phone);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        // Display success message with updated details
        echo "<script>
                alert('Customer details updated successfully.\\nCustomer Name: " . htmlspecialchars($row['Customer_name']) . "\\nShop Name: " . htmlspecialchars($row['ShopName']) . "\\nAddress: " . htmlspecialchars($row['Address']) . "');
                window.location.href = 'ducument.html'; // Update with the actual path
              </script>";
    } else {
        echo "<p>Error updating customer details: " . mysqli_error($con) . "</p>";
    }

    mysqli_stmt_close($stmt);
}

// Handle form display
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['Phone']) && isset($_POST['UpdatePhone'])) {
    $phone = mysqli_real_escape_string($con, $_POST['UpdatePhone']);

    // Fetch existing customer details based on the phone number
    $query = "SELECT Customer_name, ShopName, Address FROM bills WHERE Phone = ?";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "s", $phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Update Customer Details</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .form-group {
                    margin-bottom: 15px;
                }
                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: bold;
                }
                .form-group input[type="text"], .form-group input[type="submit"] {
                    width: 100%;
                    padding: 10px;
                    box-sizing: border-box;
                }
                .form-group input[type="submit"] {
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    cursor: pointer;
                }
                .form-group input[type="submit"]:hover {
                    background-color: #45a049;
                }
            </style>
            <script>
                function confirmUpdate() {
                    return confirm("Are you sure you want to update these details?");
                }
            </script>
        </head>
        <body>
        <div class="container">
        <img src="images/logoinv2.jpg" alt="image" width="250">

            <h1>Update Customer Details</h1>
            <form action="" method="POST" onsubmit="return confirmUpdate();">
                <!-- Hidden field to store the phone number -->
                <input type="hidden" name="Phone" value="<?php echo htmlspecialchars($phone); ?>">
                <div class="form-group">
                    <label for="Customer_name">Customer Name:</label>
                    <input type="text" id="Customer_name" name="Customer_name" value="<?php echo htmlspecialchars($row['Customer_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="ShopName">Shop Name:</label>
                    <input type="text" id="ShopName" name="ShopName" value="<?php echo htmlspecialchars($row['ShopName']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="Address">Address:</label>
                    <input type="text" id="Address" name="Address" value="<?php echo htmlspecialchars($row['Address']); ?>" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Update">
                </div>
            </form>
        </div>
        </body>
        </html>
        <?php
    } else {
        echo "No customer found with that phone number.";
    }
    
    mysqli_stmt_close($stmt);
} else {
    // Display the list of customers
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Unique Customers List</title>
        <style>
            body {
                background-color: #1a1a1a;
                color: white;
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            .table-container {
                max-height: 500px; /* Adjust the height as needed */
                overflow-y: auto;
                border: 2px solid #b1b1b1;
                background-color: #333;
                margin-top: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table th, table td {
                border: 1px solid #b1b1b1;
                padding: 10px;
                text-align: center;
                font-weight: normal;
            }
            table th {
                background-color: #b1b1b1;
                color: black;
                position: sticky;
                top: 0;
                z-index: 1; /* Ensures header stays on top */
            }
            table tr:nth-child(even) {
                background-color: #555;
            }
            table tr:hover {
                background-color: #777;
            }
            img.logo {
                display: block;
                margin: 20px auto;
                width: 200px;
            }
        </style>
    </head>
    <body>
        <!-- Logo Image -->
        <img src="images/logosl.png" alt="Logo" class="logo">

        <div class="table-container">
            <table>
                <?php
                
               
                // Query to fetch unique customer records based on mobile number
                $query = "SELECT Customer_name, ShopName, Address, Phone
                          FROM bills
                          WHERE Phone IS NOT NULL
                          GROUP BY Phone
                          ORDER BY Customer_name ASC";

                $result = mysqli_query($con, $query);

                // Check if query execution is successful
                if (!$result) {
                    echo "Error: " . mysqli_error($con);
                    exit();
                }

                // Table headers
                echo "<thead>";
                echo "<tr>";
                echo "<th>#</th>"; // Serial Number Column
                echo "<th>Customer Name</th>";
                echo "<th>Shop Name</th>";
                echo "<th>Address</th>";
                echo "<th>Mobile Number</th>";
                echo "<th>Update Customer</th>";
                echo "<th>Update Mobile</th>";
                echo "</tr>";
                echo "</thead>";

                // Table body
                echo "<tbody>";

                // Initialize serial number counter
                $serialNumber = 1;

                // Loop through each row and display data in table rows
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $serialNumber . "</td>"; // Display serial number
                    echo "<td>" . htmlspecialchars($row['Customer_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ShopName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                    echo "<td>";
                    echo "<form action='' method='POST'>";
                    echo "<input type='hidden' name='UpdatePhone' value='" . htmlspecialchars($row['Phone']) . "' />";
                    echo "<input type='submit' value='Update' />";
                    echo "</form>";
                    echo "</td>";
                    echo "<td>";
                    echo "<form action='updatecutomerphone.php' method='POST'>";
                    echo "<input type='hidden' name='UpdatePhone' value='" . htmlspecialchars($row['Phone']) . "' />"; // Phone number
                    echo "<input type='hidden' name='ShopName' value='" . htmlspecialchars($row['ShopName']) . "' />"; // Shop Name
                    echo "<input type='hidden' name='CustomerName' value='" . htmlspecialchars($row['Customer_name']) . "' />"; // Customer Name
                    echo "<input type='submit' value='UpdateM' />";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";

                    $serialNumber++; // Increment serial number for the next row
                }

                echo "</tbody>";
                echo "</table>";
                ?>
            </table>
        </div>
    </body>
    </html>
    <?php
}

// Close connection
mysqli_close($con);
?>
