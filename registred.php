<?php
session_start();

function getPublicIP() {
    $ip = file_get_contents('https://api.ipify.org'); // For IPv4
    return $ip;
}

$public_ip = getPublicIP();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "shiddh");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $a = $_POST['t1'];
    $b = $_POST['t2'];
    $c = $_POST['gender'];
    $d = $_POST['ADDRESS'];
    $e = $_POST['EMAIL'];
    $g = $_POST['PINCODE'];
    $ff = $_POST['MOB'];
    $ot = $_POST['otp'];
    $p = $_POST['pass'];

    // Check if mobile number already exists
    $checkQuery = "SELECT COUNT(*) FROM registration WHERE Phone = ?";
    $checkStmt = $conn->prepare($checkQuery);
    if (!$checkStmt) {
        die("Error in query preparation: " . mysqli_error($conn));
    }
    $checkStmt->bind_param("s", $ff);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo "<script>alert('Mobile number already registered.');</script>";
    } else {
        // Proceed with registration
        $hashed_password = password_hash($p, PASSWORD_BCRYPT);

        $query = "INSERT INTO registration (ShopName, FullName, Gender, Address, Email, Pincode, Phone, otp, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error in query preparation: " . mysqli_error($conn));
        }
        $stmt->bind_param("sssssssss", $a, $b, $c, $d, $e, $g, $ff, $ot, $hashed_password);
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful');</script>";

            $cookie_email = $e;
            $cookie_session_duration = 0;

            $cookie_query = "INSERT INTO user_cookies (email, user_ip, registration_date, registration_time, session_duration, created_at) VALUES (?, ?, CURDATE(), CURTIME(), ?, NOW())";
            $cookie_stmt = $conn->prepare($cookie_query);
            if (!$cookie_stmt) {
                die("Error in query preparation: " . mysqli_error($conn));
            }
            $cookie_stmt->bind_param("sss", $cookie_email, $public_ip, $cookie_session_duration);
            if (!$cookie_stmt->execute()) {
                echo "<script>alert('Error saving cookies data.');</script>";
            }

        } else {
            echo "<script>alert('Error registering. Please try again later.');</script>";
        }
        $stmt->close();
        $cookie_stmt->close();
    }
    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-UIW4dDhp0b+XJ5KRcrXNJv+fmD0z4i2MgYIPja9XLsBZFGZYdzSSSHV8O+XilVsX1K6ryYyLlyI1qJb5pSf7KQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 300px; /* Adjusted logo size for smaller screens */
            height: 120px; /* Adjusted logo size for smaller screens */
            border-radius: 10px; /* Rounded corners for the logo */
        }
        .ragisname {
            font-size: 25px;
        }

        /* Form styling */
        form {
            width: 100%;
        }

        .form-group {
            width: 100%;
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: calc(100% - 20px); /* Adjusted input width */
            padding: 12px; /* Increased padding */
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .form-group input[type="radio"], .form-group input[type="checkbox"] {
            display: none; /* Hide default radio buttons */
        }

        .form-group label.radio-label {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .form-group label.radio-label:hover {
            background-color: #f0f0f0;
        }

        .form-group input[type="radio"]:checked + label.radio-label {
            background-color: #1a73e8;
            color: #ffffff;
            border-color: #1a73e8;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: #1a73e8;
        }

        .otp-button {
            background-color: #4caf50;
            color: #ffffff;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .otp-button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
            width: 100%;
            text-align: center;
        }
        .password-strength {
            margin-top: 10px;
        }

        .weak {
            color: red;
        }

        .medium {
            color: orange;
        }

        .strong {
            color: green;
        }
        .submit-section #login-link {
            display: inline-block;
            margin-right: 20px; /* Adjust as needed */
            padding: 10px 20px; /* Adjust padding for link */
            font-size: 16px;
            text-decoration: none;
            color: #1a73e8; /* Link color */
            border: 1px solid #1a73e8;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }

        .submit-section #login-link:hover {
            background-color: #1a73e8;
            color: #ffffff;
            border-color: #1a73e8;
        }

        .submit-section {
            text-align: center;
            width: 100%;
        }

        .submit-section button, .submit-section input[type="submit"], .submit-section input[type="reset"] {
            background-color: #1a73e8;
            color: #ffffff;
            border: none;
            padding: 14px 32px; /* Increased padding */
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .submit-section button:hover, .submit-section input[type="submit"]:hover, .submit-section input[type="reset"]:hover {
            background-color: #0d47a1;
        }

        /* Media queries for responsive design */
        @media screen and (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .logo img {
                width: 220px; /* Adjusted logo size for smaller screens */
                height: 90px; /* Adjusted logo size for smaller screens */
            }
            .ragisname{

            }

            .form-group input, .form-group textarea, .form-group select {
                font-size: 14px;
            }

            .submit-section button, .submit-section input[type="submit"], .submit-section input[type="reset"] {
                padding: 12px 24px; /* Adjusted padding for smaller screens */
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo-container">
        <div class="logo">
            <img src="images/logob21.png" alt="Logo">
        </div>
    </div>
    <div class="ragisname">
        <b>!!<u>Registration Form</u>!!</b> 
    </div>
    <br>
    <form name="registrationForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="t1">Shop Name: <span style="color: red;">*</span></label>
            <input type="text" id="t1" name="t1" placeholder="Enter Shop Name" required>
        </div>
        
        <div class="form-group">
            <label for="t2">Full Name: <span style="color: red;">*</span></label>
            <input type="text" id="t2" name="t2" placeholder="Enter Full Name" required>
        </div>
        
        <div class="form-group">
            <label>Gender: <span style="color: red;">*</span></label>
            <br>
            <input type="radio" id="male" name="gender" value="Male" required>
            <label for="male" class="radio-label">Male</label>
            
            <input type="radio" id="female" name="gender" value="Female" required>
            <label for="female" class="radio-label">Female</label>
        </div>
        
        <div class="form-group">
            <label for="ADDRESS">Address: <span style="color: red;">*</span></label>
            <textarea id="ADDRESS" name="ADDRESS" rows="3" placeholder="Enter Address" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="PINCODE">Pincode: <span style="color: red;">*</span></label>
            <input type="number" id="PINCODE" name="PINCODE" placeholder="Enter Pincode" required>
        </div>
  
        <div class="form-group">
            <label for="MOB">Mobile No.: <span style="color: red;">*</span></label>
            <input type="number" id="MOB" name="MOB" placeholder="Enter Mobile Number" required>
            

        </div>
       
        <div class="form-group">
            <label for="EMAIL">E-mail: <span style="color: red;">*</span></label>
            <input type="email" id="EMAIL" name="EMAIL" placeholder="Enter E-mail ID" required>
            <br>
            <br>
        <button type="button" class="otp-button" onclick="requestOTP()">Request OTP By E-mail</button>
        </div>  
      
        <div class="form-group">
            <label for="otp">OTP: <span style="color: red;"></span></label>
            <input type="text" id="otp" name="otp" placeholder="Enter OTP" >
        </div>
        <div class="form-group">
            <label for="pass">Password: <span style="color: red;">*</span></label>
            <input type="password" id="pass" name="pass" placeholder="Enter Password" required onkeyup="checkPasswordStrength(this.value)">
            <div id="password-strength" class="password-strength"></div>
        </div>
        
        <div class="form-group">
            <label for="R">I agree to the terms and conditions</label>
        </div>
        
        <div class="submit-section">
            <a href="login.php" id="login-link">Login</a>
            <button type="submit" name="btn1">Submit</button>
            <input type="reset" value="Reset">
        </div>
        
        <div class="error-message" id="error-message"></div>
    </form>
</div>

<script>
    // Capture the time when the page is loaded
    var startTime = new Date().getTime();

    // Function to handle when the user leaves the page
    function trackTime() {
        var endTime = new Date().getTime();
        var duration = (endTime - startTime) / 1000; // Duration in seconds

        // Send the duration to the server using an AJAX request
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "save_duration.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("duration=" + duration + "&email=" + encodeURIComponent(document.getElementById("EMAIL").value));
    }

    // Track when the page is unloaded
    window.addEventListener("beforeunload", trackTime);

    // Function to set a cookie
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    // Set the email cookie when form is submitted
    document.forms["registrationForm"].onsubmit = function() {
        var email = document.getElementById("EMAIL").value;
        setCookie("email", email, 7); // Store email in a cookie for 7 days
    };

    function requestOTP() {
        // Simulate OTP request (replace with actual functionality)
        alert('OTP request sent to your E-mail ID !');
    }

    function checkPasswordStrength(password) {
        var strength = 0;

        // Minimum length check
        if (password.length >= 8) {
            strength += 1;
        }

        // Contains lowercase and uppercase letters
        if (password.match(/[a-z]+/) && password.match(/[A-Z]+/)) {
            strength += 1;
        }

        // Contains numbers
        if (password.match(/[0-9]+/)) {
            strength += 1;
        }

        // Contains special characters
        if (password.match(/[$@#&!]+/)) {
            strength += 1;
        }

        var strengthText;
        if (strength < 3) {
            strengthText = 'Weak';
            document.getElementById('password-strength').className = 'password-strength weak';
        } else if (strength < 5) {
            strengthText = 'Medium';
            document.getElementById('password-strength').className = 'password-strength medium';
        } else {
            strengthText = 'Strong';
            document.getElementById('password-strength').className = 'password-strength strong';
        }

        document.getElementById('password-strength').innerText = 'Password Strength: ' + strengthText;
    }
</script>

</body>
</html>
