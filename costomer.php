<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search and Update Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #333;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #444;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        #search-form {
            text-align: center;
            margin-bottom: 20px;
        }

        #search-form input[type="text"] {
            height: 30px;
            padding: 5px 10px;
            font-size: 16px;
            border: none;
            background-color: #ccc;
            border-radius: 4px;
            width: 300px;
            margin-right: 10px;
        }

        #search-form input[type="submit"] {
            height: 40px;
            padding: 0 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        #search-form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .table-container {
            overflow-y: auto;
            max-height: 400px; /* Adjust the height as needed */
            border: 1px solid #666;
            border-radius: 4px;
            background-color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #666;
        }

        th {
            background-color: #ffc107;
            color: #333;
            position: sticky;
            top: 0;
            z-index: 2; /* Ensure the header stays above the scrollable content */
        }

        td {
            background-color: #555;
            color: #fff;
        }

        td a {
            display: inline-block;
            padding: 6px 12px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        td a:hover {
            background-color: #0056b3;
        }

        td a + a {
            margin-left: 5px;
        }

        input[type="text"], input[type="email"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            background-color: #ccc;
            color: #333;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="images/logosl.png" alt="Logo" width="200">
            <h1>Search and Update Orders</h1>
        </header>

        <!-- Search Form -->
        <form id="search-form" action="" method="POST">
            <input type="text" name="searchTerm" placeholder="Search by Name, Shop Name, or Address">
            <input type="submit" name="search" value="Search">
        </form>

        <?php
        session_start(); // Start the session at the beginning of the script

        // Check if the user is not logged in, redirect to login page
        if (!isset($_SESSION['username'])) {
            header('Location: login.php'); // Redirect to login page if not logged in
            exit;
        }

        $con = mysqli_connect("localhost", "root", "", "shiddh");

        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        if (isset($_POST['search'])) {
            $searchTerm = mysqli_real_escape_string($con, $_POST['searchTerm']);
            $query = "SELECT * FROM registration WHERE FullName LIKE '%$searchTerm%' OR ShopName LIKE '%$searchTerm%' OR Address LIKE '%$searchTerm%' ORDER BY FullName ASC";
            $result = mysqli_query($con, $query);

            if (!$result) {
                die("Error in query: " . mysqli_error($con));
            }

            if (mysqli_num_rows($result) > 0) {
                echo "<div class='table-container'>";
                echo "<table>";
                echo "<tr>";
                echo "<th>#</th>"; // Added Serial Number Column
                echo "<th>C_Name</th>";
                echo "<th>C_Phone</th>";
                echo "<th>C_ShopName</th>";
                echo "<th>Shop_Address</th>";
                echo "<th>Actions</th>";
                echo "</tr>";

                $serialNo = 1; // Initialize Serial Number
                while ($row = mysqli_fetch_assoc($result)) {
                    $mobile = htmlspecialchars($row['Phone']);
                    echo "<tr>";
                    echo "<td>" . $serialNo++ . "</td>"; // Display Serial Number
                    echo "<td>" . htmlspecialchars($row['FullName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ShopName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
                    echo "<td>";
                    // Use form with hidden input for update link
                    echo "<form action='' method='POST' style='display:inline;'>";
                    echo "<input type='hidden' name='mobile' value='$mobile'>";
                    echo "<input type='submit' name='edit' value='Update'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }

                echo "</table>";
                echo "</div>";
            } else {
                echo "<p>No results found.</p>";
            }
        }

        if (isset($_POST['edit'])) {
            $mobile = mysqli_real_escape_string($con, $_POST['mobile']);

            // Fetch existing details
            $query = "SELECT * FROM registration WHERE Phone='$mobile'";
            $result = mysqli_query($con, $query);

            if (!$result) {
                die("Error in query: " . mysqli_error($con));
            }

            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                ?>

                <!-- Update Form -->
                <h2>Update User Details</h2>
                <form action="" method="POST">
                    <input type="hidden" name="mobile" value="<?php echo htmlspecialchars($user['Phone']); ?>">

                    <label for="shopName">Shop Name:</label>
                    <input type="text" id="shopName" name="shopName" value="<?php echo htmlspecialchars($user['ShopName']); ?>" required>

                    <label for="fullName">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user['FullName']); ?>" required>

                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="Male" <?php echo $user['Gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $user['Gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $user['Gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['Address']); ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>

                    <label for="pincode">Pincode:</label>
                    <input type="number" id="pincode" name="pincode" value="<?php echo htmlspecialchars($user['Pincode']); ?>" required>

                    <input type="submit" name="update" value="Update">
                </form>

                <?php
            } else {
                echo "User not found.";
            }
        }

        if (isset($_POST['update'])) {
            $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
            $shopName = mysqli_real_escape_string($con, $_POST['shopName']);
            $fullName = mysqli_real_escape_string($con, $_POST['fullName']);
            $gender = mysqli_real_escape_string($con, $_POST['gender']);
            $address = mysqli_real_escape_string($con, $_POST['address']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $pincode = mysqli_real_escape_string($con, $_POST['pincode']);

            // Update user details
            $updateQuery = "UPDATE registration SET ShopName='$shopName', FullName='$fullName', Gender='$gender', Address='$address', Email='$email', Pincode='$pincode' WHERE Phone='$mobile'";

            if (mysqli_query($con, $updateQuery)) {
                echo '<script>alert("User details updated successfully.")</script>';
            } else {
                echo "Error updating record: " . mysqli_error($con);
            }
        }

        // Close connection
        mysqli_close($con);
        ?>
    </div>
</body>
</html>
