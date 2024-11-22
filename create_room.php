<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate a unique room code (6 characters)
    $roomCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    $roomCreator = $_POST['name'];

    // Insert the room into the database
    $stmt = $conn->prepare("INSERT INTO rooms (room_code, current_turn) VALUES (?, ?)");
    $stmt->bind_param("ss", $roomCode, $roomCreator);
    $stmt->execute();

    // Store room code and room creator name in session
    $_SESSION['room_code'] = $roomCode;
    $_SESSION['user_name'] = $roomCreator;

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
                <label for="name" class="form-label">Your Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create Room</button>
        </form>
    </div>
</body>
</html>
