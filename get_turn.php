<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$roomCode = $_GET['room_code'];
$result = $conn->query("SELECT current_turn FROM rooms WHERE room_code = '$roomCode'");
$currentTurn = $result->fetch_assoc()['current_turn'];

echo json_encode(['current_turn' => $currentTurn]);
?>
