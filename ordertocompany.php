<?php
session_start();
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
$servername = "localhost"; // Replace with your database server name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "shiddh"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderDate = isset($_POST['inv_d']) ? $_POST['inv_d'] : '';
    $mode = isset($_POST['mode']) ? $_POST['mode'] : '';
    $ordertocmp = isset($_POST['ordcmp']) ? $_POST['ordcmp'] : '';
    $amount = isset($_POST['Atotal']) ? $_POST['Atotal'] : '';
    $chrgeauto = isset($_POST['Agst']) ? $_POST['Agst'] : '';
    $totalamount = isset($_POST['totalAmount']) ? $_POST['totalAmount'] : '';
    $totalamtwithgst = isset($_POST['totalWithGST']) ? $_POST['totalWithGST'] : '';

    // Start a transaction
    $conn->begin_transaction();
    
    try {
        // Prepare and bind for the `orderbycompany` table
        $stmt = $conn->prepare("INSERT INTO orderbycompany (orderDate, mode, ordertocmp, sub_amount, chrgeauto, totalamount, totalamtwithgst) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("sssssss", $orderDate, $mode, $ordertocmp, $amount, $chrgeauto, $totalamount, $totalamtwithgst);
        
        // Execute
        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }
        
        // Get the last inserted ID
        $orderId = $conn->insert_id;
        
        // Prepare and bind for the `ordertocmpdetails` table
        $stmt = $conn->prepare("INSERT INTO ordertocmpdetails (ordernum, itemname, description, qty, price, amount) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . htmlspecialchars($conn->error));
        }
        
        // Bind parameters
        $itemNumber = $orderId; // Assuming you want to use the `orderId` as `ordernum`
        
        // Loop through items
        foreach ($_POST['item_nm'] as $index => $itemName) {
            $description = isset($_POST['item_description'][$index]) ? $_POST['item_description'][$index] : '';
            $qty = isset($_POST['qty'][$index]) ? $_POST['qty'][$index] : 1;
            $price = isset($_POST['price'][$index]) ? $_POST['price'][$index] : 1;
            $amount = isset($_POST['amt'][$index]) ? $_POST['amt'][$index] : 1;
            
            $stmt->bind_param("issddd", $itemNumber, $itemName, $description, $qty, $price, $amount);
            
            if (!$stmt->execute()) {
                throw new Exception("Error executing query: " . $stmt->error);
            }
        }
        
        // Commit transaction
        $conn->commit();
        echo '<script>alert("Data Saved Successfully")</script>';
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        $conn->rollback();
        echo '<script>alert("Error: ' . $e->getMessage() . '")</script>';
    }
    
    // Close connections
    $stmt->close();
    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        <style>
        .input-group-text {
            width: 122px;
        }
       .text_linep{
        width: 110px;
       }
       .text_lineq{
        width: 110px;
       }
        .text_line {
            width: 350px;
        }
        .text_lined {
            width: 200px;
        }
        .centered-image {
    text-align: center; /* Center horizontally */
}

