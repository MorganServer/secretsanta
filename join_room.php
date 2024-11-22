<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomCode = $_POST['room_code'];
    $familyGroup = $_POST['family_group'];
    $names = explode(",", $_POST['names']); // Names entered as a comma-separated list

    $conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($names as $name) {
        $stmt = $conn->prepare("INSERT INTO participants (room_code, family_group, name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $roomCode, $familyGroup, trim($name));
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    echo "Names successfully added to the room!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Room</title>
</head>
<body>
    <h2>Join a Room</h2>
    <form method="POST">
        <label>Room Code: <input type="text" name="room_code" required></label><br>
        <label>Family Group: <input type="text" name="family_group" required></label><br>
        <label>Names (comma-separated): <input type="text" name="names" required></label><br>
        <button type="submit">Join</button>
    </form>
</body>
</html>
