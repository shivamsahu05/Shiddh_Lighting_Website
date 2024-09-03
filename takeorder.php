<!DOCTYPE html>
<html lang="en">
<head>
    <title>Take Order</title>
    <link rel="stylesheet" href="styleodr.css">
</head>
<body bgcolor="black">
<form  method="POST" action="">
    <img src="logo.png" alt="logo" width="250px">
    <div class="info">
    <i><input type="text" placeholder="SHOP NAME" name="t1" Required></i>
    <i><input type="text" placeholder="Your Name" name="t2" Required></i>
    <i><input type="number" placeholder="MOBILE" name="n1" Required></i>
    <i><input type="text" placeholder="ADDRRES" name="t3" Required></i>

   
    <div class="flex-container">
     
    <table border="0" align="center">
        <div class="box-container">
            <td colspan="5" align="center"> <p>My Order</p></td>
        <tr>
            <td>#</td>
            <td>Item</td>
            <td>Qty</td>
            <td>discription</td>
            <!-- <td><b> <button>+</button></b></td> -->

        </tr>
        <tr>
            <td>*</td>
            <td><input type="text" placeholder="Item Name"></td>
            <td><input type="number" placeholder="Qty"></td>
            <td><input type="text" placeholder="discription"></td>
            <td> <button>-</button></td>
        </tr>
        <tr>
            <td>*</td>
            <td><input type="text" placeholder="Item Name"></td>
            <td><input type="number" placeholder="Qty"></td>
            <td><input type="text" placeholder="discription"></td>
            <td><button>-</button></td>
        </tr>
        <tr>
            <td>*</td>
            <td><input type="text" placeholder="Item Name"></td>
            <td><input type="number" placeholder="Qty"></td>
            <td><input type="text" placeholder="discription"></td>
            <td><button>-</button> </td>
        </tr>
        <tr>
            <td>*</td>
            <td><input type="text" placeholder="Item Name"></td>
            <td><input type="number" placeholder="Qty"></td>
            <td><input type="text" placeholder="discription"></td>
            <td><button>-</button></td>
        </tr>
        <tr>
            <td>*</td>
            <td><input type="text" placeholder="Item Name"></td>
            <td><input type="number" placeholder="Qty"></td>
            <td><input type="text" placeholder="discription"></td>
            <td><button>-</button></td>
        </tr>
        <tr>
            <td>*</td>
            <td><input type="text" placeholder="Item Name"></td>
            <td><input type="number" placeholder="Qty"></td>
            <td><input type="text" placeholder="discription"></td>
            <td><button>-</button></td>
        </tr>
        <tr>
            <td>*</td>
            <td><input type="text" placeholder="Item Name"></td>
            <td><input type="number" placeholder="Qty"></td>
            <td><input type="text" placeholder="discription"></td>
            <td><button>-</button></td>
        </tr>
        <tr>
            <td>*</td>
            <td><input type="text" placeholder="Item Name"></td>
            <td><input type="number" placeholder="Qty"></td>
            <td><input type="text" placeholder="discription"></td>
            <td><button>-</button></td>
        </tr>
        <tr>
            <td>*</td>
            <td><input type="text" placeholder="Item Name"></td>
            <td><input type="number" placeholder="Qty"></td>
            <td><input type="text" placeholder="discription"></td>
            <td><button>-</button></td>
        </tr>
        
    </div><td>
            <input type="submit" value="Submit" id="sub" name="btn1">
        </td>
    </table>
    </div>
</form>
</body>
</html>


<?php

error_reporting(0);

$conn =mysqli_connect("localhost","root","","siddh");

if(isset($_POST['btn1']))
{
    $a=$_POST['t1'];
    $b=$_POST['t2'];
    $c=$_POST['n1'];
    $d=$_POST['t3'];

   $q= mysqli_query($conn,"insert into orders values('$a','$b','$c','$d')");
   echo '<script> alert("thankyou")</script>';
}

?>