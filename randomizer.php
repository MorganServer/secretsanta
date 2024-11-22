<?php
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_POST['room_code'];

// Get all participants
$result = $conn->query("SELECT family_group, name FROM participants WHERE room_code = '$roomCode'");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[$row['name']] = $row['family_group'];
}

$shuffled = $participants;
shuffle($shuffled);

// Generate pairs
$assignments = [];
foreach ($participants as $name => $family) {
    do {
        $receiver = array_rand($shuffled);
    } while ($participants[$receiver] === $family || isset($assignments[$receiver]));

    $assignments[$name] = $receiver;
    unset($shuffled[$receiver]);

    // Save to results
    $stmt = $conn->prepare("INSERT INTO results (room_code, giver, receiver) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $roomCode, $name, $receiver);
    $stmt->execute();
}

echo json_encode($assignments);
