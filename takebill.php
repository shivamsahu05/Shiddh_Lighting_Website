<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Fetch customer details based on mobile number
if (isset($_POST['mobileNumber'])) {
    $mobileNumber = mysqli_real_escape_string($conn, $_POST['mobileNumber']);
    $query = "SELECT Customer_name, Address, ShopName FROM bills WHERE Phone = '$mobileNumber'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No data found']);
    }
    mysqli_close($conn);
    exit;
}

// Handle invoice form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn1'])) {
    // Form data
    $i = $_POST['inv_n'];
    $a = $_POST['CUST_nm'];
    $b = $_POST['s_Name'];
    $c = $_POST['addr'];
    $d = $_POST['m_n'];
    $e = $_POST['inv_d'];

    // Convert date format to MySQL format
    list($day, $month, $year) = explode('/', $e);
    $e_mysql = "$year-$month-$day";

    $f = $_POST['Atotal'];
    $g = $_POST['Agst'];
    $j = $_POST['beAmt'];
    $k = $_POST['totalamonut'];
    $m = $_POST['paid'];
    $dis = $_POST['discount'];
    $o = $_POST['cardlist'];
    $n = $_POST['totlnamt'];

    // Insert into 'bills' table
    $query_bills = "INSERT INTO bills (InvoiceNum, Customer_name, ShopName, Address, Phone, Inv_date, sub_total_Amount, Charge, beforeamt, totalamt, Paid, discount, cardtopay, totalnetamt) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query_bills);
    mysqli_stmt_bind_param($stmt, "isssisdddddssd", $i, $a, $b, $c, $d, $e_mysql, $f, $g, $j, $k, $m, $dis, $o, $n);

    if (mysqli_stmt_execute($stmt)) {
        $last_insert_id = mysqli_insert_id($conn); // Get the last inserted ID
        
        // Insert into 'billdetails' table
        $itemname = $_POST['item_nm'];
        $qty = $_POST['qty'];
        $price = $_POST['price'];
        $amount = $_POST['amt'];

        foreach ($itemname as $key => $item) {
            if (!empty($item)) { // Avoid empty items
                $q = $qty[$key];
                $p = $price[$key];
                $a = $amount[$key];

                $query_billdetails = "INSERT INTO billdetails (InvoiceNum, itemName, qty, Price, Amount) 
                                     VALUES (?, ?, ?, ?, ?)";

                $stmt_details = mysqli_prepare($conn, $query_billdetails);
                mysqli_stmt_bind_param($stmt_details, "isddd", $i, $item, $q, $p, $a);

                if (!mysqli_stmt_execute($stmt_details)) {
                    echo '<script> alert("Error inserting into billdetails: ' . mysqli_error($conn) . '")</script>';
                }

                mysqli_stmt_close($stmt_details);
            }
        }

        echo '<script> alert("Data Saved Successfully")</script>';
    } else {
        echo '<script> alert("Error inserting into bills: ' . mysqli_error($conn) . '")</script>';
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        .input-group-text {
            width: 134px;
        }
        .text_line {
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
#mobileNumberError {
            display: none; /* Hide error message during print */
        }


}
.signature-container {
    position: relative;
     display: inline-block; /*Adjusts the width of the container based on the content */
    background-image: url('images/signature.png'); /* Path to your image */
    background-size: contain; /*Scales the image to fit the container */
     background-repeat: no-repeat; /*Prevents repeating the image */
    background-position: center; /* Centers the image within the container */
    padding: 22px; /* Adjusts padding around the text if needed */
    text-align: center; /* Centers the text */
    margin-left:65px;
    margin-top:-20px;
    /* background-color: #fff; Optional: Set a background color if needed */
    border: 0px solid #ddd; /* Optional: Add border if needed */
    
}

    </style>
    
