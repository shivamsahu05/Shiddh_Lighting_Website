<?php
// Start the session to access cookies
session_start();
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "shiddh"; // Replace with your database name

// Initialize variables
$cookie_info = [];
$result = null;
$conn = null;
$verification_result = "";

// Create connection
try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get cookies
    foreach ($_COOKIE as $key => $value) {
        $cookie_info[$key] = $value;
    }

    // Fetch data from login_logs table with role = 'user' and sort by login_time in descending order
    $sql = "SELECT username, Password, ip_address, login_time, role FROM login_logs WHERE role = 'user' ORDER BY login_time DESC";
    $result = $conn->query($sql);
    if ($result === false) {
        throw new Exception("Query failed: " . $conn->error);
    }

    // Password verification
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form inputs
        $plain_password = $_POST['plain_password'];
        $hashed_password = $_POST['hashed_password'];

        // Verify if the plain password matches the hashed password
        if (password_verify($plain_password, $hashed_password)) {
            $verification_result = "Password is correct.";
        } else {
            $verification_result = "Password is incorrect.";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        header {
            background-color: #007bff;
            color: #fff;
            padding: 15px 0;
            text-align: center;
        }
        h1 {
            margin: 0;
            font-size: 2em;
        }
        h2 {
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .card-header {
            font-size: 1.5em;
            margin-bottom: 10px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .card-body {
            padding: 10px;
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .show-password-container {
            margin-bottom: 10px;
        }
        .show-password-container input[type="checkbox"] {
            margin-right: 10px;
        }
        .result {
            margin-top: 20px;
            font-size: 1.2em;
            color: #007bff;
        }

        /* Media Queries */
        @media (max-width: 1200px) {
            .container {
                padding: 15px;
            }
            .card {
                margin-bottom: 15px;
                padding: 15px;
            }
            .card-header {
                font-size: 1.4em;
            }
        }

        @media (max-width: 992px) {
            .container {
                padding: 10px;
            }
            .card {
                margin-bottom: 10px;
                padding: 10px;
            }
            .card-header {
                font-size: 1.2em;
            }
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.5em;
            }
            table {
                font-size: 14px;
            }
            th, td {
                padding: 8px;
            }
            .card-header {
                font-size: 1.1em;
            }
        }

        @media (max-width: 576px) {
            h1 {
                font-size: 1.2em;
            }
            table {
                font-size: 12px;
            }
            th, td {
                padding: 6px;
            }
            .card-header {
                font-size: 1em;
            }
        }
    </style>
    <script>
        // Auto-refresh the page every 2 minutes
        setTimeout(function(){
            location.reload();
        }, 120000); // 120000 milliseconds = 2 minutes

        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('plain_password');
            var checkbox = document.getElementById('show-password');
            if (checkbox.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</head>
<body>

<header>
    <h1>User's Track</h1>
</header>

<div class="container">
    <div class="card">
        <div class="card-header">Cookies</div>
        <div class="card-body">
            <table>
                <tr>
                    <th>Cookie Name</th>
                    <th>Cookie Value</th>
                </tr>
                <?php if (!empty($cookie_info)): ?>
                    <?php foreach ($cookie_info as $name => $value): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($name); ?></td>
                        <td><?php echo htmlspecialchars($value); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="2">No cookies found</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Password Verification</div>
        <div class="card-body">
            <form action="" method="POST">
            <input type="password" id="plain_password" name="plain_password" placeholder="Enter Plain Password" required>
                <div class="show-password-container">
                    <input type="checkbox" id="show-password" onclick="togglePasswordVisibility()">
                    <label for="show-password">Show Password</label>
                </div>
               
                <input type="text" name="hashed_password" placeholder="Enter Hashed Password" required>
                <input type="submit" value="Verify Password">
            </form>
            <div class="result">
                <?php echo htmlspecialchars($verification_result); ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Login Logs (Role: User's)</div>
        <div class="card-body">
            <table>
                <tr>
                    <th>Username</th>
                    <th>Login Time</th>
                    <th>IP Address</th>
                    <th>Password</th>
                </tr>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['login_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['Password']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4">No records found</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php
// Close the database connection if it was created
if ($conn) {
    $conn->close();
}
?>

</body>
</html>
