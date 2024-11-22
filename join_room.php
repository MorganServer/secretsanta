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

    $_SESSION['user_name'] = $name;
    $_SESSION['room_code'] = $roomCode;

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Join a Secret Santa Room</h1>
        <form method="POST" action="join_room.php">
            <div class="mb-3">
                <label for="room_code" class="form-label">Room Code</label>
                <input type="text" id="room_code" name="room_code" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success btn-block">Join Room</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
