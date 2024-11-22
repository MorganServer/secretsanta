<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomCode = $_POST['room_code'];
    $name = $_POST['name'];

    // Check if the room exists
    $stmt = $conn->prepare("SELECT id FROM rooms WHERE room_code = ?");
    $stmt->bind_param("s", $roomCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Get the number of participants in the room to assign a turn order
        $stmt = $conn->prepare("SELECT COUNT(*) AS participant_count FROM participants WHERE room_code = ?");
        $stmt->bind_param("s", $roomCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $participantCount = $result->fetch_assoc()['participant_count'];

        // Insert the participant into the database with the next available turn order
        $stmt = $conn->prepare("INSERT INTO participants (room_code, name, turn_order) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $roomCode, $name, $participantCount + 1);
        $stmt->execute();

        $_SESSION['room_code'] = $roomCode;
        $_SESSION['user_name'] = $name;

        header("Location: room_page.php?room_code=$roomCode");
        exit();
    } else {
        echo "Room code not found!";
    }
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
</body>
</html>
