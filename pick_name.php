<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$roomCode = $data['room_code'];
$userName = $data['user_name'];

// Get the list of unpicked participants
$result = $conn->query("SELECT id, name FROM participants WHERE room_code = '$roomCode' AND picked_name IS NULL AND name != '$userName'");
$availableParticipants = [];
while ($row = $result->fetch_assoc()) {
    $availableParticipants[] = $row;
}

// Pick a random name
if (count($availableParticipants) > 0) {
    $randomParticipant = $availableParticipants[array_rand($availableParticipants)];
    $pickedName = $randomParticipant['name'];

    // Update the user's picked name
    $stmt = $conn->prepare("UPDATE participants SET picked_name = ? WHERE name = ?");
    $stmt->bind_param("ss", $pickedName, $userName);
    $stmt->execute();

    // Update the current turn to the next participant
    $stmt = $conn->prepare("SELECT turn_order FROM participants WHERE room_code = ? AND name = ?");
    $stmt->bind_param("ss", $roomCode, $userName);
    $stmt->execute();
    $currentTurn = $stmt->get_result()->fetch_assoc()['turn_order'];

    // Move to next player
    $nextTurn = $currentTurn + 1;
    $stmt = $conn->prepare("UPDATE rooms SET current_turn = (SELECT name FROM participants WHERE room_code = ? AND turn_order = ?) WHERE room_code = ?");
    $stmt->bind_param("sis", $roomCode, $nextTurn, $roomCode);
    $stmt->execute();

    echo json_encode(['picked_name' => $pickedName]);
} else {
    echo json_encode(['error' => 'No available participants to pick']);
}
?>
