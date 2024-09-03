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

// Initialize variables
$phone = $shopName = $customerName = $newPhone = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['UpdatePhone']) && isset($_POST['NewPhone'])) {
        // Retrieve and sanitize input
        $phone = mysqli_real_escape_string($con, $_POST['UpdatePhone']);
        $newPhone = mysqli_real_escape_string($con, $_POST['NewPhone']);
        $shopName = mysqli_real_escape_string($con, $_POST['ShopName']);
        $customerName = mysqli_real_escape_string($con, $_POST['CustomerName']);
        
        // Update the phone number in the bills table where customer name and shop name match
        $updateQuery = "UPDATE bills SET Phone = ? WHERE ShopName = ? AND Customer_name = ?";
        $stmt = mysqli_prepare($con, $updateQuery);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $newPhone, $shopName, $customerName);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                // Display success message and redirect
                echo "<script>
                        alert('Phone number updated successfully.');
                        window.location.href = 'document.html'; // Update with the actual path
                      </script>";
            } else {
                echo "<p>Error updating record: " . mysqli_error($con) . "</p>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<p>Error preparing statement: " . mysqli_error($con) . "</p>";
        }

        // Close connection
        mysqli_close($con);
    }
} else if (isset($_GET['phone'])) {
    // Fetch existing details based on the phone number provided via GET
    $phone = mysqli_real_escape_string($con, $_GET['phone']);
    $query = "SELECT ShopName, Customer_name FROM bills WHERE Phone = ?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $phone);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $shopName = $row['ShopName'];
            $customerName = $row['Customer_name'];
        } else {
            echo "<p>No record found with the provided phone number.</p>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<p>Error preparing statement: " . mysqli_error($con) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Phone Number</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .update-form {
            margin-top: 20px;
        }
        .update-form label {
            display: block;
            margin: 10px 0 5px;
        }
        .update-form input[type="text"], .update-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Phone Number</h1>
        <!-- Form for updating phone number -->
        <div class="update-form">
            <form action="" method="POST">
                <input type="hidden" name="UpdatePhone" value="<?php echo htmlspecialchars($phone); ?>" />
                <label for="NewPhone">New Phone Number:</label>
                <input type="text" id="NewPhone" name="NewPhone" required />
                <label for="ShopName">Shop Name:</label>
                <input type="text" id="ShopName" name="ShopName" value="<?php echo htmlspecialchars($shopName); ?>" required readonly/>
                <label for="CustomerName">Customer Name:</label>
                <input type="text" id="CustomerName" name="CustomerName" value="<?php echo htmlspecialchars($customerName); ?>" required readonly />
                <input type="submit" value="Update" />
            </form>
        </div>

        <div class="btn-container">
            <a href="trial.php" class="btn">Go Back</a>
        </div>
    </div>
</body>
</html>
