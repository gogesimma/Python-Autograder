<?php
session_start();
require 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($_POST['register'])) {
        // User Registration
        $stmt = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param('ss', $username, $hashed_password);
        if ($stmt->execute()) {
            echo "User registered successfully!";
            // Redirect after registration
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['login'])) {
        // User Login
        $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "Login successful!";
            // Redirect after login
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid username or password!";
        }
        $stmt->close();
    }

}