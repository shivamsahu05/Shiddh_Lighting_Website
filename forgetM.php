<?php
error_reporting(0);

// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn2'])) {
    $Email = $_POST['t1'];
    $UserCaptcha = $_POST['captcha'];
    $CaptchaText = $_POST['captcha']; // Read CAPTCHA value from hidden input

    // Validate CAPTCHA
    if ($UserCaptcha !== $CaptchaText) {
        echo '<script>alert("Invalid CAPTCHA")</script>';
        exit;
    }

    // Using prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM registration WHERE Phone = ?");
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Email exists in the database, redirect to password update page
        header('Location: updatepassM.php'); 
        exit;
    } else {
        // No user found with the provided email
        echo '<script>alert("Invalid Mobile Number")</script>';
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password via Mobile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #121212;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #e0e0e0;
        }

        .main {
            width: 100%;
            max-width: 360px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .box {
            border: 2px solid #00bcd4;
            background: #1e1e1e;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        img {
            width: 150px;
            margin-bottom: 20px;
        }

        h1 {
            color: #00bcd4;
            margin-bottom: 20px;
            font-size: 24px;
        }

        input[type="text"],
        input[type="tel"] {
            width: calc(100% - 22px);
            height: 40px;
            background: #333;
            border: 1px solid #00bcd4;
            border-radius: 5px;
            outline: none;
            color: #e0e0e0;
            font-size: 16px;
            margin: 10px 0;
            padding: 0 10px;
        }

        input[type="text"]::placeholder,
        input[type="tel"]::placeholder {
            color: #b0bec5;
        }

        input[type="submit"] {
            width: 100%;
            height: 45px;
            cursor: pointer;
            outline: none;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            background: linear-gradient(to right, #ff5722, #00bcd4);
            color: #fff;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        input[type="submit"]:hover {
            background: linear-gradient(to right, #00bcd4, #ff5722);
            transform: scale(1.05);
        }

        .link {
            color: #00bcd4;
            font-size: 14px;
            margin: 10px 0;
            display: block;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        p {
            font-size: 14px;
            margin-top: 20px;
        }

        #log {
            color: #00bcd4;
            font-weight: bold;
            text-decoration: none;
        }

        #log:hover {
            text-decoration: underline;
        }

        .captcha-container {
            margin: 15px 0;
            color: #e0e0e0;
            text-align: center;
        }

        #captcha-text {
            font-size: 24px;
            font-weight: bold;
            background: #333;
            padding: 10px;
            border: 1px solid #00bcd4;
            display: inline-block;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        #captcha-error {
            color: #ff5252;
            display: none;
            font-size: 14px;
        }

        .captcha-buttons {
            margin-top: 10px;
        }

        .captcha-buttons button {
            background: #00bcd4;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .captcha-buttons button:hover {
            background: #0097a7;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="box">
            <img src="images/logosl.png" alt="Logo" width="150">
            <h1><u>Forget Password</u></h1>            <a href="forgetE.php" class="link">Try Another Way?</a>
            <form id="password-reset-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <input type="tel" name="t1" id="mobile" placeholder="Enter Mobile Number" required>
                
                <!-- CAPTCHA Section -->
                <div class="captcha-container">
                    <div id="captcha-text"></div>
                    <input type="text" id="captcha-input" placeholder="Enter CAPTCHA" required>
                    <span id="captcha-error">CAPTCHA is incorrect</span>
                    <div class="captcha-buttons">
                        <button type="button" onclick="refreshCaptcha()">Refresh</button>
                    </div>
                </div>
                
                <input type="hidden" name="captcha" id="captcha-hidden">
                <input type="submit" value="Submit" id="but" name="btn2">
            </form>
            <p>Already Have an Account? <a href="login.php" id="log">Login</a></p>
        </div>
    </div>

    <script>
        let captchaText = '';

        function generateCaptcha() {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            const length = 6;
            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                result += characters[randomIndex];
            }
            captchaText = result;
            document.getElementById('captcha-text').textContent = captchaText;
            document.getElementById('captcha-hidden').value = captchaText;
        }

        function validateCaptcha() {
            const userInput = document.getElementById('captcha-input').value;
            const errorMessage = document.getElementById('captcha-error');
            
            if (userInput === captchaText) {
                errorMessage.style.display = 'none';
                return true;
            } else {
                errorMessage.style.display = 'block';
                return false;
            }
        }

        function refreshCaptcha() {
            generateCaptcha();
        }

        document.addEventListener('DOMContentLoaded', function() {
            generateCaptcha();
        });

        document.getElementById('password-reset-form').addEventListener('submit', function(event) {
            if (!validateCaptcha()) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
