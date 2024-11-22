<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomName = $_POST['room_name'] ?? null; // Added null check for safety
    $maxParticipants = isset($_POST['max_participants']) ? intval($_POST['max_participants']) : 0;
    $roomCode = substr(md5(uniqid(rand(), true)), 0, 6);

    // Validate inputs
    if (empty($roomName) || $maxParticipants <= 0) {
        die("Invalid input: Room name and max participants are required.");
    }

    $conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO rooms (room_name, max_participants, room_code) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }

    $stmt->bind_param("sis", $roomName, $maxParticipants, $roomCode);
    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Redirect to Join Room page with room code
    header("Location: join_room.php?room_code=" . $roomCode);
    exit();
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