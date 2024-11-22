<?php
$data = json_decode(file_get_contents('php://input'), true);
$roomCode = $data['room_code'];
$player = $data['player'];

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all names and family groups
$result = $conn->query("SELECT name, family_group FROM participants WHERE room_code = '$roomCode'");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[$row['name']] = $row['family_group'];
}

// Get already picked names
$result = $conn->query("SELECT receiver FROM results WHERE room_code = '$roomCode'");
$picked = [];
while ($row = $result->fetch_assoc()) {
    $picked[] = $row['receiver'];
}

// Exclude family group and already picked
$validNames = array_diff(array_keys($participants), $picked);
$validNames = array_filter($validNames, fn($name) => $participants[$name] !== $participants[$player]);

if (empty($validNames)) {
    echo json_encode(['error' => 'No valid names left']);
    exit();
}

$selectedName = $validNames[array_rand($validNames)];

// Save the result
$stmt = $conn
