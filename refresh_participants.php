<?php
header('Content-Type: application/json');
$roomCode = $_GET['room_code'];

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$result = $conn->query("SELECT name FROM participants WHERE room_code = '$roomCode'");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row['name'];
}

echo json_encode(['participants' => $participants]);
