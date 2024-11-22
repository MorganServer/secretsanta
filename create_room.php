<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));  // Generate a unique room code
    $roomName = $_POST['room_name'];

    // Insert room into database
    $stmt = $conn->prepare("INSERT INTO rooms (room_code, room_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $roomCode, $roomName);
    $stmt->execute();

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
    <title>Create Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Create a Secret Santa Room</h1>
        <form method="POST" action="create_room.php">
            <div class="mb-3">
                <label for="room_name" class="form-label">Room Name</label>
                <input type="text" id="room_name" name="room_name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create Room</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
