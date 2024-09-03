
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
    <input type="text" name="btn1" placeholder="search" id="S"> 
    <input type="submit" name="bs" value="Search" style="cursor:pointer;">
    </center>
    </form>
</body>
</html>

<?php
error_reporting(0);
$con=mysqli_connect("localhost","root","","shiddh");
 if(isset($_POST['bs']))
{
    // echo "<td>", $r1['f_name'];
    // echo "<td>", $r1['mobile'];
    // echo "<td>", $r1['shopname'];
    // echo "<td>", $r1['address'];
    $se=$_POST['btn1'];
$r=mysqli_query($con,"select * from registration where SHOPName='$se' or FullName='$se'  ");
echo "<table border='1' align='center' width='100%'>";
echo "<td style='background:yellow'>C_Name</td>";
echo "<td style='background:yellow'>C_Phone</td>";
echo "<td style='background:yellow'>C_ShopName</td>";
echo "<td style='background:yellow'>C_Address</td>";
echo "<td style='background:yellow'>Amount</td>";
while($r1=mysqli_fetch_array($r))
{
    echo "<tr style='color:white'>";
    $id=$r1['C_mobile'];
    echo "<td>", $r1['FullName'];
    echo "<td>", $r1['Phone'];
    echo "<td>", $r1['SHOPName'];
    echo "<td>", $r1['Address'];
     
}
$rs=mysqli_query($con,"select * from bills where SHOPName='$se' or InvoiceNumber='$se'  ");
while($r2=mysqli_fetch_array($rs))
{
    
    echo "<td>", $r2['totalsubamount'];
    echo "<td>  <a href=update.php?id=$id>Update </a></td>";
    
    echo "</tr>";
}


}

?>
