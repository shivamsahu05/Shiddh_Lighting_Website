<?php
// Database connection
$con = mysqli_connect("localhost", "root", "", "shiddh");

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $mobile = mysqli_real_escape_string($con, $_GET['id']);

    // Fetch existing details
    $query = "SELECT * FROM registration WHERE C_mobile='$mobile'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Error in query: " . mysqli_error($con));
    }

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "User not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $shopName = mysqli_real_escape_string($con, $_POST['shopName']);
    $fullName = mysqli_real_escape_string($con, $_POST['fullName']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $pincode = mysqli_real_escape_string($con, $_POST['pincode']);

    // Update user details
    $updateQuery = "UPDATE registration SET ShopName='$shopName', FullName='$fullName', Gender='$gender', Address='$address', Email='$email', Pincode='$pincode' WHERE C_mobile='$mobile'";

    if (mysqli_query($con, $updateQuery)) {
        echo "User details updated successfully.";
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}

// Close connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #333;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #444;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
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
        <h1>Update User Details</h1>
        <form action="update_users.php?id=<?php echo htmlspecialchars($mobile); ?>" method="POST">
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

            <input type="submit" value="Update">
        </form>
    </div>
</body>
</html>