<script>
    $(document).ready(function() {
        // Validate mobile number length on input change
        $('#mobileNumber').on('input', function() {
            var mobileNumber = $(this).val();
            var mobileNumberError = $('#mobileNumberError');

            // Clear previous error message
            mobileNumberError.text('');

            // Check if the mobile number is exactly 10 digits long
            if (mobileNumber.length !== 10 && !isPrintButtonClicked()) {
                mobileNumberError.text('Mobile number must be exactly 10 digits long.');
            }
        });

        // Function to check if the print button is clicked
        function isPrintButtonClicked() {
            return $('#printButton').data('clicked');
        }

        // Click event for print button
        $('#printButton').click(function() {
            // Set a flag indicating print button is clicked
            $(this).data('clicked', true);

            // Perform print operation
            window.print();

            // Remove error message if present
            $('#mobileNumberError').text('');
        });
    });

    $(document).ready(function() {
        const currentDate = new Date();
        const day = String(currentDate.getDate()).padStart(2, '0');
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const year = currentDate.getFullYear();
        const formattedDate = `${day}/${month}/${year}`;
        $("#invoiceDate").val(formattedDate);
    });

    function GetPrint() {
        window.print();
    }

    function BtnAdd() {
        var newRow = $("#TRow").clone().appendTo("#TBody");
        $(newRow).removeClass("d-none");
        $(newRow).find("input").val('');
        $(newRow).find("th").first().html($('#TBody tr').length - 1);
    }

    function BtnDel(v) {
        $(v).closest("tr").remove();
        GetTotal();
        $("#TBody").find("tr").each(function(index) {
            $(this).find("th").first().html(index);
        });
    }

    function calc(v) {
        var qty = parseFloat($(v).closest("tr").find("input[name='qty[]']").val());
        var price = parseFloat($(v).closest("tr").find("input[name='price[]']").val());
        var amt = qty * price;
        $(v).closest("tr").find("input[name='amt[]']").val(amt.toFixed(2));
        GetTotal(); // Calculate total after updating amt
    }

    function GetTotal() {
        var sum = 0;
        $("input[name='amt[]']").each(function() {
            var amt = parseFloat($(this).val());
            if (!isNaN(amt)) {
                sum += amt;
            }
        });
        $("#Atotal").val(sum.toFixed(2));
        var gst = parseFloat($("#Agst").val()) || 0;
        var amtotal = sum + gst;
        $("#totalamonut").val(amtotal.toFixed(2));
        var beforeamt = parseFloat($("#beAmt").val()) || 0;
        var totalWithBeamts = amtotal + beforeamt;
        $("#totalamonut").val(totalWithBeamts.toFixed(2));
        $("#totlnamt").val(totalWithBeamts.toFixed(2));
        paidb();
    }

    function paidb() {
        var totalAmount = parseFloat($("#totalamonut").val()) || 0;
        var paid = parseFloat($("#paid").val()) || 0;
        var discount = parseFloat($("#discount").val()) || 0;
        var remainingBalance = totalAmount - paid - discount;
        $("#totlnamt").val(remainingBalance.toFixed(2));
    }
</script>
</head>

