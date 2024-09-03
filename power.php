<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
   
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        .input-group-text {
            width: 157px;
        }
       
        #cards{
            width: 80px;
        } 
        .text_line{
            width: 430px;
        }
         /* .owner-signature{
            height: 20vh;
    font-family: 'Times New Roman', Times, serif;
    text-align: right;
    background-image: url('signature.png');
    color: black; 
    padding: 45px; 
    box-sizing:
     background-size: contain; 
    background-position: right; 
    background-repeat: no-repeat; 
        }  */
       


        @media print {

            .owner-signature{
                
               
            }

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
    const day = String(currentDate.getDate()).padStart(2, '0');
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const year = currentDate.getFullYear();
    const formattedDate = `${day}/${month}/${year}`; // Formats date as DD/MM/YYYY

    $("#invoiceDate").val(formattedDate);
});


    

        
        function GetPrint() {
            window.print();
        }
        
        function BtnAdd() {
      var v = $("#TRow").clone().appendTo("#TBody") ;
            $(v).find("input").val('');
            $(v).removeClass("d-none");
            $(v).find("th").first().html($('#TBody tr').length -1 );
        }

        function BtnDel(v) {
             $(v).parent().parent().remove();
             GetTotal();

             $("#TBody").find("tr").each(function(index) {
             $(this).find("th").first().html(index);
             });
             }

        function calc(v)
        {
            var index= $(v).parent().parent().index();
           var qty = document.getElementsByName("qty")[index].value;
           var price= document.getElementsByName("price")[index].value;

            var amt = +qty * price;
         document.getElementsByName("amt")[index].value = amt.toFixed(2);
                GetTotal();
        }
     
        
        function GetTotal() {
    var sum = 0;
    var amts = document.getElementsByName("amt");

    // Calculate sum of amounts
    for (let index = 0; index < amts.length; index++) {
        var amt = parseFloat(amts[index].value); // Convert to number
        if (!isNaN(amt)) { // Check if conversion is successful
            sum += amt;
        }
    }

    // Set total amount without GST
    // document.getElementById("totlnamt").value = sum;
    document.getElementById("Atotal").value = sum.toFixed(2);
  
    // document.getElementById("totlnamt").value = sum;
    // Retrieve GST amount
    var gst = parseFloat(document.getElementById("Agst").value);
    if (isNaN(gst)) { // Check if conversion is successful
        gst = 0; // Default to 0 if conversion fails
    }
    var beforeamt = parseFloat(document.getElementById("beAmt").value);

    // Calculate total amount with GST
    var amtotal = sum + gst ;
    document.getElementById("totalamonut").value = amtotal;
    document.getElementById("totlnamt").value = ammtotal;

   var ammtotal= amtotal + beforeamt;
   document.getElementById("totalamonut").value = ammtotal.toFixed(2);
  document.getElementById("totlnamt").value = ammtotal.toFixed(2);

    // Calculate grand total including beamts
    var beamts = document.getElementsByName("beAmt");
    var totalWithBeamts = amtotal; // Start with total including GST
    for (var i = 0; i < beamts.length; i++) {
        var beamt = parseFloat(beamts[i].value); // Convert to number
        if (!isNaN(beamt)) { // Check if conversion is successful
            totalWithBeamts += beamt;
        }
    }

    // Set grand total
    document.getElementById("totalamonut").value = totalWithBeamts.toFixed(2);

 }

 function paidb() {
    var totalAmount = parseFloat(document.getElementById("totalamonut").value);
    var paid = parseFloat(document.getElementById("paid").value);

    // Check if values are valid numbers
    if (!isNaN(totalAmount) && !isNaN(paid)) {
        var remainingBalance = totalAmount - paid;
        document.getElementById("totlnamt").value = remainingBalance.toFixed(2) ;
    }
 }


    </script>


</head>

