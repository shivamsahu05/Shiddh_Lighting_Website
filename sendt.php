<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Contact Us Form</title>
    <link rel="stylesheet" href="styls.css"> <!-- Link your CSS file -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body> 
    <section id="section-wrapper">
        <div class="box-wrapper">
            <div class="info-wrap">
                <img src="images/logob21.png" alt="Company Logo"> <br>
                <h2 class="info-title">Contact Information</h2>
                <h3 class="info-sub-title">Fill out the form and our team will get back to you within 24 hours</h3>
                <ul class="info-details">
                    <li>
                        <i class="fas fa-phone-alt"><ion-icon name="call-outline"></ion-icon>
                            <span>Phone:</span>
                            <a href="#">7869940934</a>
                        </i> 
                    </li>
                    <li>
                        <i class="fas fa-paper-plane"><ion-icon name="mail-outline"></ion-icon>
                            <span>E-mail:</span>
                            <a href="mailto:infoshiddh@gmail.com">infoshiddh@gmail.com</a>
                        </i>
                    </li>
                    <li>
                        <i class="fas fa-globe"><ion-icon name="globe-outline"></ion-icon>
                            <span>Website:</span>
                            <a href="http://shiddhelectric.com">Shiddhelectric.com</a>
                        </i> 
                    </li>
                </ul>
                <ul class="social-icons">
                    <li>
                        <a href="https://www.facebook.com/profile.php?id=61564997905671">
                            <i class="fab fa-facebook"><ion-icon name="logo-facebook"></ion-icon></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.youtube.com/@SHIDDHLighting2021">
                            <i class="fab fa-youtube"><ion-icon name="logo-youtube"></ion-icon></i>
                        </a>
                    </li> 
                    <li>
                        <a href="#">
                            <i class="fab fa-whatsapp"><ion-icon name="logo-whatsapp"></ion-icon></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/shiddh_lighting_industries">
                            <i class="fab fa-instagram"><ion-icon name="logo-instagram"></ion-icon></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="form-wrap">
            <?php
error_reporting(0);

// Set the time zone to India Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['bt1'])) {
    // Retrieve form data
    $b = $_POST['n1'];   // First Name
    $c = $_POST['n2'];   // Last Name
    $d = $_POST['n3'];   // Email
    $e = $_POST['num'];  // Phone
    $f = $_POST['message']; // Message

    // Handling date and time field
    $g = $_POST['de'];  // Date input from form
    if ($g == '') {
        $g = null;  // Set to NULL if empty date input
    }

    // Insert data into database
    $query = "INSERT INTO feedbackmess (FName, LName, EMail, Phone, Message, sent_date) 
              VALUES ('$b', '$c', '$d', '$e', '$f', '$g')";
    
    if (mysqli_query($conn, $query)) {
        echo '<script>
                alert("You have filled the form and our team will get back to you within 24 hours");
                setTimeout(function() {
                    window.location.href = "index.php";
                }, 1000); // 1 second delay
              </script>';
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

// Close connection
mysqli_close($conn);
?>


                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <h2 class="form-title">Send us a message</h2>
                    <div class="">
        <input type="hidden" name="de" value="<?php echo date('Y-m-d H:i:s'); ?>" required>
    </div>

                    <div class="form-fields">
                        <div class="form-group">
                            <input type="text" class="fname" placeholder="First Name" name="n1" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="lname" placeholder="Last Name" name="n2" required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="email" placeholder="E-mail id" name="n3" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" class="phone" placeholder="Phone" name="num" required>
                        </div>
                        <div class="form-group">
                            <textarea placeholder="Write your message" name="message" required></textarea>
                        </div>
                    </div>
                    <input type="submit" value="Send message" class="submit-button" name="bt1">
                </form>
            </div>
        </div>
    </section>
</body>
</html>
