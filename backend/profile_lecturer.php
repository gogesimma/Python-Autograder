<?php
session_start();
include 'db.php';  // Include your database connection script

// Initialize lecturer details
$name = '';
$surname = '';
$username = '';
$email = '';

// Check if the user is logged in and has a valid session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Prepare and execute the query to get lecturer details
    $stmt = $conn->prepare("
        SELECT l.name, l.surname, u.username, l.email 
        FROM lecturers AS l 
        JOIN users AS u ON l.user_id = u.id 
        WHERE l.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $surname, $username, $email);
    $stmt->fetch();
    $stmt->close();

    // Pass the data to the front-end HTML
    $data = [
        'name' => $name,
        'surname' => $surname,
        'username' => $username,
        'email' => $email,
    ];
    file_put_contents('profile_lecturer_data.json', json_encode($data)); // Save data to a JSON file
    header('Location: /../frontend/profile_lecturer.html'); // Redirect to the front-end profile page
    exit();
} else {
    // Redirect to login page if user is not authenticated
    header('Location: authenticate.php');
    exit();
}
