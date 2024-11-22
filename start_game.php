<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_SESSION['room_code'];

// Shuffle and assign turn orders
$stmt = $conn->prepare("SELECT id, name FROM participants WHERE room_code = ?");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$result = $stmt->get_result();
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

// Shuffle participants to randomize turn order
shuffle($participants);

// Assign turn order and reset picked names
$turnOrder = 1;
foreach ($participants as $participant) {
    $stmt = $conn->prepare("UPDATE participants SET turn_order = ?, picked_name = NULL WHERE id = ?");
    $stmt->bind_param("ii", $turnOrder, $participant['id']);
    $stmt->execute();
    $turnOrder++;
}

// Set the first player's turn
$stmt = $conn->prepare("UPDATE rooms SET current_turn = ? WHERE room_code = ?");
$stmt->bind_param("ss", $participants[0]['name'], $roomCode);
$stmt->execute();

header("Location: room_page.php?room_code=$roomCode");
exit();
?>