<body>
    <div class="container">
        <form method="POST" action="">
            <div class="card">
                <img src="images/logob21.png" alt="image" width="275">
                <p style="margin-left:800px;"> <u>Original Copy</u></p>
                <div class="card">
                    <div class="card-header text-center">
                        <h4 style="font-family: 'Times New Roman', Times, serif;"><b>!! <u> INVOICE</u> !!</b></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Customer Name</span>
                                    <input type="text" class="form-control" placeholder="Enter Customer name" name="CUST_nm" style="font-weight: bold" Required>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Shop Name</span>
                                    <input type="text" class="form-control" placeholder="Enter Shop Name" name="s_Name" style="font-weight: bold" Required>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Shop Address</span>
                                    <input type="text" class="form-control" placeholder="Enter Address" name="addr" Required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Mobile Number</span>
                                    <input type="number" class="form-control" placeholder="Enter 10 Digit Number" style="font-weight: bold" name="m_n" id="mobileNumber" required>
                                    <span id="mobileNumberError" class="text-danger"></span>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Invoice Number</span>
                                    <input type="number" class="form-control" placeholder="Enter Invoice Number" name="inv_n" Required>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Invoice Date</span>
                                    <input type="text" class="form-control" id="invoiceDate" name="inv_d" required>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" class="text_line">Item Name </th>
                            <th scope="col" class="text-end" style="font-family: 'Times New Roman', Times, serif;">Qty</th>
                            <th scope="col" class="text-end" style="font-family: 'Times New Roman', Times, serif;">Price</th>
                            <th scope="col" class="text-end" style="font-family: 'Times New Roman', Times, serif;">Amount</th>
                            <th scope="col" class="NoPrint"> <button type="button" class="btn btn-warning" onclick="BtnAdd()">+</th>
                        </tr>
                    </thead>
                    <tbody id="TBody">
                        <tr id="TRow" class="d-none">
                            <th scope="row"></th>
                            <td class="text_line"> <input type="text" class="form-control" name="item_nm[]"></td>
                            <td> <input type="number" class="form-control text-end" name="qty[]" min="0" max="3000" onchange="calc(this);"></td>
                            <td> <input type="number" class="form-control text-end" name="price[]"min="0" max="10000" step="0.01" onchange="calc(this);"></td>
                            <td> <input type="number" class="form-control text-end" name="amt[]" value="0" readonly=""></td>
                            <td class="NoPrint"><button type="button" class="btn btn-danger" onclick="BtnDel(this)">✕</button></td>
                        </tr>


                    </tbody>

                </table>
                <div class="row" >
                <div class="col-4">
                   
                <div class="input-group mb-3">
                    <label for="cars" class="input-group-text">To Pay card</label>
                        <select id="cards" class="form-control" name="cardlist" >
                            <option name="card" value="No Pay" style="font-weight: bold; color: Red;">No Pay</option>
                            <option name="card" value="cash"style="font-weight: bold; color: blue;">Cash</option>
                            <option name="card" value="paytm"style="font-weight: bold; color: blue;">Paytm</option>
                             <option name="card" value="phonepay"style="font-weight: bold; color: blue;">PhonePe</option>
                             <option name="card" value="gpay"style="font-weight: bold; color: blue;">GPay</option>
                             <option name="card" value="other"style="font-weight: bold; color: yellow;">Other</option>
                             
                         </select>
                
                </div>
                <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"style="font-weight: bold; color:green;">Discount INR ₹</span>
                            <input type="number" class="form-control" id="discount" min="0" max="5000" placeholder=" ₹ Enter Discount" name="discount" onchange="paidb()" style="font-weight: bold; color:green;">
                </div>
                <div class="input-group mb-3" >
                </div>
                <div class="input-group mb-3">
                <!-- <img src="images/signature.png" alt="signature_image" class="signature-img"> -->
                </div>

                <div class="col-4">
                            <div class="signature-container">
                                <p >SIGNATURE</p>
                            </div>
                        </div>
                </div>
                    <div class="col-4">
                  
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Auto Charge ₹</span>
                            <input type="number" class="form-control" placeholder="Enter auto charge" min="0" max="2000" id="Agst" name="Agst" onchange="GetTotal()">
                          
                        </div>
                        <div class="input-group mb-3">
                    
                            <span class="input-group-text" id="basic-addon1">Before Balance ₹</span>
                            <input type="number" class="form-control" min="0" max="100000" step="0.01" id="beAmt" name="beAmt" placeholder="Enter Before amount" onchange="GetTotal()">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"style="font-weight: bold; color:green;">Paid Payment ₹</span>
                            <input type="number" class="form-control" id="paid" placeholder="Enter Received payment" min="0" max="100000" name="paid" onchange="paidb()" style="font-weight: bold; color:green;">

                        </div>
                       
                    </div>
                        <div class="col-4">
                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1" style="font-weight: bold; font-family: 'Times New Roman', Times, serif;">Sub Amount ₹</span>
                            <input type="number" class="form-control" id="Atotal" name="Atotal" readonly="readonly"style="font-weight: bold">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1" style="font-weight: bold; font-family: 'Times New Roman', Times, serif; color: blue;"  >Total Amount ₹</span>
                            <input type="number" class="form-control" id="totalamonut" name="totalamonut" readonly="readonly" style="font-weight: bold; color: blue;"> 

                        </div> 
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"style="font-weight: bold; color:red;">Pending INR ₹</span>
                            <input type="number" class="form-control" min="0" max="100000" step="0.01" id="totlnamt" name="totlnamt" readonly="readonly" style="font-weight: bold; color:red;">

                        
                        </div>  
                        </div>
                </div>

                <div class="row-4">
                <button type="button" class="btn btn-primary" onclick="GetPrint()" id="printButton" name="btn">PRINT</button>

                    <button type="submit" class="btn btn-primary" onclick="getsave()" name="btn1">SAVE</button>
                    <!-- <button type="button" class="btn btn-primary" onclick="download()">Download</button> -->

                    
                    <button type="submit" class="btn btn-success" onclick="history.back()" name="btn3">CANCEL</button>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
    $(document).ready(function() {
        $('#mobileNumber').on('input', function() {
            var mobileNumber = $(this).val();
            if (mobileNumber.length === 10) {
                $.ajax({
                    url: '', // Current page
                    type: 'POST',
                    data: { mobileNumber: mobileNumber },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('input[name="CUST_nm"]').val(response.data.Customer_name);
                            $('input[name="addr"]').val(response.data.Address);
                            $('input[name="s_Name"]').val(response.data.ShopName);
                        } else {
                            $('input[name="CUST_nm"]').val('');
                            $('input[name="addr"]').val('');
                            $('input[name="s_Name"]').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: " + status + error);
                    }
                });
            }
        });

        // Handle form submission and calculations
        $('form').on('submit', function(e) {
    // Just to test form submission
    alert('Data Saved Successfully');
    // You can add your AJAX or form handling code here
});

    });
    </script>
</body>

</html>
