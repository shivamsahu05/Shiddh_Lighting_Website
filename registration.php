<!DOCTYPE html>
<html  lang=en>
<head>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <title>Registration Form</title>
    <link rel="stylesheet" href="style.css">
    <style>
        h2{

        color: blue;
         text-align: center;
         background-color: aqua;
         padding: 6px;
         
        } 
        h5{

            color: rgb(255, 0, 0);
            text-align: center;
           } 
        #logo{
            float: left;
           height: 36px;
          width: 26px;
          border-radius: 30px;
          background-color: rgb(252, 252, 252);
          margin: 11px;
        }
        #next
        {
            background: rgb(0, 255, 255);
            position: relative;
            top: 5px;
            right: 5px;
            height: 30px;
            width: 70px;
            text-align: center;
            margin: 10px;
            border-radius: 20px;
            cursor: pointer;

        }
        #reset
        {
            background: rgb(0, 255, 255);
            position: relative;
            top: 5px;
            left: 5px;
            height: 30px;
            width: 70px;
            text-align: center;
            margin: 10px;
            border-radius: 20px;
            cursor: pointer;

        }

 </style>
 <script>
    function validate()
    {
        var FullName =document.getElementById("FULL NAME");
        var Gender=document.getElementById("GENDER");
        var Address=document.getElementById("ADDRESS");
        var  Email =document.getElementById("E-MAIL");
        var  MobileNo=document.getElementById("MOBILE");
        var  AdharNo=document.getElementById("ADHAR NO.");
        var DOB=document.getElementById("DOB");
        var R=document.getElementById("RIGHT");

        if(username.value.trim()==" " ||GENDER.value.trim()==" "||ADDRESS.value.trim()==" "||EMAIL.value.trim()==" "||MOBILE.trim()==" "
        ||ADHARNO.value.trim()==" "||DOB.value.trim()==" "||RIGHT.value.trim()==" ")
        {
            alert("no Blank value allowed");
            return false;
            
        }
        
        else
        {
            true;
        }
    }
</script>
</head>
<body bgcolor="black">
<form name="si" method="POST" action="">
    <div class="container">
         <fieldset style="background-image: url('formback.jpg');">  
        <img  src="logo.png" alt="This is an image" height="100">
     
       <hr>
       <div class="box">
        <h2>REGISTRATION</h2>
        
        <h3>YOUR  INFORMATION </h3>
    
        <h5>Required Fileds are Followed by *</h5>
      
       <p>Name : <ion-icon name="person-circle-outline"></ion-icon><input type="text" placeholder="NAME" name="t1"> </p>
        <p>* Last Name :  <input  type="text" name="t2"  placeholder="FULL NAME"required="required"></p>

       
         <p>* Gender  : 
    <input  type="radio" name="gender" id="MALE" value="Male" required="required">Male <input type="radio" name="gender" value="Female" id="Female" required="required">Female
    
      </p>
    
     
    <p> * Address: <ion-icon name="location-outline"></ion-icon>  <textarea name="ADDRESS" id="ADDRESS" cols="50" rows="3" placeholder="ADDRESS" required="required"></textarea></p>
    <p>* E-mail :  <ion-icon name="mail-outline"></ion-icon> <input  type="email" name="EMAIL"  placeholder="E-MAIL ID" required="required"></p>
    <p>* Mobile No. <ion-icon name="call-outline"></ion-icon>  : <input type="number" maxlength="10" name="MOB"  placeholder="MOBILE NUMBER" required="required"></p>
    <p> Pincode : <ion-icon name="navigate-outline"></ion-icon> <input  type="number" name="PINCODE" placeholder="PIN CODE"  required></p>
    <p>* Adhar No. <ion-icon name="person-outline"></ion-icon>  : <input  type="number" maxlength="12" placeholder="ADHAR NUMBER"required="required" name="aadhar"></p>
        
     <p>DATE OF BRITH : 
        <ion-icon name="calendar-outline"></ion-icon>

    <input type="date" name="date">
    <p><input type="password" name="pass" ></p>
     <p>* Right your information: <input type="checkbox" name="R" id="R"required="required"></p>
        <input type="submit" name="btn1"  id="next" >
        <input  type="reset" value="reset" id="reset">
</div>
</div>
    </form>
</body>  
</html
 <?php

$conn =mysqli_connect("localhost","root","","siddh");
//mysql_select_db("siddh");

if(isset($_POST['btn1']))
{
    $a=$_POST['t1'];
    $b=$_POST['t2'];
    $c=$_POST['gender'];
    $d=$_POST['ADDRESS'];
    $e=$_POST['EMAIL'];
    $f=$_POST['MOB'];
    $g=$_POST['PINCODE'];
    $h=$_POST['aadhar'];
    $i=$_POST['date'];

   $q= mysqli_query($conn,"insert into registration values('$a','$b','$c','$d','$e','$f','$g','$h','$i')");

    echo '<script> alert("Saved")</script>';
    header('Location:abc.php');

}

?>




