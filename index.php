<?php
// Error reporting and session start
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
date_default_timezone_set('Asia/Kolkata');

if (isset($_POST['btn1'])) {
    $a = $_POST['name'];      // Customer name
    $b = $_POST['phone'];     // Mobile number
    $c = $_POST['email'];     // Email 
    $d = $_POST['message'];   // Message
    $e = $_POST['datetime'];  // Date & Time hidden field

    // Validate mobile number
    if (!isset($b) || strlen($b) !== 10 || !is_numeric($b)) {
        echo '<script> alert("Please enter a valid 10-digit mobile number."); </script>';
        exit(); // Stop further execution if validation fails
    }

    // Prepare the query
    $query = "INSERT INTO indexmess (c_Name, Phone, Email, Message, Date_Send) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sisss", $a, $b, $c, $d, $e); 

    // Execute the query
    if ($stmt->execute()) {
        echo '<script> alert("Message sent successfully."); </script>';
        header('Location: sendt.php'); // Redirect to success page or the same page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Website</title>
    <link rel="stylesheet" href="indexstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <input type="checkbox" id="check">
    <nav>
        <div class="icon"><img src="images/logosl.png" alt="logo" width="200"></div>
        <div class="search_box">
            <input type="search" id="searchInput" placeholder="Search here">
            <span><ion-icon name="search-outline"></ion-icon></span>
        </div>
        <div id="searchTag"></div> <!-- Add this div to display search tag -->
        
        <ol>
            <li><a href="registerd.php">SingUp</a></li>
            <li><a href="login.php">Signin</a></li>
            <li><a href="service.html">Service</a></li>
            <li><a href="aboutus.html">About</a></li>
            <li><a href="sendt.php">Contact</a></li>
            <li><a href="ourproduct.php">Product</a></li>
        </ol>
        <label for="check" class="bar">
            <span><ion-icon name="menu-outline"></ion-icon></span>
            <span><ion-icon name="close-outline"></ion-icon></span>
        </label>
    </nav>
    
    <section class="slider-container">
        <div class="slider">
            <div class="slides">
                <div class="slide">
                    <img src="images/logoscreen (2).png" alt="Slider Image 1">
                    <div class="content-overlay">
                        <div class="content">
                            <h1>Welcome to our website</h1>
                            <p id="contentText">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at libero sed justo dignissim sollicitudin at at mauris.
                            </p>
                        </div>
                        
                    </div>
                </div>
                <div class="slide">
                    <img src="images/logoscreen (2).png" alt="Slider Image 2">
                    <div class="content-overlay">
                        <div class="content">
                            <h1>Welcome to our website</h1>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at libero sed justo dignissim sollicitudin at at mauris.</p>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <img src="images/logoscreen (2).png" alt="Slider Image 3">
                    <div class="content-overlay">
                        <div class="content">
                            <h1>Welcome to our website</h1>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at libero sed justo dignissim sollicitudin at at mauris.</p>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <img src="images/logoscreen (2).png" alt="Slider Image 4">
                    <div class="content-overlay">
                        <div class="content">
                            <h1>Welcome to our website</h1>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at libero sed justo dignissim sollicitudin at at mauris.</p>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <img src="images/logoscreen (2).png" alt="Slider Image 5">
                    <div class="content-overlay">
                        <div class="content">
                            <h1>Welcome to our website</h1>
                            <p id="contentText">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at libero sed justo dignissim sollicitudin at at mauris.
                            </p>                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dots">
            <span class="dot active" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
            <span class="dot" onclick="currentSlide(4)"></span>
            <span class="dot" onclick="currentSlide(5)"></span>
        </div>
        <div class="scrolling-text">
        <div class="scrolling-text">
            <marquee behavior="alternate" direction="right" scrollamount="14"> <h1>SHIDDH Lighting Industries <sup>2021</sup></h1></marquee> 
        </div>
    </div>
    </section>
    
    <!-- Products Section -->
    <div class="products">
        <!-- Product 1 -->
        <div class="product">
            <img src="images/led-night-bulb.jpg" alt="Product 1">
            <h3>0.5 Watt</h3>
            <p>Watt: 9<br>Type of bulb: Cool Multiple Colors<br>Made in: INDIA<br>Type: b22</p>
            
        </div>
        <div class="product">
            <img src="images/philips-led-bulb.jpg" alt="Product 2">
            <h3>9 Watt </h3>
            <p>Watt: 9<br>Type of bulb: Cool Multiple Colors<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/led-t-bulb.jpg" alt="Product 3">
            <h3>T Bulb</h3>
            <p>Watt: 10 W<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/9w-hpf-bulb-driver-with-emc.jpg" alt="Product 4">
            <h3>5-9 Watt Driver</h3>
            <p><br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/acdc-led-bulb.jpg" alt="Product 5">
            <h3>9 Watt </h3>
            <p>Watt: 9<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22 <br>Backup Hours: 4 Hours</p>
        </div>
        <div class="product">
            <img src="images/color-led-tubelight.jpg" alt="Product 6">
            <h3>20 Watt</h3>
            <p>Watt: 20<br>Type of bulb: Cool Multiple Colors<br>Made in: INDIA</p>
        </div>
        
        <div class="product">
            <img src="images/led-concealed-light-tiranga.jpg" alt="Product 7">
            <h3>6 Watt </h3>
            <p>Watt: 6<br>Type of bulb: Cool Multiple Colors<br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/philips-led-bulb.jpg" alt="Product 8">
            <h3>12 Watt </h3>
            <p>Watt: 12<br>Type of bulb: Cool White Colors<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/led-bulb.jpg" alt="Product 9">
            <h3>15 Watt </h3>
            <p>Watt: 15<br>Type of bulb: Cool White Colors<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/philips-led-bulb.jpg" alt="Product 10">
            <h3>18 Watt </h3>
            <p>Watt: 18<br>Type of bulb: Cool White Colors<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/box20w.jpg" alt="Product 11">
            <h3>20 Watt </h3>
            <p>Watt: 20<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/high-watt-40.jpg" alt="Product 12">
            <h3>40 Watt </h3>
            <p>Watt: 40<br>Type of bulb: Cool White Colors<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/high-watt-55.jpg" alt="Product 12">
            <h3>55 Watt </h3>
            <p>Watt: 55<br>Type of bulb: Cool White Colors<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/led-cob-light.jpg" alt="Product 13">
            <h3>6 Watt Cob Light </h3>
            <p>Watt: 6<br>Type of bulb: Cool White & WW <br>Made in: INDIA</p>
        </div> 
        <div class="product">
            <img src="images/led-panel-light.jpg" alt="Product 14">
            <h3>8 Watt Panel Light </h3>
            <p>Watt: 8<br>Type of bulb: Cool Multiple Colors<br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/led-surface-panel-light.jpg" alt="Product 15">
            <h3>8 Watt Surface Panel Light</h3>
            <p>Watt: 8<br>Type of bulb: Cool Multiple Colors<br>Made in: INDIA</p>
        </div>
       
        <div class="product">
            <img src="images/led-concealed-light-packaging-box.jpg" alt="Product 16">
            <h3>3 Watt </h3>
            <p>Watt: 3<br>Type of bulb: Cool White & Warm White<br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/led-dob-bulb.jpg" alt="Product 17">
            <h3>9 Watt Dob Non Warranty</h3>
            <p>Watt: 9<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/syskaled.webp" alt="Product 18">
            <h3>9 Watt sh Bulb</h3>
            <p>Watt: 9<br>Type of bulb: Cool White & Warm White<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/all-rounder-bulb.jpg" alt="Product 18">
            <h3>3 in 1 All Rounder Bulb</h3>
            <p>Watt: 15, 12, 0.5 W<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22</p>
        </div>
        <div class="product">
            <img src="images/led-street-light20.jpg" alt="Product 19">
            <h3>20 Watt Street Light </h3>
            <p>Watt: 20<br>Type of bulb: Cool White<br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/led-street-light30.jpg" alt="Product 20">
            <h3>30 Watt Street Light </h3>
            <p>Watt: 30<br>Type of bulb: Cool White<br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/led-street-light50.jpg" alt="Product 21">
            <h3>50 Watt Street Light </h3>
            <p>Watt: 50<br>Type of bulb: Cool White<br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/led-street-light100.jpg" alt="Product 22">
            <h3>100 Watt Street Light </h3>
            <p>Watt: 100<br>Type of bulb: Cool White<br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/led-flood-light30.jpg" alt="Product 23">
            <h3>30 Watt Flood Light </h3>
            <p>Watt: 30<br>Type of bulb: Cool White<br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/led-flood-light50.jpg" alt="Product 24">
            <h3>50 Watt Flood Light </h3>
            <p>Watt: 30<br>Type of bulb: Cool White<br>Made in: INDIA</p>
        </div>
        <div class="product">
            <img src="images/led-flood-light100.jpg" alt="Product 25">
            <h3>100 Watt Flood Light </h3>
            <p>Watt: 100<br>Type of bulb: Cool White<br>Made in: INDIA</p>
        </div>

    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section about">
                    <h2>About Us</h2>
                    <p>We are a recognized organization in the industry, involved in manufacturing, importing, and supplying a commendable array of indoor and outdoor lighting products.</p>
                    <div class="contact">
                        <span><ion-icon name="location-outline"></ion-icon> Address: Sohawal, Satna, M.P. 485441</span>
                        <p><span><ion-icon name="mail-outline"></ion-icon> Email: <a href="mailto:infoshiddh@gmail.com" style="color: #ccc;">infoshiddh@gmail.com</a></span></p>
                    </div>
                    <div class="socials">
                        <a href="https://www.facebook.com/profile.php?id=61564997905671"><ion-icon name="logo-facebook"></ion-icon></a>
                        <a href="https://www.instagram.com/shiddh_21?igsh=MWJjdmU0ZGowODBocA=="><ion-icon name="logo-instagram"></ion-icon></a>
                        <a href="https://www.youtube.com/@SHIDDHLightingIndustries"><ion-icon name="logo-youtube"></ion-icon></a>
                        <a href="https://www.linkedin.com/in/shiddh-lighting-industries-447065325/"><ion-icon name="logo-linkedin"></ion-icon></a>
                    </div>
                </div>

                <!-- Company Rating Section -->
                <div class="footer-section company-rating">
                    <h2>Company Rating</h2>
                    <div class="rating-stars">
                        <!-- Replace with actual star icons if desired -->
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <i style="font-size:24px" class="fa">&#xf123;</i>
                    
                    <div style="color: white;">
                        
                        <p>Rating: <strong>4.6</strong></p>
                        <p>Rating Percentage: <strong>88%</strong></p>
                    </div>
                    </div>
                </div>

                <div class="footer-section links">
                    <h2>Quick Links</h2>
                    <ul>
                        <li><a href="aboutus.html">About Us</a></li>
                        <li><a href="termcondition.html">Terms & Conditions</a></li>
                        <!-- <li><a href="#">Privacy Policy</a></li> -->
                        <li><a href="login.php">Signin</a></li>
                        <li><a href="registred.php">SingUp</a></li>
                        <li><a href="service.html">Service</a></li>
                        <li><a href="sendt.php">Contact Us</a></li>
                    </ul>
                </div>

                <div class="footer-section contact-form">
                    <h2>Contact Us</h2>
                    <form action="" method="POST">
                        <input type="text" name="name" class="text-input contact-input" placeholder="Your Full Name" required>
                        <input type="number" name="phone" class="text-input contact-input" placeholder="Your Mobile Number !0 Digit" required>
                        <input type="email" name="email" class="text-input contact-input" placeholder="Your Email" required>
                        <textarea rows="4" name="message" class="text-input contact-input" placeholder="Your Message" required></textarea>
                        <input type="hidden" name="datetime" value="<?php echo date('Y-m-d H:i:s'); ?>">
                        <button type="submit" value="send" name="btn1" class="btn btn-big contact-btn">
                            <ion-icon name="paper-plane-outline"></ion-icon> Send
                        </button>
                    </form>
                </div>
            </div>
            <p class="footer-bottom">Copyright &copy; 2024 | Designed by Shivam</p>
        </div>
    </footer>

    <!-- JavaScript for Slider -->
    <script>
        const slides = document.querySelector('.slides');
        const dots = document.querySelectorAll('.dot');
        let slideIndex = 0;

        function showSlides() {
            slides.style.transition = 'transform 0.5s ease-in-out';
            slides.style.transform = `translateX(-${slideIndex * 100}%)`;
            dots.forEach(dot => dot.classList.remove('active'));
            dots[slideIndex].classList.add('active');
        }

        function currentSlide(index) {
            slideIndex = index - 1;
            showSlides();
        }

        setInterval(() => {
            slideIndex = (slideIndex + 1) % dots.length;
            showSlides();
        }, 1700); // Auto slide every 3 seconds


    </script>
</body>
</html>

