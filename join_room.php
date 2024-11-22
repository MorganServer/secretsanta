<?php
session_start();

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomCode = $_POST['room_code'];
    $name = $_POST['name'];

    // Join the room
    $stmt = $conn->prepare("INSERT INTO participants (room_code, name, family_group) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $roomCode, $name, $name); // Assume each person is their own family group
    $stmt->execute();

    // Store user session
    $_SESSION['user_name'] = $name;

    header("Location: room_page.php?room_code=$roomCode");
    exit();
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
        <label>Room Code: <input type="text" name="room_code" value="<?php echo $_GET['room_code'] ?? ''; ?>" required></label><br>
        <label>Family Group: <input type="text" name="family_group" required></label><br>
        <label>Names (comma-separated): <input type="text" name="names" required></label><br>
        <button type="submit">Join</button>
    </form>
</body>
</html>
