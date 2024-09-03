<?php
error_reporting(E_ALL);
session_start();

// Set the default timezone to IST
date_default_timezone_set('Asia/Kolkata');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Database connection function
function connectDB() {
    $conn = mysqli_connect("localhost", "root", "", "shiddh");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

// Fetch user details function
function fetchUserDetails($username) {
    $conn = connectDB();
    $sql = "SELECT FullName, Address, SHOPName, Phone FROM registration WHERE FullName = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("MySQL prepare error: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return $row;
    } else {
        mysqli_close($conn);
        return null;
    }
}

// Function to fetch next order number
function getNextOrderNumber() {
    $conn = connectDB();
    $sql = "SELECT MAX(ordernum) AS max_order_num FROM orders";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("MySQL query error: " . mysqli_error($conn));
    }
    $row = mysqli_fetch_assoc($result);
    $next_order_num = $row['max_order_num'] + 1;
    mysqli_close($conn);
    return $next_order_num;
}

// Fetch logged-in user's details
$username = $_SESSION['username'];
$userDetails = fetchUserDetails($username);
if ($userDetails) {
    $fullName = htmlspecialchars($userDetails['FullName']);
    $address = htmlspecialchars($userDetails['Address']);
    $shopName = htmlspecialchars($userDetails['SHOPName']);
    $mobileNumber = htmlspecialchars($userDetails['Phone']);
} else {
    echo "User details not found.";
    exit();
}

// Initialize order number
$order_num = getNextOrderNumber();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = connectDB();

    // Validate and sanitize inputs
    $errors = [];
    $items = isset($_POST['item_nm']) ? $_POST['item_nm'] : [];
    $qtys = isset($_POST['qty']) ? $_POST['qty'] : [];
    $descriptions = isset($_POST['discription']) ? $_POST['discription'] : [];
    $order_num = isset($_POST['order_id']) ? htmlspecialchars($_POST['order_id']) : '';

    // Check if arrays are not empty and have the same length
    if (empty($items) || count($items) !== count($qtys) || count($qtys) !== count($descriptions)) {
        $errors[] = "Please fill in all item details.";
    }

    if (empty($order_num)) {
        $errors[] = "Order Number is required.";
    }

    if (empty($errors)) {
        $currentDate = date('Y-m-d H:i:s'); // Current date and time

        $insert_sql = "INSERT INTO orders (ordernum, Mobile_Num, CustomerName, ShopName, Address, Date) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        if (!$stmt) {
            die("MySQL prepare error: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "isssss", $order_num, $mobileNumber, $fullName, $shopName, $address, $currentDate);

        if (mysqli_stmt_execute($stmt)) {
            $last_insert_id = mysqli_insert_id($conn);

            // Loop through each item to insert into 'orderdetails' table
            for ($x = 1; $x < count($items); $x++) {
                $item = htmlspecialchars($items[$x]);
                $q = htmlspecialchars($qtys[$x]);
                $d = htmlspecialchars($descriptions[$x]);

                $query_billdetails = "INSERT INTO orderdetails (ordernum, items, qtys, discription) VALUES (?, ?, ?, ?)";
                $stmt_details = mysqli_prepare($conn, $query_billdetails);
                if (!$stmt_details) {
                    die("MySQL prepare error: " . mysqli_error($conn));
                }
                mysqli_stmt_bind_param($stmt_details, "isss", $order_num, $item, $q, $d);

                if (!mysqli_stmt_execute($stmt_details)) {
                    $errors[] = "Error inserting into orderdetails: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt_details);
            }

            if (empty($errors)) {
                echo '<script> alert("Your Order Saved Successfully")</script>';
            } else {
                foreach ($errors as $error) {
                    echo '<script> alert("' . $error . '")</script>';
                }
            }
        } else {
            echo '<script> alert("Error inserting into orders: ' . mysqli_error($conn) . '")</script>';
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        foreach ($errors as $error) {
            echo '<script> alert("' . $error . '")</script>';
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        .input-group-text {
            width: 140px;
        }
        .text_line{
            width: 430px;
        }
        @media print 
        {

.btn {
    display: none;
}
.back {
    display: none;
}
.home {
    display: none;
}

.NoPrint {
    display: none;
}

.form-control {
    display: 0px;

}
.form-control_text-end{
    display: 0;
   
}

.input-group-text {
    display: 0px;
    /* background-color: white; */
    background: #ffe9e9;
}


}
    </style>
    <script>
       $(document).ready(function() {
    const currentDate = new Date();
    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Month is zero-based
    const day = String(currentDate.getDate()).padStart(2, '0');
    const hours = String(currentDate.getHours()).padStart(2, '0');
    const minutes = String(currentDate.getMinutes()).padStart(2, '0');
    const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`; // Formats date as YYYY-MM-DDTHH:MM
    $("input[name='inv_d']").val(formattedDate);
});

-

        function GetPrint() {
            window.print();
        }

        function BtnAdd() {
            var v = $("#TRow").clone().appendTo("#TBody");
            $(v).find("input").val('');
            $(v).removeClass("d-none");
            $(v).find("th").first().html($('#TBody tr').length - 1);
        }

        function BtnDel(v) {
            $(v).parent().parent().remove();
            // Adjust row numbers
            $("#TBody").find("tr").each(function(index) {
                $(this).find("th").first().html(index);
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <form method="POST" action="">
            <img src="images/logoinv2.jpg" alt="image" width="250">
            <div class="card">
                <div class="card-header text-center">
                    <h4 style="font-family: 'Times New Roman', Times, serif;"><u><b>Take My Order</b></u></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Name</span>
                                <input type="text" class="form-control" placeholder="Your name" name="CUST_nm" style="font-weight: bold" value="<?php echo isset($fullName) ? $fullName : ''; ?>" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text " id="basic-addon1">Address</span>
                                <input type="text" class="form-control" placeholder="Address" name="addr" value="<?php echo isset($address) ? $address : ''; ?>" required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Shop Name</span>
                                <input type="text" class="form-control" placeholder="Shop Name" name="s_Name" value="<?php echo isset($shopName) ? $shopName : ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Mobile Number</span>
                                <input type="number" class="form-control" placeholder="Mobile Number" name="num" value="<?php echo isset($mobileNumber) ? $mobileNumber : ''; ?>" required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Invoice Date</span>
                                <input type="datetime-local" class="form-control" placeholder="Invoice Date" name="inv_d" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Order Num</span>
                                <input type="number" class="form-control" name="order_id" value="<?php echo $order_num; ?>" readonly required>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text_line" >Item Name</th>
                                <th scope="col" class="text-end" style="font-family: 'Times New Roman', Times, serif;">Qty</th>
                                <th scope="col" class="text-end" style="font-family: 'Times New Roman', Times, serif;">Description</th>
                                <th scope="col" class="NoPrint"><button type="button" class="btn btn-warning" onclick="BtnAdd()">+</button></th>
                            </tr>
                        </thead>
                        <tbody id="TBody">
                            <tr id="TRow" class="d-none">
                                <th scope="row"></th>
                                <td><input type="text" class="form-control" name="item_nm[]" ></td>
                                <td><input type="number" class="form-control text-end" name="qty[]"></td>
                                <td><input type="text" class="form-control text-end" name="discription[]"></td>
                                <td class="NoPrint"><button type="button" class="btn btn-danger" onclick="BtnDel(this)">✕</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row-8">
                        <button type="submit" class="btn btn-primary" name="btn1">SUBMIT</button>
                        <!-- <button type="button" class="btn btn-primary" onclick="GetPrint()">PRINT</button> -->
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