<body>
    <div class="container">
        <form method="POST" action="">
        <img src="images/logoinv.jpg" alt="image" width="250">
        <div class="card"> 
    
            <div class="card-hrader text-center">
                <h4 style="font-family: 'Times New Roman', Times, serif;"><b>INVOICE</b> <hr></h4>
               
            </div>
            <div class="card-body">


                <div class="row">
                    <div class="col-7">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Customer</span>
                            <input type="text" class="form-control" placeholder="Enter Customer name" name="CUST_nm" style="font-weight: bold" Required>

                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Shop Name</span>
                            <input type="text" class="form-control" placeholder="Enter Shop Name" name="s_Name" style="font-weight: bold" Required>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text " id="basic-addon1">Address</span>
                            <input type="text" class="form-control" placeholder="Enter Address" name="addr"Required>
                        </div>

                        
                    </div>
                    <div class="col-5">
                            <div class="input-group mb-3">
                              <span class="input-group-text" id="basic-addon1">Mobile Number</span>
                            <input type="number" class="form-control" placeholder="Enter Mobile Number" style="font-weight: bold" name="m_n" Required>
                            </div>
                            <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Invoice no.</span>
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
                            <td class="text_line"> <input type="text" class="form-control" name="item_nm"></td>
                            <td> <input type="number" class="form-control text-end" name="qty" onchange="calc(this);"></td>
                            <td> <input type="number" class="form-control text-end" name="price"min="0" max="1000" step="0.01" onchange="calc(this);"></td>
                            <td> <input type="number" class="form-control text-end" name="amt" value="0" readonly=""></td>
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
                <div class="input-group mb-3" >
                </div>
                <div class="input-group mb-3" >
                </div>
                <div class="input-group mb-3" >
                    <b> <u>
                    <p>OWNER SIGNATURE</p>
                   </u> </b>
                </div>
                </div>
                    <div class="col-4">
                  
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Auto Charge </span>
                            <input type="number" class="form-control" placeholder="Enter auto charge" id="Agst" name="Agst" onchange="GetTotal()">
                          
                        </div>
                        <div class="input-group mb-3">
                    
                            <span class="input-group-text" id="basic-addon1">Before Balance</span>
                            <input type="number" class="form-control" min="0" max="10000" step="0.01" id="beAmt" name="beAmt" placeholder="Enter Before amount" onchange="GetTotal()">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"style="font-weight: bold; color:green;">Paid Payment </span>
                            <input type="number" class="form-control" id="paid" placeholder="Enter Received payment" name="paid" onchange="paidb()" style="font-weight: bold; color:green;">
                          
                        </div>
                       
                    </div>
                        <div class="col-4">
                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1" style="font-weight: bold; font-family: 'Times New Roman', Times, serif;">Amount</span>
                            <input type="number" class="form-control" id="Atotal" name="Atotal" readonly="readonly"style="font-weight: bold">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1" style="font-weight: bold; font-family: 'Times New Roman', Times, serif; color: blue;"  >Total Amount ₹/-</span>
                            <input type="number" class="form-control" id="totalamonut" name="totalamonut" readonly="readonly" style="font-weight: bold; color: blue;"> 

                        </div> 
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"style="font-weight: bold; color:red;">Pending Amount ₹</span>
                            <input type="number" class="form-control " min="0" max="1000" step="0.01" id="totlnamt" name="totlnamt"readonly="readonly" style="font-weight: bold; color:red;">

                        
                        </div> 
                       



                    </div>
                </div>

           
                <div class="row-4">
                <button type="button" class="btn btn-primary" onclick="GetPrint()" name="btn1">PRINT</button>
                    <button type="submit" class="btn btn-primary" onclick="getsave()" name="btn1">SAVE</button>
                       
                    <button type="submit" class="btn btn-primary" onclick="history.back()" name="btn1">BACK</button>
                    <button type="button" class="btn btn-primary" id="home">LogOut</button>
                    <script> 
                                document.getElementById("home").onclick = function() {
                                window.location.href = "login.php";
                                 };
                    </script>
                    
            </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</body>
</html>


<?php
error_reporting(0);
$conn = mysqli_connect("localhost", "root", "", "shiddh");

if (isset($_POST['btn1'])) {
    $i = $_POST['inv_n'];
    $a = $_POST['CUST_nm'];
    $b = $_POST['s_Name'];
    $c = $_POST['addr'];
    $d = $_POST['m_n'];
    
    $e = $_POST['inv_d']; // Assuming the date is received in DD/MM/YYYY format

    // Convert the date to MySQL's YYYY-MM-DD format
    list($day, $month, $year) = explode('/', $e);
    $e_mysql = "$year-$month-$day";
   
    $f = $_POST['Atotal'];
    $g = $_POST['Agst'];
   
    $j = $_POST['beAmt'];
    $k = $_POST['totalamonut'];
    $m = $_POST['paid'];
    $o = $_POST['cardlist'];
    $n = $_POST['totlnamt'];

    $q = mysqli_query($conn, "INSERT INTO bills VALUES ('$i', '$a', '$b', '$c', '$d', '$e_mysql', '$f', '$g', '$j', '$k', '$m','$o','$n')");

    if ($q) {
        echo '<script> alert("Saved")</script>';
    } else {
        echo '<script> alert("Error: ' . mysqli_error($conn) . '")</script>';
    }
}
?>
