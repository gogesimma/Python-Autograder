<?php 
require 'db.php';

// Fetch submissions, student info, test case results, and associated test case details
$query = "SELECT students.name, students.surname, users.username, submissions.file_content, 
                 results.test_case_id, results.actual_output, test_cases.expected_output, 
                 results.passed, results.marks
          FROM results
          INNER JOIN submissions ON results.submission_id = submissions.id
          INNER JOIN students ON submissions.user_id = students.user_id
          INNER JOIN users ON students.user_id = users.id
          INNER JOIN test_cases ON results.test_case_id = test_cases.id
          WHERE submissions.is_active = TRUE
          ORDER BY students.surname, students.name, users.username";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr>
            <th>Student Name</th>
            <th>Username (Student Number)</th>
            <th>Submission File</th>
            <th>Test Case ID</th>
            <th>Student Output</th>
            <th>Expected Output</th>
            <th>Passed</th>
            <th>Marks</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['name'] . " " . $row['surname'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td><pre>" . htmlentities($row['file_content']) . "</pre></td>";
        echo "<td>" . $row['test_case_id'] . "</td>";
        echo "<td><pre>" . htmlentities($row['actual_output']) . "</pre></td>";
        echo "<td><pre>" . htmlentities($row['expected_output']) . "</pre></td>";
        echo "<td>" . ($row['passed'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $row['marks'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No submissions found.";
}

$conn->close();




