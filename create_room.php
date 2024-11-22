<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomName = $_POST['room_name'];
    $maxParticipants = intval($_POST['max_participants']);
    $roomCode = substr(md5(uniqid(rand(), true)), 0, 6);

    $conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO rooms (room_name, max_participants, room_code) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $roomName, $maxParticipants, $roomCode);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Redirect to Join Room page with room code
    header("Location: join_room.php?room_code=" . $roomCode);
    exit();
}
?>
