<?php
session_start();

// Function to get public IP
function getPublicIP() {
    return file_get_contents('https://api.ipify.org'); // For IPv4
}

// Get public IP
$public_ip = getPublicIP();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CAPTCHA verification
    $captchaResponse = $_POST['g-recaptcha-response'];
    $secretKey = '6Ld35yUqAAAAAJIVznbLeUM4SmsuG_3u-yWoocuI'; // Replace with your actual secret key

    // Verify the CAPTCHA
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse");
    $responseKeys = json_decode($response, true);

    if (!$responseKeys["success"]) {
        echo "<script>alert('CAPTCHA verification failed. Please try again.');</script>";
    } else {
        // Continue with the rest of your registration process
        $conn = mysqli_connect("localhost", "root", "", "shiddh");
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable detailed error reporting

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $shopName = $_POST['t1'];
        $fullName = $_POST['t2'];
        $gender = $_POST['gender'];
        $address = $_POST['ADDRESS'];
        $email = $_POST['EMAIL'];
        $pincode = $_POST['PINCODE'];
        $mobile = $_POST['MOB'];
        $otp = $_POST['otp'];
        $password = $_POST['pass'];
        $confirm_pass = $_POST['confirm_pass'];

        // Check if passwords match
        if ($password !== $confirm_pass) {
            echo "<script>alert('Passwords do not match.');</script>";
        } else if ($fullName === 'admin' && $password === '1234') {
            // Prevent registration for "admin" with password "1234"
            echo "<script>alert('This username is not allowed.');</script>";
        } else {
            // Check mobile number length
            if (strlen($mobile) != 10 || !ctype_digit($mobile)) {
                echo "<script>alert('Mobile number must be exactly 10 digits.');</script>";
            } else {
                // Check if mobile number already exists
                $checkQuery = "SELECT COUNT(*) FROM registration WHERE Phone = ?";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param("s", $mobile);
                $checkStmt->execute();
                $checkStmt->bind_result($count);
                $checkStmt->fetch();
                $checkStmt->close();

                if ($count > 0) {
                    echo "<script>alert('Already registered with this mobile number.');</script>";
                } else {
                    // Proceed with registration
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                    $query = "INSERT INTO registration (ShopName, FullName, Gender, Address, Email, Pincode, Phone, otp, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssssssss", $shopName, $fullName, $gender, $address, $email, $pincode, $mobile, $otp, $hashed_password);
                    if ($stmt->execute()) {
                        echo "<script>alert('Registration successful');</script>";

                        $session_duration = 0; // Set a default value for session_duration

                        // Insert into user_cookies table
                        $cookie_query = "INSERT INTO user_cookies (email, user_ip, registration_date, registration_time, created_at, session_duration) VALUES (?, ?, CURDATE(), CURTIME(), NOW(), ?)";
                        $cookie_stmt = $conn->prepare($cookie_query);
                        if ($cookie_stmt === false) {
                            die("Prepare failed: " . htmlspecialchars($conn->error));
                        }
                        $cookie_stmt->bind_param("ssi", $email, $public_ip, $session_duration);
                        if (!$cookie_stmt->execute()) {
                            echo "<script>alert('Error saving cookies data: " . htmlspecialchars($cookie_stmt->error) . "');</script>";
                        }

                    } else {
                        echo "<script>alert('Error registering. Please try again later.');</script>";
                    }
                    $stmt->close();
                    $cookie_stmt->close();
                }
                $conn->close();
            }
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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
            width: 250px;
            height: auto;
            border-radius: 10px;
        }

        .ragisname {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
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
            font-size: 16px;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: #1a73e8;
            outline: none;
        }

        .form-group input[type="radio"], .form-group input[type="checkbox"] {
            display: none;
        }

        .form-group label.radio-label {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: 1px solid #ddd;
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

        .otp-button {
            background-color: #4caf50;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .otp-button:hover {
            background-color: #45a049;
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
        .password-match {
            margin-top: 10px;
        }

        .password-match.valid {
            color: green;
        }

        .password-match.invalid {
            color: red;
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
            padding: 14px 30px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .submit-section button:hover, .submit-section input[type="submit"]:hover, .submit-section input[type="reset"]:hover {
            background-color: #0d47a1;
        }

        .error-message {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
            width: 100%;
            text-align: center;
        }
/* CAPTCHA section styling */
.captcha-group {
    position: relative;
    margin-bottom: 20px;
}

.captcha-container {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.captcha-text {
    font-size: 18px;
    font-weight: bold;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
    color: #333;
    width: 120px; /* Adjust as needed */
    text-align: center;
    margin-right: 10px;
    word-break: break-all;
}

.refresh-captcha {
    background-color: #1a73e8;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.refresh-captcha:hover {
    background-color: #0d47a1;
}

.captcha-input {
    width: calc(100% - 150px); /* Adjust width based on the size of the CAPTCHA container and button */
}

.error-message {
    color: #f44336;
    font-size: 14px;
    margin-top: 5px;
    width: 100%;
    text-align: center;
}

        @media screen and (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .logo img {
                width: 200px;
            }

            .form-group input, .form-group textarea, .form-group select {
                font-size: 14px;
            }

            .submit-section button, .submit-section input[type="submit"], .submit-section input[type="reset"] {
                padding: 12px 24px;
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
        <b>!! <u>Registration Form</u> !!</b> 
    </div>
    <form method="post" action="">
        <div class="form-group">
            <label for="shop-name">Shop Name <span style="color: red;">*</span></label>
            <input type="text" id="shop-name" name="t1" placeholder="Enter your shop name" required>
        </div>
        <div class="form-group">
            <label for="full-name">Full Name <span style="color: red;">*</span></label>
            <input type="text" id="full-name" name="t2" placeholder="Enter your full name" required>
        </div>
        <div class="form-group">
            <label>Gender <span style="color: red;">*</span></label>
            <input type="radio" id="male" name="gender" value="Male" required>
            <label class="radio-label" for="male">Male</label>
            <input type="radio" id="female" name="gender" value="Female" required>
            <label class="radio-label" for="female">Female</label>
        </div>
        <div class="form-group">
            <label for="address">Address <span style="color: red;">*</span></label>
            <textarea id="address" name="ADDRESS" rows="4" placeholder="Enter your address" required></textarea>
        </div>
        <div class="form-group">
            <label for="pincode">Pincode <span style="color: red;">*</span></label>
            <input type="text" id="pincode" name="PINCODE" placeholder="Enter your pincode" required>
        </div>
        <div class="form-group">
            <label for="mobile">Mobile Number <span style="color: red;">*</span></label>
            <input type="text" id="mobile" name="MOB" placeholder="Enter your mobile number" required maxlength="10" oninput="validateMobileNumber()">
            <div class="error-message" id="mobile-error">Mobile number must be exactly 10 digits.</div>
        </div>
        <div class="form-group">
            <label for="email">Email <span style="color: red;">*</span></label>
            <input type="email" id="email" name="EMAIL" placeholder="Enter your email address" required oninput="validateFields()">
            <br>
            <br>
            <button type="button" class="otp-button" onclick="sendOTP()">Send OTP</button>
        </div>
       
        <div class="form-group">
    <label for="otp">OTP <span style="color: red;">*</span></label>
    <input type="text" id="otp" name="otp" placeholder="Enter OTP" required oninput="validateFields()">
</div>


<div class="form-group captcha-group">
    <label for="captcha">CAPTCHA <span style="color: red;">*</span></label>
    <div class="captcha-container">
        <span id="captcha-text" class="captcha-text" readonly></span>
        <button type="button" class="refresh-captcha" onclick="refreshCaptcha()">Refresh</button>
    </div>
    <input type="text" id="captcha-input" name="captcha_input" placeholder="Enter CAPTCHA" required>
    <div class="error-message" id="captcha-error" style="display: none;">CAPTCHA is incorrect. Please try again.</div>
</div>
        <div class="form-group">
    <label for="password">Password <span style="color: red;">*</span></label>
    <input type="password" id="password" name="pass" placeholder="Enter your password" required disabled oninput="checkPasswordStrength(); validatePasswords();">
    <button type="button" onclick="togglePassword('password', this)" disabled>Show Password</button>
    <div class="password-strength" id="password-strength"></div>
</div>
<div class="form-group">
    <label for="confirm-password">Confirm Password <span style="color: red;">*</span></label>
    <input type="password" id="confirm-password" name="confirm_pass" placeholder="Re-enter your password" required disabled oninput="validatePasswords();">
    <button type="button" onclick="togglePassword('confirm-password', this)" disabled>Show Password</button>
    <div class="password-match" id="password-match"></div>
</div>



<div class="form-group">
    <label for="captcha"> <span style="color: red;"></span></label>
    <div class="g-recaptcha" data-sitekey="6Ld35yUqAAAAABH6KrWXnYg59C1m6JS5FEnuox64"></div>
</div>


        <div class="form-group">
            <label for="R">I agree to the terms and conditions</label>
            <input type="checkbox" id="R" name="terms">
        </div>
        <div class="submit-section">
            <a href="login.php" id="login-link">Login</a>
            <input type="submit" value="Register">
            <input type="reset" value="Reset">
        </div>
    </form>
</div>

<script>
    let captchaText = '';

// Function to generate a random CAPTCHA
function generateCaptcha() {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    const length = 6; // Length of the CAPTCHA text
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        result += characters[randomIndex];
    }
    captchaText = result;
    document.getElementById('captcha-text').textContent = captchaText;
}

// Function to validate the CAPTCHA input
function validateCaptcha() {
    const userInput = document.getElementById('captcha-input').value;
    const errorMessage = document.getElementById('captcha-error');
    
    if (userInput === captchaText) {
        errorMessage.style.display = 'none';
        return true; // CAPTCHA is correct
    } else {
        errorMessage.style.display = 'block';
        return false; // CAPTCHA is incorrect
    }
}

// Function to refresh the CAPTCHA
function refreshCaptcha() {
    generateCaptcha(); // Regenerate the CAPTCHA text
}

// Initialize CAPTCHA on page load
document.addEventListener('DOMContentLoaded', function() {
    generateCaptcha(); // Generate CAPTCHA when the page loads
});

// Example form submission handler
document.querySelector('form').addEventListener('submit', function(event) {
    if (!validateCaptcha()) {
        event.preventDefault(); // Prevent form submission if CAPTCHA is incorrect
    }
});


    function sendOTP() {
    var emailField = document.getElementById('email');
    var email = emailField.value;

    if (email) {
        // Simulate OTP request (replace with actual functionality)
        alert('OTP request sent to your E-mail ID: ' + email);
    } else {
        // Show message if email is not provided
        alert('Please enter your email ID.');
    }
}

    function validateMobileNumber() {
        var mobileField = document.getElementById('mobile');
        var errorMessage = document.getElementById('mobile-error');
        var mobileNumber = mobileField.value;

        if (mobileNumber.length > 10) {
            mobileField.value = mobileNumber.slice(0, 10);
        }

        if (mobileNumber.length === 10 && /^[0-9]+$/.test(mobileNumber)) {
            errorMessage.style.display = 'none';
        } else {
            errorMessage.style.display = 'block';
        }

        validateFields();
    }

    function validateFields() {
    var email = document.getElementById('email').value;
    var otp = document.getElementById('otp').value;
    var mobile = document.getElementById('mobile').value;
    var passwordField = document.getElementById('password');
    var confirmPasswordField = document.getElementById('confirm-password');
    var showPasswordButtons = document.querySelectorAll('button[onclick*="togglePassword"]');

    // Enable password fields only if email, OTP, and mobile are filled
    if (email && otp && mobile.length === 10) {
        passwordField.removeAttribute('disabled');
        confirmPasswordField.removeAttribute('disabled');
        showPasswordButtons.forEach(button => button.removeAttribute('disabled'));
    } else {
        passwordField.setAttribute('disabled', 'true');
        confirmPasswordField.setAttribute('disabled', 'true');
        showPasswordButtons.forEach(button => button.setAttribute('disabled', 'true'));
    }

    validatePasswords();
}


    function checkPasswordStrength() {
        var password = document.getElementById('password').value;
        var strengthText = '';
        var strengthClass = '';

        if (password.length >= 4) {
            var hasLower = /[a-z]/.test(password);
            var hasUpper = /[A-Z]/.test(password);
            var hasNumber = /[0-9]/.test(password);
            var hasSpecial = /[!@#$%^&*]/.test(password);

            if (password.length >= 6 && hasLower && hasUpper && hasNumber && hasSpecial) {
                strengthText = 'Strong';
                strengthClass = 'strong';
            } else if (password.length >= 4 && (hasLower || hasUpper) && hasNumber) {
                strengthText = 'Medium';
                strengthClass = 'medium';
            } else {
                strengthText = 'Weak';
                strengthClass = 'weak';
            }
        } else {
            strengthText = 'Too short';
            strengthClass = 'too-short';
        }

        document.getElementById('password-strength').textContent = strengthText;
        document.getElementById('password-strength').className = strengthClass;
    }

    function togglePassword() {
        var passwordField = document.getElementById('password');
        var confirmPasswordField = document.getElementById('confirm-password');
        var type = passwordField.type === 'password' ? 'text' : 'password';
        
        passwordField.type = type;
        confirmPasswordField.type = type;
    }
</script>


</body>
</html>
