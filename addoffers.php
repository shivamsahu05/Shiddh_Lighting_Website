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
// Handle form submission for adding offers
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        // Handle offer deletion
        $offer_id = $_POST['offer_id'];
        $delete_stmt = $conn->prepare("DELETE FROM Offers WHERE offer_id = ?");
        $delete_stmt->bind_param("i", $offer_id);
        if ($delete_stmt->execute()) {
            $message = "Offer deleted successfully!";
        } else {
            $message = "Error deleting offer: " . $delete_stmt->error;
        }
        $delete_stmt->close();
    } else {
        // Add new offer
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $discount_type = htmlspecialchars($_POST['discount_type']);
        $discount_value = htmlspecialchars($_POST['discount_value']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $status = htmlspecialchars($_POST['status']);

        $stmt = $conn->prepare("INSERT INTO Offers (title, description, discount_type, discount_value, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $description, $discount_type, $discount_value, $start_date, $end_date, $status);

        if ($stmt->execute()) {
            $message = "New offer added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
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
    <title>Add Offer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 800px;
            box-sizing: border-box;
            margin: 10px; /* Adds space around the container */
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
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Ensure padding is included in width */
        }
        .add_button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: blue;
        }
        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            color: #333;
        }
        .offer {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .delete_button{
            background-color: red;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
                box-shadow: none;
                margin: 10px;
            }
            input[type="submit"] {
                font-size: 14px;
            }
        }
        @media (max-width: 400px) {
            .container {
                padding: 10px;
            }
            .logo img {
                max-width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="images/logoinv2.jpg" alt="Shiddh Lighting Industries Logo"> <!-- Ensure correct path and file name -->
        </div>
        <h2>Add New Offer</h2>
        <?php if (isset($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>
        <form action="" method="POST">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="discount_type">Discount Type</label>
            <select id="discount_type" name="discount_type" required>
                <option value="Percentage">Percentage</option>
                <option value="Fixed">Fixed Amount</option>
            </select>

            <label for="discount_value">Discount Value</label>
            <input type="number" id="discount_value" name="discount_value" step="0.01" min="0" max="3000" required>

            <label for="start_date">Start Date</label>
            <input type="datetime-local" id="start_date" name="start_date" required>

            <label for="end_date">End Date</label>
            <input type="datetime-local" id="end_date" name="end_date" required>

            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <input type="submit" class='add_button' value="Add Offer">
        </form>
       
        <h2><b>!! <u>Current Offers</u> !!</b></h2>
        <?php
        if ($result->num_rows > 0) {
            $serial = 1; // Initialize serial number
            while ($row = $result->fetch_assoc()) {
                echo "<div class='offer'>";
                echo "<td>$serial</td>"; // Serial Number
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
                echo "<p><strong>Discount:</strong> " . htmlspecialchars($row['discount_value']) . " " . htmlspecialchars($row['discount_type']) . "</p>";
                echo "<p><strong>Valid From:</strong> " . htmlspecialchars($row['start_date']) . "</p>";
                echo "<p><strong>Valid Until:</strong> " . htmlspecialchars($row['end_date']) . "</p>";
                echo "<form action='' method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='offer_id' value='" . htmlspecialchars($row['offer_id']) . "'>";
                echo "<input type='hidden' name='action' value='delete'>";
                echo "<input type='submit'  class='delete_button' value='Delete Offer' onclick='return confirm(\"Are you sure you want to delete this offer?\");'>";
                echo "</form>";
                echo "</div>";
                $serial++; // Increment serial number
            }
        } else {
            echo "<p>No current offers available.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
