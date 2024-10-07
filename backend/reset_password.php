<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_or_username = $_POST['email_or_username'];

    // Check if the email or username exists in the users table
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
    $stmt->bind_param('ss', $email_or_username, $email_or_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Logic to send reset password email goes here (generate token, store it in DB, send email with link)

        echo "An email with password reset instructions has been sent.";
    } else {
        echo "No account found with that student number or email.";
    }

    $stmt->close();
}

$conn->close();
