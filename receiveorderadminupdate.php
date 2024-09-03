<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $order_id = isset($_POST['ord_id']) ? $_POST['ord_id'] : '';
    $items = isset($_POST['items']) ? $_POST['items'] : '';
    $qtys = isset($_POST['qtys']) ? $_POST['qtys'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Prepare update query
    $query = "UPDATE orderdetails SET items=?, qtys=?, discription=? WHERE ord_id=?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "sssi", $items, $qtys, $description, $order_id);

    // Execute statement
    if (mysqli_stmt_execute($stmt)) {
        // Redirect back to the order details page after successful update
        if (isset($_GET['ordernum'])) {
            $ordernum = $_GET['ordernum'];
            header("Location: order_details.php?ordernum=$ordernum");
            exit();
        } else {
            die("Error: ordernum parameter not specified.");
        }
    } else {
        $error_message = "Error updating item details: " . mysqli_stmt_error($stmt);
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Item Details</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
        text-transform: uppercase;
    }
    form {
        margin-bottom: 20px;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }
    input[type="text"], textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    input[type="submit"]:hover {
        background-color: #45a049;
    }
    .error {
        color: red;
        font-weight: bold;
        margin-bottom: 10px;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Update Item Details</h2>

    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="ord_id" value="<?php echo isset($_POST['ord_id']) ? htmlspecialchars($_POST['ord_id']) : ''; ?>">
        
        <label for="items">Item Name:</label>
        <input type="text" id="items" name="items" value="<?php echo isset($_POST['items']) ? htmlspecialchars($_POST['items']) : ''; ?>" required>
        
        <label for="qtys">Quantity:</label>
        <input type="text" id="qtys" name="qtys" value="<?php echo isset($_POST['qtys']) ? htmlspecialchars($_POST['qtys']) : ''; ?>" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
        
        <input type="submit" value="Update">
    </form>

    <?php if (isset($_GET['ordernum'])): ?>
        <p><a href="receiveorder.php?ordernum=<?php echo $_GET['ordernum']; ?>">Back to Order Details</a></p>
    <?php endif; ?>
</div>

</body>
</html>
