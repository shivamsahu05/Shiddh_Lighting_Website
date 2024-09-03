<?php
session_start();
// Check if the user is not logged in, redirect to login page
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

// Handle setting the session variable
if (isset($_POST['orderId'])) {
    $_SESSION['orderId'] = intval($_POST['orderId']);
    header('Location: trial.php');
    exit;
}

// Fetch orders
$sql = "SELECT * FROM orderbycompany ORDER BY ordernum DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script>
        function setOrderId(orderId) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'orderId=' + orderId
            }).then(response => response.text())
              .then(() => {
                  window.location.href = 'ordertocompanydetails.php'; // Redirect to details page
              })
              .catch(error => {
                  console.error('Error setting order ID:', error);
              });
        }
        function setOrderIdtial(orderId) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'orderId=' + orderId
            }).then(response => response.text())
              .then(() => {
                  window.location.href = 'adminordertocompanyupdate.php'; // Redirect to details page   adminordertocompanyupdate.php
              })
              .catch(error => {
                  console.error('Error setting order ID:', error);
              });
        }
    </script>
    <style>
        /* Custom CSS */
        .container {
            margin-top: 20px;
        }
        .table {
            margin-top: 20px;
        }
        .table thead th {
            text-align: center;
        }
        .table tbody td {
            text-align: center;
        }
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            .table th, .table td {
                white-space: nowrap;
            }
        }
        @media (max-width: 576px) {
            .btn {
                font-size: 12px;
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5 mb-4"> <u>Order List To Company</u></h1>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">To Company</th>
                        <th scope="col">Mode</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Total Amount</th>
                        <th scope="col">Net Amount</th>
                        <th scope="col">Actions</th>
                        <th scope="col">Update</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $ordernum = $row['ordernum'];
                            $orderDate = new DateTime($row['orderDate']);
                            $formattedDate = $orderDate->format('d-m-Y');
                            
                            echo "<tr>
                                <td>{$ordernum}</td>
                                <td>{$formattedDate}</td>
                                <td>{$row['ordertocmp']}</td>
                                <td>{$row['mode']}</td>
                                <td>{$row['sub_amount']}</td>
                                <td>{$row['totalamount']}</td>
                                <td style='font-weight: bold; color:red;'>{$row['totalamtwithgst']}</td>
                                <td>
                                    <a href='#' class='btn btn-info btn-sm' onclick='setOrderId({$ordernum})'>View</a>
                                </td>
                                  <td>
                                    <a href='#' class='btn btn-warning btn-sm' onclick='setOrderIdtial({$ordernum})'>Update</a>
                                </td>
                                <td>
                                    <a href='delete_order.php?id={$ordernum}' class='btn btn-danger btn-sm'>Delete</a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