.centered-image img {
    width: 170px; /* Adjust to make the image smaller */
    height: auto; /* Maintain aspect ratio */
    
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
    </style>
    <script>
        $(document).ready(function() {
            $('#mobileNumber').on('input', function() {
                var mobileNumber = $(this).val();
                var mobileNumberError = $('#mobileNumberError');
                mobileNumberError.text('');
                if (mobileNumber.length !== 10 && !isPrintButtonClicked()) {
                    mobileNumberError.text('Mobile number must be exactly 10 digits long.');
                }
            });

            function isPrintButtonClicked() {
                return $('#printButton').data('clicked');
            }

            $('#printButton').click(function() {
                $(this).data('clicked', true);
                window.print();
                $('#mobileNumberError').text('');
            });

            const currentDate = new Date();
const day = String(currentDate.getDate()).padStart(2, '0');
const month = String(currentDate.getMonth() + 1).padStart(2, '0');
const year = currentDate.getFullYear();
const formattedDate = `${year}-${month}-${day}`; // yyyy-mm-dd
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
            GetTotal();
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

            var autoCharge = parseFloat($("#Agst").val()) || 0;
            var totalAmount = sum + autoCharge;
            $("#totalAmount").val(totalAmount.toFixed(2));

            // Calculate 18% GST on the total amount
            var gst = totalAmount * 0.18; // 18% GST
            var totalAmountWithGST = totalAmount + gst;
            $("#totalWithGST").val(totalAmountWithGST.toFixed(2));
        }
    </script>
</head>
<body>
    <div class="container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="card">
            <img src="images/logoinv2.jpg" alt="image" width="250">
            <div class="card"> 
                <div class="card-header text-center">
                    <h4 style="font-family: 'Times New Roman', Times, serif;"><b>!! <u>ORDER</u> !!</b></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Company</span>
                                <input type="text" class="form-control" placeholder="SHIDDH LIGHTING INDUSTRIES ,  Satna M.P." name="CUST_nm" style="font-weight: bold" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Name</span>
                                <input type="text" class="form-control" placeholder="MOHIT KUMAR SAHU" name="s_Name" style="font-weight: bold" readonly>
                            </div> 
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Father Name</span>
                                <input type="text" class="form-control" placeholder="KRISHNA KISHOR SAHU" name="f_Name" style="font-weight: bold" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <label for="cars" class="input-group-text">Mode</label>
                                <select id="cards" class="form-control" name="mode" required >
                                    <option name="mode" value="" style="font-weight: bold; color: Red;">Choose</option>
                                    <option name="card" value="Nagpur Golden Transport Satna Mp 485001 / 9870291089,9302106249" style="font-weight: bold; color: blue;">Nagpur Golden Transport Satna Mp 485001</option>
                                    <option name="card" value="TCI Freight Transport Satna Mp 485001 / 9818866824" style="font-weight: bold; color: blue;">TCI Freight Transport Satna Mp 485001</option>
                                    <option name="card" value="other" style="font-weight: bold; color: yellow;">Other</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Phone</span>
                                <input type="text" class="form-control" placeholder="Enter Others Number" name="phone" required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Address</span>
                                <input type="text" class="form-control" placeholder="SOHAWAL TARAHTI WARD NO. 8, SOHAWAL SATNA MP 485001" name="addr" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <label for="cars" class="input-group-text">To Company</label>
                                <select id="cards" class="form-control" name="ordcmp" required >
                                    <option name="card" value="" style="font-weight: bold; color: Red;">Choose</option>
                                    <option name="card" value="Bhagwati Lighting Industries / Rachhi-7217886313" style="font-weight: bold; color: blue;">Bhagwati Lighting Industries / Rachhi-7217886313</option>
                                    <option name="card" value="Bhagwati Lighting Industries / Shreya-8595521644" style="font-weight: bold; color: blue;">Bhagwati Lighting Industries / Shreya-8595521644</option>
                                    <option name="card" value="Agrawal Electricals / Mantu-9300517412" style="font-weight: bold; color: blue;">Agrawal Electricals / Mantu-9300517412</option>
                                    <option name="card" value="Lalita Electricals / Shyam-9301526038" style="font-weight: bold; color: blue;">Lalita Electricals / Shyam-9301526038</option>
                                    <option name="card" value="other" style="font-weight: bold; color: yellow;">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Brand Name</span>
                                <input type="text" class="form-control" placeholder="SHIDDH" style="font-weight: bold" name="brand" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Number</span>
                                <input type="number" class="form-control" placeholder="7869940934" style="font-weight: bold" name="brand_m_n" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Mother Name</span>
                                <input type="text" class="form-control" placeholder="SHYAM BAI SAHU" name="m_Name" style="font-weight: bold" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Adhar Number</span>
                                <input type="text" class="form-control" placeholder="7133 7547 3408" name="adhar" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Pan Number</span>
                                <input type="text" class="form-control" placeholder="Enter Pan Number" name="pan" >
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Order Date</span>
                                <input type="text" class="form-control" id="invoiceDate" name="inv_d" readonly>
                            </div>
                            <div class="input-group mb-3 centered-image">
    <img src="images/shiddh21new.png" alt="Logo">
</div>


                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text_line">Item Name</th>
                                <th scope="col" class="text_end">Description</th>
                                <th scope="col" class="text-end" style="font-family: 'Times New Roman', Times, serif;">Qty</th>
                                <th scope="col" class="text-end" style="font-family: 'Times New Roman', Times, serif;">Price</th>
                                <th scope="col" class="text-end" style="font-family: 'Times New Roman', Times, serif;">Amount</th>
                                <th scope="col" class="NoPrint"><button type="button" class="btn btn-warning" onclick="BtnAdd()">+</button></th>
                            </tr>
                        </thead>
                        <tbody id="TBody">
                            <tr id="TRow" class="d-none">
                                <th scope="row"></th>
                                <td class="text_line"><input type="text" class="form-control" name="item_nm[]"></td>
                                <td class="text_lined"><input type="text" class="form-control" name="item_description[]"></td>
                                <td class="text_lineq"><input type="number" class="form-control text-end" name="qty[]" onchange="calc(this);"></td>
                                <td class="text_linep"><input type="number" class="form-control text-end" name="price[]" min="0" max="1000" step="0.01" onchange="calc(this);"></td>
                                <td><input type="number" class="form-control text-end" name="amt[]" value="0" readonly></td>
                                <td class="NoPrint"><button type="button" class="btn btn-danger" onclick="BtnDel(this)">✕</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-6 offset-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1" style="font-weight: bold; font-family: 'Times New Roman', Times, serif;">Amount ₹ /-</span>
                                <input type="number" class="form-control" id="Atotal" name="Atotal" readonly="readonly" style="font-weight: bold">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Auto Charge </span>
                                <input type="number" class="form-control" placeholder="Enter auto charge" id="Agst" name="Agst" onchange="GetTotal()">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1" style="font-weight: bold; color:red;">Total Amount </span>
                                <input type="number" class="form-control" min="0" max="100000" step="0.01" id="totalAmount" name="totalAmount" readonly="readonly" style="font-weight: bold; color:red;">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1" style="font-weight: bold; color:red;">Pending Pay </span>
                                <input type="number" class="form-control" min="0" max="100000" step="0.01" id="totalWithGST" name="totalWithGST" readonly="readonly" style="font-weight: bold; color:red;">
                            </div>
                        </div>
                    </div>
                    <div class="row-4">
                        <button type="button" class="btn btn-primary" onclick="GetPrint()" id="printButton" name="btn1">PRINT</button>
                        <button type="submit" class="btn btn-primary" id="savebutton" name="btn1">SAVE</button>
                        <button type="button" class="btn btn-success" onclick="history.back()">CANCEL</button>
                    </div>
                </div>
            </div>
            </div>
        </form>
   
    </div>
</body>
</html>
