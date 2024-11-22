<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $creatorName = $_POST['creator_name'];
    $roomCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6); // Generate random room code

    // Create the room
    $stmt = $conn->prepare("INSERT INTO rooms (room_code) VALUES (?)");
    $stmt->bind_param("s", $roomCode);
    if ($stmt->execute()) {
        // Insert the creator as the first participant
        $stmt = $conn->prepare("INSERT INTO participants (room_code, name, turn_order) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $roomCode, $creatorName, 1); // creator gets the turn order 1
        $stmt->execute();

        $_SESSION['room_code'] = $roomCode;
        $_SESSION['user_name'] = $creatorName;

        header("Location: room_page.php?room_code=$roomCode");
        exit();
    } else {
        echo "Error creating room!";
    }
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
                <label for="creator_name" class="form-label">Your Name</label>
                <input type="text" id="creator_name" name="creator_name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success btn-block">Create Room</button>
        </form>
    </div>
</body>
</html>
