<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload and Download Price List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f0f0f0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 1.8em;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="file"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .table-container {
            max-height: 400px; /* Adjust height as needed */
            overflow-y: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
            position: -webkit-sticky; /* For Safari */
            position: sticky;
            top: 0;
            z-index: 2; /* Ensure it sits above other content */
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:nth-child(odd) {
            background-color: #fff;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload and Download Price List</h1>

        <!-- Form for Uploading PDF -->
        <form action="" method="post" enctype="multipart/form-data">
            <label for="pdf">Upload Price List (PDF):</label>
            <input type="file" id="pdf" name="pdf" accept=".pdf" required>
            <input type="submit" name="upload" value="Upload PDF">
        </form>

        <!-- Form for Downloading PDF -->
        <form action="" method="post">
            <input type="submit" name="download" value="Download Latest Price List">
        </form>

        <!-- Table to Display Uploaded PDFs -->
        <h2>Uploaded Price Lists</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Serial No</th>
                        <th>File Name</th>
                        <th>Upload Date</th>
                        <th>Download</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
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
                        echo "<tr><td colspan='5'>Failed to connect to MySQL: " . mysqli_connect_error() . "</td></tr>";
                        exit();
                    }

                    // Handle PDF upload
                    if (isset($_POST['upload']) && isset($_FILES['pdf'])) {
                        $file = $_FILES['pdf'];
                        $fileName = basename($file['name']);
                        $fileTmpName = $file['tmp_name'];
                        $fileSize = $file['size'];
                        $fileError = $file['error'];
                        $fileType = $file['type'];

                        if ($fileError === UPLOAD_ERR_OK) {
                            if ($fileType === 'application/pdf') {
                                $uploadDir = 'uploads/';
                                $filePath = $uploadDir . $fileName;

                                // Check if directory exists and is writable
                                if (!is_dir($uploadDir)) {
                                    echo "<p>Upload directory does not exist.</p>";
                                } elseif (!is_writable($uploadDir)) {
                                    echo "<p>Upload directory is not writable.</p>";
                                } else {
                                    // Move the uploaded file to the server
                                    if (move_uploaded_file($fileTmpName, $filePath)) {
                                        $query = "INSERT INTO price_list (file_name, file_path) VALUES ('$fileName', '$filePath')";
                                        if (mysqli_query($con, $query)) {
                                            echo "<p>PDF uploaded successfully.</p>";
                                        } else {
                                            echo "<p>Error: " . mysqli_error($con) . "</p>";
                                        }
                                    } else {
                                        echo "<p>Failed to move uploaded file. Check directory permissions.</p>";
                                    }
                                }
                            } else {
                                echo "<p>Invalid file type. Only PDFs are allowed.</p>";
                            }
                        } else {
                            echo "<p>File upload error. Error code: $fileError</p>";
                        }
                    }

                    // Handle PDF download
                    if (isset($_POST['download'])) {
                        $query = "SELECT file_path FROM price_list ORDER BY upload_date DESC LIMIT 1";
                        $result = mysqli_query($con, $query);

                        if ($row = mysqli_fetch_assoc($result)) {
                            $filePath = $row['file_path'];
                            if (file_exists($filePath)) {
                                header('Content-Type: application/pdf');
                                header('Content-Disposition: attachment;filename="' . basename($filePath) . '"');
                                readfile($filePath);
                                exit();
                            } else {
                                echo "<p>File does not exist.</p>";
                            }
                        } else {
                            echo "<p>No PDF available for download.</p>";
                        }
                    }

                    // Handle PDF delete
                    if (isset($_POST['delete'])) {
                        $serial = $_POST['delete']; // Get the serial number of the file to delete
                        // Fetch the file record by serial number
                        $query = "SELECT id, file_name, file_path FROM price_list WHERE id = (SELECT id FROM price_list ORDER BY upload_date DESC LIMIT 1 OFFSET $serial)";
                        $result = mysqli_query($con, $query);

                        if ($row = mysqli_fetch_assoc($result)) {
                            $filePath = $row['file_path'];
                            // Delete the file from the server
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                            // Delete the record from the database
                            $query = "DELETE FROM price_list WHERE id = " . $row['id'];
                            if (mysqli_query($con, $query)) {
                                echo "<p>File deleted successfully.</p>";
                            } else {
                                echo "<p>Error: " . mysqli_error($con) . "</p>";
                            }
                        } else {
                            echo "<p>File does not exist.</p>";
                        }
                    }

                    // Display Uploaded PDFs
                    $query = "SELECT * FROM price_list ORDER BY upload_date DESC";
                    $result = mysqli_query($con, $query);
                    $serialNumber = 1; // Initialize serial number

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $serialNumber++ . "</td>"; // Display serial number
                        echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['upload_date']) . "</td>";
                        echo "<td><a href='" . htmlspecialchars($row['file_path']) . "' target='_blank'>Download</a></td>";
                        echo "<td><button onclick=\"confirmDelete('" . htmlspecialchars($serialNumber - 1) . "', '" . htmlspecialchars($row['file_name']) . "')\">Delete</button></td>";
                        echo "</tr>";
                    }

                    mysqli_close($con);
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript for Confirm Delete -->
    <script>
        function confirmDelete(serialNumber, fileName) {
            if (confirm("Are you sure you want to delete the file: " + fileName + "?")) {
                var form = document.createElement('form');
                form.method = 'post';
                form.action = '';

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete';
                input.value = serialNumber;
                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
