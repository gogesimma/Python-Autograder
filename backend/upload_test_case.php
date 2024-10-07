<?php
session_start();
include 'db.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['role']) && $_SESSION['role'] == 'lecturer') {
    if (isset($_FILES['testcase_file']) && $_FILES['testcase_file']['error'] == UPLOAD_ERR_OK) {
        $user_id = $_SESSION['user_id'];
        $input_filename = $_FILES['testcase_file']['name'];
        $file_content = file_get_contents($_FILES['testcase_file']['tmp_name']);
        
        
        $test_cases = explode('---', $file_content);

        foreach ($test_cases as $test_case) {
            $lines = array_map('trim', explode("\n", trim($test_case)));

            if (count($lines) >= 4) {
                $identifier = $lines[0];   
                $testcase_input = $lines[1];  
                $expected_output = $lines[2]; 
                $marks = $lines[3];  

                $stmt = $conn->prepare("INSERT INTO test_cases (user_id, identifier, testcase_input, expected_output, marks,input_filename ) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    error_log("Prepare statement failed: " . $conn->error);
                    exit("Database error.");
                }
                $stmt->bind_param("isssss", $user_id, $identifier, $testcase_input, $expected_output, $marks, $input_filename);

                if ($stmt->execute()) {
                    echo "Test case $identifier uploaded successfully!<br>";
                } else {
                    error_log("Execute statement failed: " . $stmt->error);
                    echo "Failed to upload test case $identifier.";
                }

                $stmt->close();
            } else {
                echo "Invalid test case format. Each test case must have an identifier, input, expected output, and marks.";
            }
        }
    } else {
        echo "No file uploaded or upload error.";
    }
}

$conn->close();









