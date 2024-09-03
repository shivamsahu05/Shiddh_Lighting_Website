<?php
// Establish database connection
$con = mysqli_connect("localhost", "root", "", "shiddh");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Function to calculate Net Amount in PHP
function calculateNetAmount($totalnetamt, $Paid, $Add) {
    return $totalnetamt - $Paid + $Add;
}

// Check if form is submitted and process the update
if (isset($_POST['update'])) {
    // Sanitize input data to prevent SQL injection
    $invoiceNum = mysqli_real_escape_string($con, $_POST['invoiceNum']);
    $customerName = mysqli_real_escape_string($con, $_POST['customerName']);
    $shopname = mysqli_real_escape_string($con, $_POST['shopname']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $datePaid = date('Y-m-d', strtotime($_POST['date_paid'])); // Convert dd-mm-yyyy to yyyy-mm-dd
    $Paid = mysqli_real_escape_string($con, $_POST['paid']);
    $Add = mysqli_real_escape_string($con, $_POST['add']);
    $discription = mysqli_real_escape_string($con, $_POST['discription']);
    $crdPaid = mysqli_real_escape_string($con, $_POST['cardlist']);
    $totalNetAmt = mysqli_real_escape_string($con, $_POST['totalnetamt']); // Added for Net Amount calculation

    // Calculate Net Amount including additional amount
    $netAmt = calculateNetAmount($totalNetAmt, $Paid, $Add);

    // Insert or update query based on whether InvoiceNum already exists
    $query = "INSERT INTO billupdate (InvoiceNum, Phone, CustomerName, ShopName, date_paid, Paid, addedamount, discription, Net_Amount, cardtopay)
    VALUES ('$invoiceNum', '$phone', '$customerName', '$shopname', '$datePaid', '$Paid', '$Add', '$discription', '$netAmt', '$crdPaid')
    ON DUPLICATE KEY UPDATE
    Phone = VALUES(Phone),
    date_paid = VALUES(date_paid),
    Paid = VALUES(Paid),
    CustomerName = VALUES(CustomerName),
    ShopName = VALUES(ShopName),
    addedamount = VALUES(addedamount),
    discription = VALUES(discription),
    cardtopay = VALUES(cardtopay),
    Net_Amount = VALUES(Net_Amount)";
    



    if (mysqli_query($con, $query)) {
        // Update totalnetamt and cardtopay in bills table
        $updateQuery = "UPDATE bills SET totalnetamt = '$netAmt', cardtopay = '$crdPaid' WHERE InvoiceNum = '$invoiceNum'";
        if (mysqli_query($con, $updateQuery)) {
            // Alert with current values and then redirect
            echo "<script>alert('Paid Amount: $Paid\\nAdd Amount: $Add\\nNet Amount: $netAmt'); window.location.href = 'feesupdate.php';</script>";
            exit();
        } else {
            echo "Error updating totalnetamt and cardtopay: " . mysqli_error($con);
        }
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// Ensure the invoiceNum parameter is set and is not empty
if (isset($_POST['invoiceNum']) && !empty($_POST['invoiceNum'])) {
    // Sanitize the input to prevent SQL injection
    $invoiceNum = mysqli_real_escape_string($con, $_POST['invoiceNum']);

    // Fetch the record for the given InvoiceNum
    $query = "SELECT * FROM bills WHERE InvoiceNum = '$invoiceNum'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Display the update form or perform update logic here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Invoice</title>
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: grid;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], input[type="date"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Invoice</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="hidden" name="invoiceNum" value="<?php echo htmlspecialchars($row['InvoiceNum']); ?>">

            <label for="customerName">Customer Name:</label>
            <input type="text" id="customerName" name="customerName" value="<?php echo htmlspecialchars($row['Customer_name']); ?>" readonly required>

            <label for="shopname">Shop Name:</label>
<input type="text" id="shopname" name="shopname" value="<?php echo htmlspecialchars($row['ShopName']); ?>" readonly required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($row['Phone']); ?>" required readonly>

            <label for="totalnetamt">Amount:</label>
            <input type="text" id="totalnetamt" name="totalnetamt" value="<?php echo htmlspecialchars($row['totalnetamt']); ?>" required readonly>

            <label for="date_paid">Date:</label>
            <input type="text" id="date_paid" name="date_paid" value="<?php echo isset($row['Date_Paid']) ? htmlspecialchars(date('d-m-Y', strtotime($row['Date_Paid']))) : date('d-m-Y'); ?>" required>

            <label for="add">Add Amount:</label>
            <input type="number" id="add" name="add" placeholder="Enter Add Amount" required>
            
            <label for="discription">Discription:</label>
            <input type="text" id="discription" name="discription" placeholder="Discribe add amount" >

            <label for="paid">Paid:</label>
            <input type="number" id="paid" name="paid" placeholder="Enter Paid Amount" required>

            <label for="cards">To Pay Card</label>
            <select id="cards" name="cardlist" required>
                <option value="" disabled selected>Select To Pay Card</option>
                <option value="cash">Cash</option>
                <option value="paytm">Paytm</option>
                <option value="phonepay">PhonePe</option>
                <option value="gpay">GPay</option>
                <option value="other">Other</option>
            </select>

            <label for="net_amt">Net Amount:</label>
            <input type="text" id="net_amt" name="net_amt" value="<?php echo htmlspecialchars(calculateNetAmount($row['totalnetamt'], 0, 0)); ?>" required readonly>

            <input type="submit" name="update" value="Update">
        </form>
    </div>

    <script>
        // JavaScript function to calculate Net Amount based on totalnetamt, Paid, and Add
        function calculateNetAmount(totalnetamt, Paid, Add) {
            var netAmt = parseFloat(totalnetamt) - parseFloat(Paid) + parseFloat(Add);
            return netAmt.toFixed(2); // Adjust to decimal places as needed
        }

        // Automatically calculate and update Net Amount field
        document.addEventListener('DOMContentLoaded', function() {
            var totalnetamt = <?php echo json_encode($row['totalnetamt']); ?>;
            var Paid = 0; // Initialize paid amount to 0
            var Add = 0; // Initialize add amount to 0
            var netAmtField = document.getElementById('net_amt');

            // Initial calculation on page load
            netAmtField.value = calculateNetAmount(totalnetamt, Paid, Add);

            // Recalculate when Paid or Add fields change
            document.getElementById('paid').addEventListener('input', function() {
                Paid = this.value;
                netAmtField.value = calculateNetAmount(totalnetamt, Paid, Add);
            });

            document.getElementById('add').addEventListener('input', function() {
                Add = this.value;
                netAmtField.value = calculateNetAmount(totalnetamt, Paid, Add);
            });
        });
    </script>

</body>
</html>
<?php
    } else {
        echo "Invoice number not found.";
    }
} else {
    echo "Invalid request.";
}
?>
