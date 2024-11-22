<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$roomCode = $data['room_code'];
$userName = $data['user_name'];

// Get all participants in the room, excluding the user
$result = $conn->query("SELECT id, name, picked_name FROM participants WHERE room_code = '$roomCode' AND name != '$userName' ORDER BY turn_order ASC");
$availableParticipants = [];
while ($row = $result->fetch_assoc()) {
    if (!$row['picked_name']) {
        $availableParticipants[] = $row; // Only add participants who haven't been picked yet
    }
}

// If there are any available participants, pick one at random
if (count($availableParticipants) > 0) {
    $randomParticipant = $availableParticipants[array_rand($availableParticipants)];
    $pickedName = $randomParticipant['name'];

    // Update the current user's picked_name
    $stmt = $conn->prepare("UPDATE participants SET picked_name = ? WHERE name = ?");
    $stmt->bind_param("ss", $pickedName, $userName);
    $stmt->execute();

    // Update the current turn in the database to the next participant
    $stmt = $conn->prepare("SELECT turn_order FROM participants WHERE room_code = ? AND name = ?");
    $stmt->bind_param("ss", $roomCode, $userName);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentUserTurn = $result->fetch_assoc()['turn_order'];

    // Get the next player
    $stmt = $conn->prepare("SELECT name FROM participants WHERE room_code = ? AND turn_order = ?");
    $nextTurn = $currentUserTurn + 1;
    $stmt->bind_param("si", $roomCode, $nextTurn);
    $stmt->execute();
    $nextTurnResult = $stmt->get_result();
    $nextTurnParticipant = $nextTurnResult->fetch_assoc();

    $nextTurnName = $nextTurnParticipant ? $nextTurnParticipant['name'] : $userName; // if it's the last player, loop back

    // Update the next turn in the rooms table
    $stmt = $conn->prepare("UPDATE rooms SET current_turn = ? WHERE room_code = ?");
    $stmt->bind_param("ss", $nextTurnName, $roomCode);
    $stmt->execute();

    // Return the name that was picked
    echo json_encode(['picked_name' => $pickedName]);
} else {
    echo json_encode(['error' => 'No available participants to pick']);
}
?>
