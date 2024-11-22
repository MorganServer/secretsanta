<?php
require_once 'db.php';

$participant_id = $_SESSION['participant_id']; // Assume participant_id is stored in session
$sql = "SELECT p.name, s.name AS assigned_to_name FROM participants p
        JOIN participants s ON p.assigned_to = s.id
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $participant_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result) {
    echo "You are buying a gift for: " . $result['assigned_to_name'];
} else {
    echo "Error fetching assignment.";
}
?>
