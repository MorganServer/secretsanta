<?php
$roomCode = $_GET['room_code'];

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT giver, receiver FROM results WHERE room_code = '$roomCode'");

echo "<h2>Secret Santa Results</h2>";
while ($row = $result->fetch_assoc()) {
    echo "<p>" . $row['giver'] . " → " . $row['receiver'] . "</p>";
}
?>
