<?php
session_start();
include 'db.php';

$room_code = $_GET['room_code'];
$participant_name = $_SESSION['name'];

// Fetch the participant data
$stmt = $pdo->prepare("SELECT * FROM participants WHERE room_code = :room_code AND name = :name");
$stmt->execute(['room_code' => $room_code, 'name' => $participant_name]);
$participant = $stmt->fetch();

// Check if the participant has already picked a name
if ($participant['assigned_to'] != NULL) {
    echo "<h3>You have already picked a name: {$participant['assigned_to']}</h3>";
    exit();
}

echo "<h3>Pick your Secret Santa: {$participant['assigned_to']}</h3>";

?>
