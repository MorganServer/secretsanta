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

    echo "Room created! Share this code: " . $roomCode;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room</title>
</head>
<body>
    <h2>Create a Room</h2>
    <form method="POST">
        <label>Room Name: <input type="text" name="room_name" required></label><br>
        <label>Max Participants: <input type="number" name="max_participants" required></label><br>
        <button type="submit">Create</button>
    </form>
</body>
</html>
