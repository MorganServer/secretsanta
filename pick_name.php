<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$roomCode = $data['room_code'];
$userName = $data['user_name'];

// Get all participants except the current user
$result = $conn->query("SELECT id, name FROM participants WHERE room_code = '$roomCode' AND name != '$userName' AND picked_name IS NULL");

$availableParticipants = [];
while ($row = $result->fetch_assoc()) {
    $availableParticipants[] = $row;
}

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

    $nextTurn = $currentUserTurn + 1;
    $stmt = $conn->prepare("SELECT name FROM participants WHERE room_code = ? AND turn_order = ?");
    $stmt->bind_param("si", $roomCode, $nextTurn);
    $stmt->execute();
    $nextTurnResult = $stmt->get_result();
    $nextTurnParticipant = $nextTurnResult->fetch_assoc();

    $nextTurnName = $nextTurnParticipant ? $nextTurnParticipant['name'] : $userName;

    $stmt = $conn->prepare("UPDATE rooms SET current_turn = ? WHERE room_code = ?");
    $stmt->bind_param("ss", $nextTurnName, $roomCode);
    $stmt->execute();

    echo json_encode(['picked_name' => $pickedName]);
} else {
    echo json_encode(['error' => 'No available participants to pick']);
}
?>
