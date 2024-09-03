<?php
session_start();
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

$servername = "localhost";
$username = "root"; // Apna database username
$password = ""; // Apna database password
$dbname = "shiddh"; // Apna database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch offers from database
$sql = "SELECT * FROM Offers WHERE status = 'Active' AND end_date >= NOW() ORDER BY start_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Offers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .offer {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .offer h3 {
            margin: 0;
            font-size: 20px;
        }
        .offer p {
            margin: 5px 0;
        }
        .offer .discount {
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
        }
        @media (max-width: 400px) {
            .logo img {
                max-width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="images\logob21.png" alt="Shiddh Lighting Industries Logo"> <!-- Replace 'logo.png' with the actual logo file name -->
        </div>
        <h2>Current Offers</h2>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<div class='offer'>";
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
                echo "<p><strong>Discount:</strong> " . htmlspecialchars($row['discount_value']) . " " . htmlspecialchars($row['discount_type']) . "</p>";
                echo "<p><strong>Valid From:</strong> " . htmlspecialchars($row['start_date']) . "</p>";
                echo "<p><strong>Valid Until:</strong> " . htmlspecialchars($row['end_date']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No current offers available.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
