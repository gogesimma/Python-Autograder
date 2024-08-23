<?php
// upload.php
include 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $user_id = $_SESSION['user_id'];
    $upload_dir = 'uploads/';
    $uploaded_file = $upload_dir . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file)) {
        $stmt = $mysqli->prepare("INSERT INTO submissions (user_id, file_path) VALUES (?, ?)");
        $stmt->bind_param('is', $user_id, $uploaded_file);
        $stmt->execute();
        echo "File uploaded successfully!";
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}



