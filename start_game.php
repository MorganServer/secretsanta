<?php
session_start(); // Start the session to store session variables like current_turn

header('Content-Type: application/json');

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Get room code from the request
$roomCode = $data['room_code'];

// Validate the room code to prevent SQL injection
if (!$roomCode) {
    echo json_encode(['error' => 'Room code is required']);
    exit();
}

// Fetch the first participant based on the room code
$stmt = $conn->prepare("SELECT name FROM participants WHERE room_code = ? ORDER BY turn_order LIMIT 1");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any participants
if ($result->num_rows > 0) {
    // Get the first participant's name
    $firstParticipant = $result->fetch_assoc()['name'];

    // Set the first participant as the current turn in the rooms table
    $updateStmt = $conn->prepare("UPDATE rooms SET current_turn = ? WHERE room_code = ?");
    $updateStmt->bind_param("ss", $firstParticipant, $roomCode);
    $updateStmt->execute();

    // Store the current turn in the session
    $_SESSION['current_turn'] = $firstParticipant;

    // Respond with the first participant's name
    echo json_encode(['current_turn' => $firstParticipant]);
} else {
    // No participants found for the given room code
    echo json_encode(['error' => 'No participants found for this room']);
}
?>
