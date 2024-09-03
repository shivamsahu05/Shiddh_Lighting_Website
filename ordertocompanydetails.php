<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shiddh";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order ID from session
$orderId = isset($_SESSION['orderId']) ? intval($_SESSION['orderId']) : 0;

// Fetch order details
$sql = "SELECT * FROM orderbycompany WHERE ordernum = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();

// Check if order data was returned
if (!$order) {
    die("No order found with ID: " . htmlspecialchars($orderId));
}

// Fetch order items
$sqlItems = "SELECT * FROM ordertocmpdetails WHERE ordernum = ?";
$stmtItems = $conn->prepare($sqlItems);
if (!$stmtItems) {
    die("Prepare failed: " . $conn->error);
}
$stmtItems->bind_param("i", $orderId);
$stmtItems->execute();
$itemsResult = $stmtItems->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5 mb-4">Order Details - Order ID: <?php echo htmlspecialchars($orderId); ?></h1>
        
        <div class="mb-4">
            <h4>Order Information</h4>
            <?php
            $orderDate = new DateTime($order['orderDate']);
            $formattedDate = $orderDate->format('d-m-Y');
            ?>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($formattedDate); ?></p>
            <p><strong>Mode:</strong> <?php echo htmlspecialchars($order['mode'] ?? 'N/A'); ?></p>
            <p><strong>To Company:</strong> <?php echo htmlspecialchars($order['ordertocmp'] ?? 'N/A'); ?></p>
            <p><strong>Amount:</strong> <?php echo htmlspecialchars($order['sub_amount'] ?? 'N/A'); ?></p>
            <p><strong>Total Amount:</strong> <?php echo htmlspecialchars($order['totalamount'] ?? 'N/A'); ?></p>
            <p style="font-weight: bold; color:red;"><strong>Total Amount with GST:</strong> <?php echo htmlspecialchars($order['totalamtwithgst'] ?? 'N/A'); ?> ₹/-</p>
        </div>
        
        <h4>Order Items</h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Item Name</th>
                    <th scope="col">Description</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Price</th>
                    <th scope="col">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($itemsResult->num_rows > 0) {
                    $displayed = false;
                    $serialNumber = 1;
                    while ($item = $itemsResult->fetch_assoc()) {
                        if (!empty($item['itemname']) && !empty($item['qty'])) {
                            $displayed = true;
                            echo "<tr>
                                <td>" . $serialNumber++ . "</td>
                                <td>" . htmlspecialchars($item['itemname']) . "</td>
                                <td>" . htmlspecialchars($item['description']) . "</td>
                                <td>" . htmlspecialchars($item['qty']) . "</td>
                                <td>" . htmlspecialchars($item['price']) . " ₹/-</td>
                                <td>" . htmlspecialchars($item['amount']) . " ₹</td>
                            </tr>";
                        }
                    }
                    if (!$displayed) {
                        echo "<tr><td colspan='6'>No items found</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No items found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="adminordertocompany.php" class="btn btn-primary mt-4">Back to Orders</a>
    </div>
</body>
</html>

<?php
// Close connection
$stmt->close();
$stmtItems->close();
$conn->close();
?>
