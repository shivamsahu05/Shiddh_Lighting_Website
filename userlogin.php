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
                <img src="images/logosl.png" alt="this is logo" width="280">
                <h3>User Login</h3>
                <div class="inputBox">
                    <input type="text" required="required" name="t1">
                    <span>USER NAME</span>
                    <i></i>
                </div>
                <div class="inputBox">
                    <input type="password" required="required" name="t2">
                    <span>PASSWORD</span>
                    <i></i>
                </div>
                <div class="links">
                    <a href="forgetE.php">Forget Password / ID Blocked ?</a>
                </div>
                <input type="submit" value="Login" name="Login"> 
                <!-- <input type="button" value="Go back!" onclick="history.back()" id="back"> -->
                
                <br><br><br>
                <a href="registred.php">
                    <input type="button" value="User Login Registration" id="bt1">
                </a>
                <br><br>
            </div> 
        </div>
    </form>
    
    <?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "shiddh");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['Login'])) {
    $username = $_POST['t1'];
    $password = $_POST['t2'];

    // Query to validate admin credentials (static password)
    if ($username == 'admin' && $password == '1234') {
        // Set session variables for admin
        $_SESSION['username'] = $username;

        // Redirect to admin page
        header('location: firstlog.html');
        exit();
    }

    // Query to validate regular user credentials
    $sql = mysqli_query($conn, "SELECT * FROM registration WHERE FullName = '$username' AND Password = '$password'");
    $count = mysqli_num_rows($sql);

    if($count == 1) {
        // Set session variables for regular user
        $_SESSION['username'] = $username;

        // Redirect to the logged-in page
        header('location: trial.php');
        exit();
    } else {
        echo '<script> alert("Invalid username or password")</script>';
    }
}

// Close the database connection
mysqli_close($conn);
?>

</body>
</html>
