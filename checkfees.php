
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document</title>
    <link rel="stylesheet" href="stylecos.css">
    <style>
        #S{
            height: 30px;
            padding: auto;
            background: #b1b1b1;
            
        }
    </style>
</head>
<body bgcolor="black">
    <form action="#" method="POST">
    <img src="images/logosl.png" alt="" width="200px">
    <center style="padding:4px;">
    <!-- <input type="text" name="btn1" placeholder="search" id="S">  -->
    <input type="date" name="btn2" required="required">
    <input type="submit" name="bs" value="Search" style="cursor:pointer;">
    </center>
    </form>
</body>
</html>

<?php
// Establish connection
$con = mysqli_connect("localhost", "root", "", "shiddh");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

$totalPaid = 0; // Variable to store the total paid amount

if(isset($_POST['bs'])) {
    $se = $_POST['btn2'];
    $query = "SELECT * FROM bills WHERE I_Date='$se'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        echo "Error: " . mysqli_error($con);
    } else {
        echo "<table border='1' align='center' width='100%'>";
        echo "<tr>";
        echo "<td style='background:yellow'>Customer_Name</td>";
        echo "<td style='background:yellow'>Shop_Name</td>";
        echo "<td style='background:yellow'>Address</td>";
        echo "<td style='background:yellow'>Date</td>";
        echo "<td style='background:yellow'>Card To Pay</td>";
        echo "<td style='background:yellow'>Amount</td>";
        echo "</tr>";

        while ($row = mysqli_fetch_array($result)) {
            echo "<tr style='color:white'>";
            echo "<td>" . $row['CustomerName'] . "</td>";
            echo "<td>" . $row['ShopName'] . "</td>";
            echo "<td>" . $row['Address'] . "</td>";

            // Format date from yyyy-mm-dd to dd-mm-yyyy
            $formattedDate = date('d-m-Y', strtotime($row['I_Date']));
            echo "<td>" . $formattedDate . "</td>";

            echo "<td>" . $row['Card_payment'] . "</td>";
            echo "<td>" . $row['paid'] . "</td>";
            echo "</tr>";

            // Accumulate the paid amount to calculate total
            $totalPaid += $row['paid'];
        }

        // Display the total paid amount row
        echo "<tr>";
        echo "<td colspan='5' style='text-align:right; font-weight: bold; background: #ffe9e9;color:blue'>Total Paid ₹ /- </td>";
        echo "<td style='font-weight: bold; background:yellow ; color:blue'>$totalPaid</td>";
        echo "</tr>";

        echo "</table>";
        echo "<script>alert('$totalPaid') </script>";
    }
}

// Close connection
mysqli_close($con);
?>

