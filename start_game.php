<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Database connection
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get the room code from the POST data
$data = json_decode(file_get_contents('php://input'), true);
$roomCode = $data['room_code'];

// Check if there is an even number of participants
$result = $conn->query("SELECT COUNT(*) AS participant_count FROM participants WHERE room_code = '$roomCode'");
$participantCount = $result->fetch_assoc()['participant_count'];

if ($participantCount % 2 != 0) {
    echo json_encode(['error' => 'There must be an even number of participants to start the game.']);
    exit();
}

// Get the list of participants in the desired order
$participantsResult = $conn->query("SELECT id, name FROM participants WHERE room_code = '$roomCode' ORDER BY turn_order ASC");
$participants = [];

while ($row = $participantsResult->fetch_assoc()) {
    $participants[] = $row;
}

// Shuffle the list of participants to randomize the pairing
shuffle($participants);

// Prepare the list of names to assign randomly
$namesToPick = array_map(function ($participant) {
    return $participant['name'];
}, $participants);

// Assign names to each participant, making sure no one picks themselves
$pickAssignments = [];
foreach ($participants as $index => $participant) {
    // Get the list of names left for picking (excluding the current participant's name)
    $namesLeft = array_diff($namesToPick, [$participant['name']]);

    // Randomly pick a name from the remaining names
    $pickedName = $namesLeft[array_rand($namesLeft)];

    // Store the pick assignment
    $pickAssignments[$participant['id']] = $pickedName;

    // Remove the picked name from the list to ensure no one is picked twice
    $namesToPick = array_diff($namesToPick, [$pickedName]);
}

// Update the database with the pick assignments
foreach ($pickAssignments as $participantId => $pickedName) {
    $stmt = $conn->prepare("UPDATE participants SET picked_name = ? WHERE id = ?");
    $stmt->bind_param("si", $pickedName, $participantId);
    $stmt->execute();
}

// Set the current turn to the first participant
$firstParticipant = $participants[0]['name'];
$stmt = $conn->prepare("UPDATE rooms SET current_turn = ? WHERE room_code = ?");
$stmt->bind_param("ss", $firstParticipant, $roomCode);
$stmt->execute();

// Respond with success and the name of the first participant
echo json_encode(['current_turn' => $firstParticipant, 'pick_assignments' => $pickAssignments]);
?>
