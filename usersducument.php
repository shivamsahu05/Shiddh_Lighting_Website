<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged In User</title>
    <link rel="stylesheet" href="">
    <!-- Add your CSS or external links -->
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            box-sizing: border-box;
        }
        i {
            color: rgb(255, 0, 0);
            font-size: 18px;
            cursor: pointer;
            float: right; /* Align to the right */
            margin-right: 20px; /* Add some margin for spacing */
        }
        p {
            color: black;
            background: linear-gradient(#fff, rgb(0, 225, 255));
            width: 100%;
            font-family: 'Times New Roman', Times, serif;
            height: 40px;
            text-align: center;
            padding: 10px;
            font-size: 25px;
        }
        .flex-container {
            width: 100%;
            height: auto; 
            background-color: aqua;
            display: flex;
        } 
        .box-container {
            width: 100%;
            height: auto; 
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
        } 
        .flex-box {
            width: 200px;
            height: 200px;
            background: linear-gradient(purple, red);
            color: white;
            font-size: 25px;
            text-align: center;
            line-height: 200px;
            border-radius: 20px;
            margin: 10px;
        }
        .flex-box a {
            color: #fff;
            font-size: 20px;
            text-decoration: none;
            text-transform: capitalize;
            letter-spacing: 1px;
        }
    </style>
</head>
<body bgcolor="black">
    <img src="images/logosl.png" alt="logo" width="250px">
   
    <p>
        <!-- Display username or any other user information -->
        <?php
        session_start();

        // Check if the user is logged in
        if (isset($_SESSION['username'])) {
            // Database connection
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
                echo "<span style='float: right; margin-right: 20px;'>Welcome: " . $fullName . "</span>";
            } else {
                echo "User not found in the database.";
            }

            // Close the database connection
            mysqli_close($conn);
        } else {
            echo "User not logged in.";
        }
        ?>
        <b><u>!! My Documents !!</u></b>
        <i><a href="logout.php">Log-Out </a></i> <!-- Make sure to create logout.php for logging out -->
    </p>
    
    <div class="flex-container">
        <div class="box-container">
            <div class="flex-box">
            <!-- <a href="trial.php">Take Order</a> -->
                <a href="trial.php">All Invoices</a>   
            </div>
            <div class="flex-box">
                <a href="#">My Fess</a>
            </div>
            
            <div class="flex-box">
            <!-- <a href="trial.php">Fees Detail</a> -->
                <a href="mydetail.php">Price List Download</a>
            </div>
        </div>
    </div>
</body>
</html>
