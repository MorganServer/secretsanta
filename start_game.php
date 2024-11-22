<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_SESSION['room_code'];

// Get participants and shuffle them
$result = $conn->query("SELECT id, name FROM participants WHERE room_code = '$roomCode' ORDER BY turn_order ASC");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

shuffle($participants);

// Assign picks
$pickAssignments = [];
for ($i = 0; $i < count($participants); $i++) {
    $pickAssignments[$participants[$i]['id']] = $participants[($i + 1) % count($participants)]['name'];
}

// Update participants with picked names
foreach ($pickAssignments as $id => $pickedName) {
    $stmt = $conn->prepare("UPDATE participants SET picked_name = ? WHERE id = ?");
    $stmt->bind_param("si", $pickedName, $id);
    $stmt->execute();
}

// Update current turn to the first player
$stmt = $conn->prepare("UPDATE rooms SET current_turn = ? WHERE room_code = ?");
$stmt->bind_param("ss", $participants[0]['name'], $roomCode);
$stmt->execute();

echo json_encode(['success' => true, 'pick_assignments' => $pickAssignments]);
?>
