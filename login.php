<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "shiddh");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to get public IP
function getUserIP() {
    return file_get_contents('https://api.ipify.org'); // For IPv4
}

// Generate and store CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if (isset($_POST['Login'])) {
    // Check if CSRF token is set in POST request
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $ip_address = getUserIP();
        $login_time = date('Y-m-d H:i:s');

        // Static admin credentials
        $static_admin_username = 'admin';
        $static_admin_password = '1234';

        if ($username === $static_admin_username && $password === $static_admin_password) {
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin';

            setcookie('admin_logged_in', 'true', time() + 3600, '/', '', true, true);
            setcookie('username', $username, time() + 3600, '/', '', true, true);

            $sql = "INSERT INTO login_logs (username, ip_address, login_time, role) VALUES (?, ?, ?, 'admin')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $ip_address, $login_time);
            $stmt->execute();

            header('Location: firstlog.html');
            exit();
        }

        $sql = "SELECT * FROM registration WHERE FullName = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['Password'])) {
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';

            setcookie('user_logged_in', 'true', time() + 3600, '/', '', true, true);
            setcookie('username', $username, time() + 3600, '/', '', true, true);

            $sql = "INSERT INTO login_logs (username, Password, ip_address, login_time, role) VALUES (?, ?, ?, ?, 'user')";
            $stmt = $conn->prepare($sql);
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bind_param("ssss", $username, $hashed_password, $ip_address, $login_time);
            $stmt->execute();

            header('Location: firstuserlog.php');
            exit();
        } else {
            $error_message = "Invalid username or password";
        }
    } else {
        $error_message = "CSRF token validation failed";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animated Login Page</title>
    <link rel="stylesheet" href="styles3.css">
</head>
<body>
    <form name="a" action="" method="POST">
        <div class="box">
            <div class="from">
                <img src="images/logosl.png" alt="Logo" width="280">
                <h3>User Login</h3>
                <?php if (isset($error_message)) { ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <?php } ?>
                <div class="inputBox">
                    <input type="text" required="required" name="username">
                    <span>USER NAME</span>
                    <i></i>
                </div>
                <div class="inputBox">
                    <input type="password" id="password" required="required" name="password">
                    <span>PASSWORD</span>
                    <i></i>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" id="show-password" onclick="togglePasswordVisibility()">
                    <label for="show-password">Show Password</label>
                </div>
                <div class="links">
                    <a href="forgetE.php">Forget Password / ID Blocked?</a>
                </div>
                <!-- Add CSRF token as a hidden input -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="submit" value="Login" name="Login">
                
                <br><br><br>
                <a href="registerd.php">
                    <input type="button" value="User Login Registration" id="bt1">
                </a>
                <br><br>
            </div> 
        </div>
    </form>
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('password');
            var checkbox = document.getElementById('show-password');
            if (checkbox.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</body>
</html>
