<?
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];  // Assuming the student is logged in
$query = "SELECT filename, uploaded_at, status FROM submissions WHERE user_id = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$submissions = [];
while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
}

echo json_encode($submissions);  // Return the submissions in JSON format
