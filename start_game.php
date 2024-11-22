<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_POST['room_code'];

// Get all participants in the room
$result = $conn->query("SELECT id, name FROM participants WHERE room_code = '$roomCode' ORDER BY turn_order ASC");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

// Shuffle participants to randomize the order
shuffle($participants);

// Now assign each person a random name, ensuring no one picks themselves
$pickAssignments = [];
for ($i = 0; $i < count($participants); $i++) {
    $currentId = $participants[$i]['id'];
    $nextParticipant = $participants[($i + 1) % count($participants)]; // Get the next participant in the array, wrap around to the start

    // Ensure no one picks themselves
    if ($nextParticipant['id'] == $currentId) {
        $nextParticipant = $participants[($i + 2) % count($participants)]; // Skip to the next participant
    }

    $pickAssignments[$currentId] = $nextParticipant['name'];

    // Update the 'picked_name' in the database for the current participant
    $stmt = $conn->prepare("UPDATE participants SET picked_name = ? WHERE id = ?");
    $stmt->bind_param("si", $nextParticipant['name'], $currentId);
    $stmt->execute();
}

// Update the current turn to the first person (first turn in shuffled list)
$stmt = $conn->prepare("UPDATE rooms SET current_turn = ? WHERE room_code = ?");
$stmt->bind_param("ss", $participants[0]['name'], $roomCode);
$stmt->execute();

// Optionally, you can send the result back to the client or display it in the next page
echo json_encode(['success' => true, 'pick_assignments' => $pickAssignments]);
?>
