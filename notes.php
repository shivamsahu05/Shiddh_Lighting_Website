<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f0f0f0; /* Light gray background */
            padding-top: 20px;
        }
        .card {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff; /* White background for the card */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }
        .note-item {
            margin-bottom: 10px;
        }
        .notes-list {
            list-style-type: none;
            padding: 0;
        }
        .notes-list li {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .notes-list li strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Add a Note</h5>
                            <div class="form-group note-item">
                                <textarea class="form-control" placeholder="Write your message" name="message" rows="3" required></textarea>
                                <input type="hidden" class="form-control" placeholder="Id_Num" name="id_num" value="">
                            </div>
                            <div class="form-group note-item">
                                <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group note-item">
                                <input type="text" class="form-control" placeholder="Mobile number" name="number">
                            </div>
                            <button type="submit" class="btn btn-primary" name="save_btn">Save Note</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Saved Notes</h5>
                        <ul class="notes-list">
                            <?php
                            session_start();
                            // Check if the user is not logged in, redirect to login page
                            if (!isset($_SESSION['username'])) {
                                header('Location: login.php'); // Redirect to login page if not logged in
                                exit;
                            }
                            $conn = mysqli_connect("localhost", "root", "", "shiddh");

                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            // Insert new note into database
                            if (isset($_POST['save_btn'])) {
                                $message = $_POST['message'];
                                $id = $_POST['id_num'];
                                $date = $_POST['date'];
                                $number = $_POST['number'];

                                $insert_query = "INSERT INTO notesmessage (id, notice, date, MobileNumber) VALUES ('$id', '$message', '$date', '$number')";

                                if (mysqli_query($conn, $insert_query)) {
                                    echo '<li>Note added successfully</li>';
                                } else {
                                    echo '<li>Error adding note: ' . mysqli_error($conn) . '</li>';
                                }
                            }

                            // Update note in database
                            if (isset($_POST['update_btn'])) {
                                $id = $_POST['update_id'];
                                $message = $_POST['message'];
                                $date = $_POST['date'];
                                $number = $_POST['number'];

                                $update_query = "UPDATE notesmessage SET notice='$message', date='$date', MobileNumber='$number' WHERE id=$id";

                                if (mysqli_query($conn, $update_query)) {
                                    echo '<li>Note updated successfully</li>';
                                } else {
                                    echo '<li>Error updating note: ' . mysqli_error($conn) . '</li>';
                                }
                            }

                            // Delete note from database
                            if (isset($_POST['delete_btn'])) {
                                if (isset($_POST['id'])) {
                                    $note_id = $_POST['id'];
                                    $delete_query = "DELETE FROM notesmessage WHERE id = $note_id";

                                    if (mysqli_query($conn, $delete_query)) {
                                        echo '<li>Note deleted successfully</li>';
                                    } else {
                                        echo '<li>Error deleting note: ' . mysqli_error($conn) . '</li>';
                                    }
                                } else {
                                    echo '<li>Error deleting note: Note ID not set</li>';
                                }
                            }

                            // Display existing notes
                            $query = "SELECT * FROM notesmessage";
                            $result = mysqli_query($conn, $query);

                            if(mysqli_num_rows($result) > 0) {
                                $count = 1; // Initialize counter for incrementing number

                                while($row = mysqli_fetch_assoc($result)) {
                                    echo '<li>';
                                    echo '<strong>Number:</strong> ' . $count . '<br>'; // Incrementing number
                                    echo '<strong>Message:</strong> ' . htmlspecialchars($row['notice']) . '<br>';
                                    echo '<strong>Date:</strong> ' . date('d-m-Y', strtotime($row['date'])) . '<br>';
                                    echo '<strong>Mobile Number:</strong> ' . htmlspecialchars($row['MobileNumber']) . '<br>';
                                    
                                    // Update button with form pre-filled for editing
                                    echo '<form method="post" action="trial.php" style="display: inline;">';
                                    echo '<input type="hidden" name="update_id" value="' . $row['id'] . '">';
                                    echo '<button type="submit" class="btn btn-warning btn-sm" name="edit_btn">Edit</button>';
                                    echo '</form>';

                                    // Delete button with confirmation
                                    echo '<form method="post" style="display: inline; margin-left: 5px;" onsubmit="return confirmDelete()">';
                                    echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                                    echo '<button type="submit" class="btn btn-danger btn-sm" name="delete_btn">Delete</button>';
                                    echo '</form>';
                                    
                                    echo '</li>';

                                    $count++; // Increment counter for next iteration
                                }
                            } else {
                                echo '<li>No notes found</li>';
                            }

                            mysqli_close($conn);
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and jQuery (needed for some Bootstrap components) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // JavaScript function to confirm delete action
        function confirmDelete() {
            return confirm("Are you sure you want to delete this note?");
        }

    </script>
</body>
</html>
