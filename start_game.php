<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$roomCode = $data['room_code'];

// Get the first participant
$result = $conn->query("SELECT name FROM participants WHERE room_code = '$roomCode' LIMIT 1");
$firstParticipant = $result->fetch_assoc()['name'];

// Set the first turn
$stmt = $conn->prepare("UPDATE rooms SET current_turn = ? WHERE room_code = ?");
$stmt->bind_param("ss", $firstParticipant, $roomCode);
$stmt->execute();

echo json_encode(['current_turn' => $firstParticipant]);
?>
