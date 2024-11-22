<?php
header('Content-Type: application/json');

// Get the incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['room_code'], $data['player'])) {
    echo json_encode(['error' => 'Invalid input data']);
    exit();
}

$roomCode = $data['room_code'];
$player = $data['player'];

// Database connection
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Get all names and family groups for the room
$participants = [];
$result = $conn->query("SELECT name, family_group FROM participants WHERE room_code = '$roomCode'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $participants[$row['name']] = $row['family_group'];
    }
} else {
    echo json_encode(['error' => 'Failed to fetch participants']);
    exit();
}

// Get already picked names for the room
$picked = [];
$result = $conn->query("SELECT receiver FROM results WHERE room_code = '$roomCode'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $picked[] = $row['receiver'];
    }
} else {
    echo json_encode(['error' => 'Failed to fetch picked names']);
    exit();
}

// Filter out invalid names
$validNames = array_diff(array_keys($participants), $picked);
$validNames = array_filter($validNames, function ($name) use ($participants, $player) {
    return $participants[$name] !== $participants[$player]; // Exclude same family group
});

// Check if there are valid names left
if (empty($validNames)) {
    echo json_encode(['error' => 'No valid names left for the player']);
    exit();
}

// Pick a random name
$selectedName = $validNames[array_rand($validNames)];

// Save the pairing in the database
$stmt = $conn->prepare("INSERT INTO results (room_code, giver, receiver) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("sss", $roomCode, $player, $selectedName);
    $stmt->execute();
    $stmt->close();
} else {
    echo json_encode(['error' => 'Failed to save the pairing']);
    exit();
}

// Return the selected name
echo json_encode(['name' => $selectedName]);
$conn->close();
?>
