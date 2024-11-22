<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
    $creatorName = $_POST['creator_name'];

    // Create the room
    $stmt = $conn->prepare("INSERT INTO rooms (room_code, created_at) VALUES (?, NOW())");
    if (!$stmt) {
        die("Room creation statement failed: " . $conn->error);
    }
    $stmt->bind_param("s", $roomCode);
    if (!$stmt->execute()) {
        die("Room creation failed: " . $stmt->error);
    }

    // Auto-join the creator
    $stmt = $conn->prepare("INSERT INTO participants (room_code, name, family_group) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Participant statement failed: " . $conn->error);
    }
    $stmt->bind_param("sss", $roomCode, $creatorName, $creatorName);
    if (!$stmt->execute()) {
        die("Participant creation failed: " . $stmt->error);
    }

    // Redirect to the room page
    header("Location: room_page.php?room_code=$roomCode");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Create a Secret Santa Room</h1>
        <form method="POST" action="create_room.php">
            <label for="creator_name">Your Name:</label>
            <input type="text" id="creator_name" name="creator_name" required>
            <button type="submit">Create Room</button>
        </form>
    </div>
</body>
</html>
