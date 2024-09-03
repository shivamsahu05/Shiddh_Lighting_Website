<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged In User</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            box-sizing: border-box;
        }
        body {
            background-color: #f0f0f0;
            color: #333;
        }
        .header {
            background-color: black;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            position: relative;
        }
        .logo {
            width: 150px; /* Increased logo size */
            margin-left: 20px; /* Added margin for spacing */
            margin-right: auto; /* Pushes the logo to the left */
        }
        .logout {
            margin-left: auto; /* Pushes the logout button to the right */
            margin-right: 20px; /* Added margin for spacing */
        }
        .logout-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #cc0000;
        }
        .service {
            text-align: center;
            font-size: 28px;
            margin: 20px 0;
        }
        .flex-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        .box-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .flex-box {
            width: 200px;
            height: 200px;
            background: linear-gradient(to right bottom, #ff5252, #ff4081);
            color: white;
            font-size: 24px;
            text-align: center;
            line-height: 200px;
            border-radius: 20px;
            margin: 10px;
            transition: transform 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .flex-box:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .flex-box a {
            color: #fff;
            text-decoration: none;
            text-transform: capitalize;
            letter-spacing: 1px;
        }
    </style>

    <!-- JavaScript to prevent back button after logout -->
    <script>
        if (window.history && window.history.pushState) {
            window.history.pushState('forward', null, './#forward');
            window.onpopstate = function () {
                window.location.href = 'login.php'; // Redirect to login page
            };
        }
    </script>
</head>
<body>
    <div class="header">
        <img src="images/logosl.png" alt="logo" class="logo">
        <?php
        session_start();

        // Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
        // Prevent caching
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

        // Check if the user is not logged in, redirect to login page
        if (!isset($_SESSION['username'])) {
            header('Location: login.php');
            exit;
        }

        // Check if the user is logged in
        if (isset($_SESSION['username'])) {
            // Database connection (replace with your database credentials)
            $conn = mysqli_connect("localhost", "root", "", "shiddh");

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Fetch the logged-in user's username from the session
            $username = $_SESSION['username'];

            // Query to select the full name of the logged-in user
            $sql = mysqli_query($conn, "SELECT FullName FROM registration WHERE FullName = '$username'");
            if (mysqli_num_rows($sql) > 0) {
                // Fetch the full name and print it
                $row = mysqli_fetch_assoc($sql);
                $fullName = $row['FullName'];
                echo "<span>Welcome, " . $fullName . "!</span>";
            } else {
                echo "User not found in the database.";
            }

            // Close the database connection
            mysqli_close($conn);
        }
        ?>
        <div class="logout">
            <a class="logout-btn" href="logout.php">Log-Out</a>
        </div>
    </div>

    <div class="service">
     <b>!! <u>My Service</u> !!</b> 
    </div>

    <div class="flex-container">
        <div class="box-container">
            <div class="flex-box">
                <a href="takecorder.php">Take Order</a>   
            </div>
            <div class="flex-box">
                <a href="usercart.php">My Cart</a>   
            </div>
            <div class="flex-box">
                <a href="mydetail.php">Fees Detail</a>
            </div>       
            <div class="flex-box">
                <a href="userallinv.php">Document</a>
            </div>
            <div class="flex-box">
                <a href="userpricelist.php">Price's List</a>   
            </div>
            <div class="flex-box">
                <a href="usershowoffers.php">Offer</a>
            </div>
        </div>
    </div>
</body>
</html>
