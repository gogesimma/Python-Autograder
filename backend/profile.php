<?php 
session_start();
include 'db.php';  // Include your database connection script

// Check if the user is logged in and has a valid session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Prepare and execute the query to get user details
    $stmt = $conn->prepare("
        SELECT s.name, s.surname, u.username, s.email 
        FROM students AS s
        JOIN users AS u ON s.user_id = u.id 
        WHERE s.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $surname, $username, $email);
    $stmt->fetch();
    $stmt->close();

    // Close the database connection
    $conn->close();
} else {
    // Redirect to login page if user is not authenticated
    header('Location: authenticate.php');
    exit();
}



