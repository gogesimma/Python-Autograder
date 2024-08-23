<?php
// Database connection parameters
$host = '127.0.0.1';
$dbname = 'Autograder';
$username = 'root';
$password = 'simakahle@10#';

// Create a MySQLi connection
$conn =  mysqli_connect($host, $username, $password, $dbname);

// Check the connection
if (!$conn) {
    die("Connection failed: " . $mysqli->connect_error);
} else {
    echo "Connected successfully to the database!";
}






