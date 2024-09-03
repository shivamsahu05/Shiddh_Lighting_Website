<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages by Contacting</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        img {
            width: 200px;
            margin: 20px 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            overflow-x: auto;
        }
        .table-wrapper {
            max-height: 600px; /* Adjust as needed */
            overflow-y: auto; /* Vertical scroll for table body */
            border: 1px solid #555;
            border-radius: 8px;
            background-color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: transparent; /* Transparent to show container background */
        }
        th, td {
            border: 1px solid #555;
            padding: 12px;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background-color: yellow;
            color: black;
            font-weight: bold;
            position: -webkit-sticky; /* For Safari */
            position: sticky;
            top: 0;
            z-index: 2; /* Ensure it sits above other content */
        }
        tr:nth-child(even) {
            background-color: #444;
        }
        tr:nth-child(odd) {
            background-color: #333;
        }
        tr:hover {
            background-color: #555;
        }
        select, button {
            margin: 5px;
            padding: 8px;
            border: 1px solid #777;
            border-radius: 4px;
            background-color: #444;
            color: white;
        }
        button {
            background-color: yellow;
            color: black;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #f1c40f;
        }
        @media (max-width: 768px) {
            th, td {
                padding: 8px;
                font-size: 14px;
            }
            select, button {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>

<img src="images/logosl.png" alt="Logo">

<div class="container">
    <div class="table-wrapper">
        <?php
        error_reporting(E_ALL);  // Enable error reporting for debugging

        session_start();
        // Check if the user is not logged in, redirect to login page
        if (!isset($_SESSION['username'])) {
            header('Location: login.php'); // Redirect to login page if not logged in
            exit;
        }

        // Handle delete request
        if (isset($_GET['delete'])) {
            $phone = $_GET['delete'];
            // Establish connection to MySQL database
            $con = mysqli_connect("localhost", "root", "", "shiddh");
            // Check connection
            if (!$con) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Prepare and execute the DELETE query
            $phone = mysqli_real_escape_string($con, $phone);
            $query = "DELETE FROM feedbackmess WHERE Phone = '$phone'";
            if (mysqli_query($con, $query)) {
                echo "<script>alert('Record deleted successfully');</script>";
            } else {
                echo "<script>alert('Error deleting record: " . mysqli_error($con) . "');</script>";
            }
            mysqli_close($con);
        }

        // Establishing connection to MySQL database
        $con = mysqli_connect("localhost", "root", "", "shiddh");
        // Checking connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }

        // Fetching data sorted by sent_date in descending order
        $query = "SELECT * FROM feedbackmess ORDER BY sent_date DESC";
        $result = mysqli_query($con, $query);

        // Check if query executed successfully
        if (!$result) {
            echo "Error fetching data: " . mysqli_error($con);
            exit();
        }

        // Checking if there are any results
        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>#</th>"; // Serial number header
            echo "<th>F_Name</th>";
            echo "<th>L_Name</th>";
            echo "<th>Email</th>";
            echo "<th>Number</th>";
            echo "<th>Message</th>";
            echo "<th>Date</th>";
            echo "<th>Action</th>";
            echo "</tr>";

            $serialNumber = 1; // Initialize serial number

            // Output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $serialNumber++ . "</td>"; // Display serial number
                echo "<td>" . htmlspecialchars($row['FName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['LName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['EMail']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Message']) . "</td>";

                // Format Date and Time from yyyy-mm-dd hh:mm:ss to dd-mm-yyyy hh:mm:ss
                $formattedDateTime = date("d-m-Y H:i:s", strtotime($row['sent_date']));
                echo "<td>" . htmlspecialchars($formattedDateTime) . "</td>";

                // Adding checkboxes and buttons with JavaScript actions
                echo "<td>";
                echo "<select id='cards' class='form-control' name='cardlist'>
                    <option name='card' value='New' style='font-weight: bold; color: Red;'>New</option>
                    <option name='card' value='Pending' style='font-weight: bold; color: blue;'>Pending</option>
                    <option name='card' value='Done' style='font-weight: bold; color: green;'>Done</option>
                </select>";
                echo "<input type='checkbox' name='R' id='R" . htmlspecialchars($row['Phone']) . "' required='required'>";
                echo "</td>";
                echo "<td>";
                echo "<button onclick='updateAction(\"" . htmlspecialchars($row['Phone']) . "\")'>Update</button>";
                echo "<button onclick='confirmDelete(\"" . htmlspecialchars($row['Phone']) . "\")'>Delete</button>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No records found</p>";
        }

        // Closing connection
        mysqli_close($con);
        ?>
    </div>
</div>

<!-- JavaScript functions -->
<script>
    // Function for update action
    function updateAction(phone) {
        // Check the checkbox
        document.getElementById('R' + phone).checked = true;
        
        // Replace with your update logic, e.g., redirect to an update page
        alert("Update clicked for phone number: " + phone);
        // Example: window.location.href = 'update.php?phone=' + encodeURIComponent(phone);
    }

    // Function to confirm and execute delete action
    function confirmDelete(phone) {
        if (confirm("Are you sure you want to delete this record?")) {
            // Redirect to the same page with a delete query parameter
            window.location.href = window.location.pathname + '?delete=' + encodeURIComponent(phone);
        }
    }
</script>

</body>
</html>
