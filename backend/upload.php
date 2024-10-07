<?php  
session_start();
include 'db.php';  // Include your database connection script
$success_message = '';
$previous_submission = null;

// Check if the request is a POST request from a student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['role']) && $_SESSION['role'] == 'student') {
    // Check if a file was uploaded without error
    if (isset($_FILES['submission']) && $_FILES['submission']['error'] == UPLOAD_ERR_OK) {
        $user_id = $_SESSION['user_id'];   // Student ID from session
        $filename = basename($_FILES['submission']['name']);  // Get the base filename
        $file_tmp_path = $_FILES['submission']['tmp_name'];  // Temporary upload path
        $file_content = file_get_contents($file_tmp_path);  // Read the file content

        // Check file extension to allow only Python files
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($file_ext != 'py') {
            echo json_encode(['success' => false, 'error' => 'Only Python (.py) files are allowed.']);
            exit();
        }

        // Set upload directory and generate a unique filename
        $upload_dir = 'uploads/';
        $unique_filename = $user_id . '_' . time() . '_' . $filename; // Create a unique filename
        $target_file_path = $upload_dir . $unique_filename;

        // Check if the directory exists; if not, create it
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);  // Set appropriate permissions
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file_tmp_path, $target_file_path)) {
            // Deactivate previous submissions
            $stmt = $conn->prepare('UPDATE submissions SET is_active = FALSE WHERE user_id = ?');
            $stmt->bind_param('i', $user_id);
            $stmt->execute();  

            // Insert the submission into the database, storing both file path and content
            $stmt = $conn->prepare("INSERT INTO submissions (user_id, filename, filepath, file_content, is_active) VALUES (?, ?, ?, ?, TRUE)");
            $stmt->bind_param("isss", $user_id, $unique_filename, $target_file_path, $file_content);

            if ($stmt->execute()) {
                $submission_id = $stmt->insert_id;  // Get the inserted submission ID
                $success_message = "You have successfully submitted your file!";
                
                // Begin Auto-Grading Process
                auto_grade_submission($conn, $submission_id, $user_id, $target_file_path, $file_content);

                echo json_encode(['success' => true, 'message' => $success_message]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to upload submission.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Error moving the uploaded file.']);
        }
        $prev_stmt = $conn->prepare("SELECT filename, filepath, uploaded_at FROM submissions WHERE user_id = ? AND is_active = TRUE ORDER BY uploaded_at DESC LIMIT 1");
        if ($prev_stmt) {
            $prev_stmt->bind_param("i", $_SESSION['user_id']);
            $prev_stmt->execute();
            $prev_stmt->store_result();
            $prev_stmt->bind_result($prev_filename, $prev_filepath, $prev_uploaded_at);
            if ($prev_stmt->fetch()) {
                $previous_submission = [
                    'filename' => $prev_filename,
                    'filepath' => $prev_filepath,
                    'uploaded_at' => $prev_uploaded_at
                ];
            }
            $prev_stmt->close();
        } 
        echo json_encode(array_merge(['previous_submission' => $previous_submission], isset($response) ? $response : []));
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized or invalid request.']);
}


// Function to handle auto-grading of the submission
function auto_grade_submission($conn, $submission_id, $user_id, $filepath, $file_content) {
    // Parse the student's submission to extract identifiers and code
    $submission_lines = explode("\n", $file_content);
    $student_data = [];
    $current_identifier = null;

    // Parse the submission based on identifier patterns like "#1", "#2"
    foreach ($submission_lines as $line) {
        $line = trim($line);
        if (preg_match('/^#(\d+)/', $line, $matches)) {
            $current_identifier = "#" . $matches[1];  // Use # to match identifiers
            $student_data[$current_identifier] = [];  // Initialize an array for this identifier
        } elseif ($current_identifier) {
            $student_data[$current_identifier][] = $line;  // Append data to the identifier
        }
    }

    // Retrieve the most recent test case for each identifier from the database
    foreach ($student_data as $identifier => $student_code) {
        $test_cases_stmt = $conn->prepare("
            SELECT id, identifier, testcase_input, expected_output, marks
            FROM test_cases
            WHERE identifier = ?
            ORDER BY uploaded_at DESC
            LIMIT 1
        ");
        if (!$test_cases_stmt) {
            error_log("Prepare test cases statement failed: " . $conn->error);
            return;
        }
        $test_cases_stmt->bind_param("s", $identifier);
        $test_cases_stmt->execute();
        $test_cases_stmt->store_result();
        $test_cases_stmt->bind_result($test_case_id, $db_identifier, $testcase_input, $expected_output, $marks);

        // Fetch the test case for this identifier
        if ($test_cases_stmt->fetch()) {
            // Handle multiple inputs by splitting the input string
            $inputs = array_map('trim', explode(',', $testcase_input));

            // Run the student code with the test case inputs
            $student_output = run_student_code($filepath, $inputs, $identifier);

            // Handle multiple expected outputs
            $expected_outputs = array_map('trim', explode(',', $expected_output));
            $student_outputs = array_map('trim', explode(',', $student_output));

            $passed = 1;  // Assume passed unless we find a mismatch
            $marks_earned = $marks;

            // Compare each output from the student with the expected outputs
            foreach ($expected_outputs as $index => $expected) {
                if (!isset($student_outputs[$index]) || $student_outputs[$index] !== $expected) {
                    $passed = 0;  // If any output does not match, the test fails
                    $marks_earned = 0;
                    break;
                }
            }

            // Insert the result into the results table
            $result_stmt = $conn->prepare("
                INSERT INTO results (submission_id, test_case_id, test_case_identifier, actual_output, passed, marks)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            if (!$result_stmt) {
                error_log("Prepare result statement failed: " . $conn->error);
                return;
            }
            $result_stmt->bind_param("iissii", $submission_id, $test_case_id, $db_identifier, $student_output, $passed, $marks_earned);

            if ($result_stmt->execute()) {
                echo "Test case ID: $test_case_id | Identifier: $db_identifier | Student output: $student_output | Expected output: $expected_output | Passed: $passed | Marks earned: $marks_earned<br>";
            } else {
                error_log("Execute result statement failed: " . $result_stmt->error);
                echo "Failed to record result for Test Case ID: $test_case_id.";
            }
            $result_stmt->close();
        } else {
            echo "No test case found for identifier: $identifier<br>";
        }

        $test_cases_stmt->close();
    }
}

// Function to run the student's code with multiple inputs
function run_student_code($filepath, $inputs, $identifier) {
    if (!file_exists($filepath)) {
        return "Error: File does not exist.";
    }

    // Prepare to run the student's Python code
    $command = escapeshellcmd("python $filepath");
    $input_string = implode(" ", array_map('escapeshellarg', $inputs));  // Combine inputs into one string
    $full_command = "$command $input_string " . escapeshellarg($identifier);

    // Capture the output
    $student_output = shell_exec($full_command);
    if ($student_output === null) {
        return "Error running student's code.";
    }

    return trim($student_output);  // Remove extra whitespace/newlines
}
