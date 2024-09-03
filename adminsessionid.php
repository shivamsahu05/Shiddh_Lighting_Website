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

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get cookies
$cookie_info = [];
foreach ($_COOKIE as $key => $value) {
    $cookie_info[$key] = $value;
}

// Fetch data from login_logs table with role = 'admin' and sort by login_time in descending order
$sql = "SELECT username, ip_address, login_time, role FROM login_logs WHERE role = 'admin' ORDER BY login_time DESC";
$result = $conn->query($sql);

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
    </style>
</head>
<body>

<header>
    <h1>Admin Dashboard</h1>
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
                <?php foreach ($cookie_info as $name => $value): ?>
                <tr>
                    <td><?php echo htmlspecialchars($name); ?></td>
                    <td><?php echo htmlspecialchars($value); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Login Logs (Role: Admin)</div>
        <div class="card-body">
            <table>
                <tr>
                    <th>Username</th>
                    <th>IP Address</th>
                    <th>Login Time</th>
                  
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['login_time']); ?></td>
                       
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
// Close the database connection
$conn->close();
?>

</body>
</html>
