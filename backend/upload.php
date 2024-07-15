<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Define the upload directory and file path
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $uploadFile = $uploadDir . basename($file['name']);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        echo 'File uploaded successfully: ' . $uploadFile;
    } else {
        echo 'Error uploading file';
    }
} else {
    echo 'No file uploaded';
}
?>
