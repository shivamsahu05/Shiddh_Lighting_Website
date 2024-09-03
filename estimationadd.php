<?php
error_reporting(E_ALL);
session_start();
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
// Database connection function
function connectDB() {
    $conn = mysqli_connect("localhost", "root", "", "shiddh");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

// Function to fetch next order number
function getNextOrderNumber() {
    $conn = connectDB();
    $sql = "SELECT MAX(ordernum) AS max_order_num FROM orders";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $next_order_num = $row['max_order_num'] + 1; // Increment the maximum order number
    mysqli_close($conn);
    return $next_order_num;
}

// Initialize order number
$order_num = getNextOrderNumber();

// Process form submission
if (isset($_POST["btn1"])) {
    $conn = connectDB();

    $fullName = $_POST['CustomerName'];
    $address = $_POST['Address'];
    $shopName = $_POST['ShopName'];
    $mobileNumber = $_POST['Mobile_Num'];
    $currentDate = date('Y-m-d'); // Format date as YYYY-MM-DD for MySQL

    $items = isset($_POST['item_nm']) ? $_POST['item_nm'] : [];
    $qtys = isset($_POST['qty']) ? $_POST['qty'] : [];
    $descriptions = isset($_POST['discription']) ? $_POST['discription'] : [];

    // Validate required fields
    $errors = [];
    $mobileNumber = preg_replace('/\D/', '', $mobileNumber); // Remove non-digit characters
    if (strlen($mobileNumber) !== 10) {
        $errors[] = "Mobile Number must be exactly 10 digits.";
    }

    if (empty($errors)) {
        // Insert into 'orders' table
        $insert_sql = "INSERT INTO orders (ordernum, Mobile_Num, CustomerName, ShopName, Address, Date) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "isssss", $order_num, $mobileNumber, $fullName, $shopName, $address, $currentDate);

        if (mysqli_stmt_execute($stmt)) {
            $last_insert_id = mysqli_insert_id($conn); // Get the last inserted ID of 'orders' table

            // Insert into 'orderdetails' table
            for ($x = 0; $x < count($items); $x++) {
                $item = htmlspecialchars($items[$x]);
                $qty = htmlspecialchars($qtys[$x]);
                $description = htmlspecialchars($descriptions[$x]);

                $query_billdetails = "INSERT INTO orderdetails (ordernum, items, qtys, discription) 
                                      VALUES (?, ?, ?, ?)";
                $stmt_details = mysqli_prepare($conn, $query_billdetails);
                mysqli_stmt_bind_param($stmt_details, "isss", $order_num, $item, $qty, $description);

                if (!mysqli_stmt_execute($stmt_details)) {
                    $errors[] = "Error inserting into orderdetails: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt_details);
            }

            if (empty($errors)) {
                echo '<script> alert("Data Saved Successfully")</script>';
            } else {
                foreach ($errors as $error) {
                    echo '<script> alert("' . $error . '")</script>';
                }
            }
        } else {
            echo '<script> alert("Error inserting into orders: ' . mysqli_error($conn) . '")</script>';
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        foreach ($errors as $error) {
            echo '<script> alert("' . $error . '")</script>';
        }
    }

    // Close connection
    mysqli_close($conn);
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
        .text_lne{
            width: 320px;
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
}  }
    </style>
    <script>
   function validateForm() {
        var mobileNumber = document.querySelector("input[name='Mobile_Num']").value;
        
        // Remove non-digit characters and check length
        mobileNumber = mobileNumber.replace(/\D/g, '');
        
        if (mobileNumber.length !== 10) {
            alert("Mobile Number must be exactly 10 digits.");
            return false; // Prevent form submission
        }
        
        return true; // Allow form submission
    }

        $(document).ready(function() {
            const currentDate = new Date();
            const day = String(currentDate.getDate()).padStart(2, '0');
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const year = currentDate.getFullYear();
            const formattedDate = `${day}-${month}-${year}`; // Formats date as DD-MM-YYYY
            $("input[name='Date']").val(formattedDate);
        });

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
    <form method="POST" action="" onsubmit="return validateForm()">

            <img src="images/logoinv2.jpg" alt="image" width="250">
            <div class="card">
                <div class="card-header text-center">
                    <h4 style="font-family: 'Times New Roman', Times, serif;"><b>!! <u>Estimation</u> !!</b></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Name</span>
                                <input type="text" class="form-control" placeholder="Cutomer name" name="CustomerName" style="font-weight: bold" value="<?php echo isset($fullName) ? $fullName : ''; ?>" >
                            </div>
                           
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Shop Name</span>
                                <input type="text" class="form-control" placeholder="Shop Name" name="ShopName" value="<?php echo isset($shopName) ? $shopName : ''; ?>" >
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text " id="basic-addon1">Address</span>
                                <input type="text" class="form-control" placeholder="Address" name="Address" value="<?php echo isset($address) ? $address : ''; ?>" >
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Mobile Number</span>
                                <input type="text" class="form-control" placeholder="Mobile Number" name="Mobile_Num" maxlength="10" pattern="\d{10}" title="Please enter exactly 10 digits" required  >
                               
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Order Date</span>
                                <input type="text" class="form-control" placeholder="Order Date" name="Date" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Order Number</span>
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
                                <th scope="col" class="text_lne" style="font-family: 'Times New Roman', Times, serif;">Description</th>
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
                    <button type="button" class="btn btn-primary" onclick="GetPrint()">PRINT</button>
                        <button type="submit" class="btn btn-primary" name="btn1">SUBMIT</button>
                       
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
