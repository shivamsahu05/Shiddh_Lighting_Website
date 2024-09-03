<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Password</title>
    <link rel="stylesheet" href="stylec.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>    
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        var passwordField = document.getElementById('password');
        var togglePassword = document.getElementById('togglePassword');
        var passwordVisible = false;

        togglePassword.addEventListener('click', function () {
            if (!passwordVisible) {
                passwordField.setAttribute('type', 'text');
                togglePassword.classList.add('active');
                passwordVisible = true;
            } else {
                passwordField.setAttribute('type', 'password');
                togglePassword.classList.remove('active');
                passwordVisible = false;
            }
        });

        passwordField.addEventListener('input', function () {
            var password = passwordField.value;
            var strength = '';

            if (password.length == 0) {
                strength = '';
            } else if (password.length < 6) {
                strength = 'Weak';
            } else if (password.length < 10) {
                strength = 'Medium';
            } else {
                strength = 'Strong';
            }

            document.getElementById('password-strength-message').innerHTML = 'Password Strength: ' + strength;
        });

        // Ensure confirmPasswordField is defined and add input event listener
        var confirmPasswordField = document.getElementById('confirmPassword');
        confirmPasswordField.addEventListener('input', function () {
            var password = passwordField.value;
            var confirmPassword = confirmPasswordField.value;

            if (password !== confirmPassword) {
                confirmPasswordField.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        });
    });
</script>

</head>
<body>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
    <div class="main">
        <div class="box">
            <img src="images/logosl.png" alt="Logo" width="150px">
            <h1>Create Password</h1>
            <input type="tel" name="t1" id="mobile" placeholder="Enter Mobile Number" required>
            <input type="password" name="t2" id="password" placeholder="New Password" required>
<span id="togglePassword" class="toggle-password" onclick="togglePasswordField()">
    <ion-icon name="eye-outline"></ion-icon>
</span>
<div id="password-strength-message" style="color: aqua; margin-bottom: 10px;"></div>
<input type="password" name="t3" id="confirmPassword" placeholder="Confirm Password" required>

            <input type="submit" name="update" value="Update" id="sub">
            <p>Already Have an Account? <a href="login.php" id="log">Login</a></p>
        </div>
    </div>
</form>

<?php
error_reporting(0);

// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['update'])) {
    $mobile = $_POST['t1'];
    $newPassword = $_POST['t2'];
    $confirmPassword = $_POST['t3'];

    // Check if passwords match
    if ($newPassword !== $confirmPassword) {
        echo '<script>alert("Passwords do not match")</script>';
    } else {
        // Check if mobile exists in the database
        $stmt = $conn->prepare("SELECT * FROM registration WHERE Phone = ?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Update password in the database
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE registration SET Password = ? WHERE Phone = ?");
            $updateStmt->bind_param("ss", $hashedPassword, $mobile);
            $updateStmt->execute();

            echo '<script>alert("Password updated successfully. Please login with your new password.")</script>';
            header('Location: login.php');
            exit;
        } else {
            echo '<script>alert("Mobile number not found. Please enter a valid mobile number.")</script>';
        }
    }

    $stmt->close();
}

$conn->close();
?>

</body>
</html>
