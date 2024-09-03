<?php
session_start();
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
// Establish database connection
$con = mysqli_connect("localhost", "root", "", "shiddh");

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Handle the download request
if (isset($_GET['download']) && $_GET['download'] === 'pdf') {
    // Fetch the latest PDF file path from the database
    $query = "SELECT file_path, file_name FROM price_list ORDER BY upload_date DESC LIMIT 1";
    $result = mysqli_query($con, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $filePath = $row['file_path'];
        $fileName = $row['file_name'];
        
        if (file_exists($filePath)) {
            // Set headers to initiate file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo "File does not exist.";
            exit;
        }
    } else {
        echo "No PDF found.";
        exit;
    }
}

// Fetch the latest PDF file path from the database
$query = "SELECT file_path, file_name FROM price_list ORDER BY upload_date DESC LIMIT 1";
$result = mysqli_query($con, $query);

$latestPdf = null;

if ($row = mysqli_fetch_assoc($result)) {
    $filePath = $row['file_path'];
    $fileName = $row['file_name'];

    if (file_exists($filePath)) {
        $latestPdf = array('path' => $filePath, 'name' => $fileName);
    }
}

// Close database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Latest PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .container h1 {
            margin-bottom: 20px;
            color: #007bff;
        }
        .container p {
            margin-bottom: 20px;
            font-size: 1.2em;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($latestPdf): ?>
            <h1>Latest PDF Available</h1>
            <p>Click the button below to download the latest PDF.</p>
            <a href="?download=pdf" class="button">Download PDF</a>
        <?php else: ?>
            <h1>No Price List Available</h1>
            <p>There are no Price List available for download at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
