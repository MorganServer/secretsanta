<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_SESSION['room_code'];

// Get the first participant's name
$result = $conn->query("SELECT name FROM participants WHERE room_code = '$roomCode' ORDER BY turn_order ASC LIMIT 1");
$firstParticipant = $result->fetch_assoc();

if ($firstParticipant) {
    // Set the current turn to the first participant
    $stmt = $conn->prepare("UPDATE rooms SET current_turn = ? WHERE room_code = ?");
    $stmt->bind_param("ss", $firstParticipant['name'], $roomCode);
    $stmt->execute();

    // Redirect to room page
    header("Location: room_page.php?room_code=$roomCode");
    exit();
}
?>
