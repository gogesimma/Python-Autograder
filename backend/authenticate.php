
<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['register'])) {
        // Registration logic
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = $_POST['role'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];

        // Check if passwords match
        if ($password !== $confirm_password) {
            echo "Passwords do not match!";
            exit();
        }

        // Validate student number for students (9 digits)
        if ($role === 'student' && (!is_numeric($username) || strlen($username) != 9)) {
            echo "Invalid student number! Please enter a valid 9-digit number.";
            exit();
        }

        // Check if the username (student number or email) already exists
        $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Username already in use! Please choose another.";
        } else {
            // Insert into users table
            $stmt = $conn->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $username, $password, $role);
            $stmt->execute();

            // Get the user ID of the newly inserted user
            $user_id = $stmt->insert_id;

            // Insert into students or lecturers table
            if ($role === 'student') {
                $stmt = $conn->prepare('INSERT INTO students (user_id, name, surname, email) VALUES (?, ?, ?, ?)');
            } else {
                $stmt = $conn->prepare('INSERT INTO lecturers (user_id, name, surname, email) VALUES (?, ?, ?, ?)');
            }
            $stmt->bind_param('isss', $user_id, $name, $surname, $email);
            $stmt->execute();
            $stmt->close();

            echo "User registered successfully!";
            header("Location: ../frontend/login.html");
            exit();
        }
    } elseif (isset($_POST['login'])) {
        // Login logic
        $username = $_POST['username'];
        $password = $_POST['password'];        

        // Retrieve user from the database
        $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Check if password matches
            if ($password === $user['password']) {
                // Store user info in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on user role
                if ($user['role'] == 'student') {
                    header("Location: ../frontend/student.html");
                } else {
                    header("Location: ../frontend/lectureto.html");
                }
                exit();
            } else {
                echo "Invalid password!";
            }
        } else {
            echo "User not found!";
        }
        $stmt->close();
    }
}











        



