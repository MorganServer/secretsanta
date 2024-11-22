<?php
include 'db.php';

if (isset($_POST['create_room'])) {
    $room_name = $_POST['room_name'];
    $max_participants = $_POST['max_participants'];

    $stmt = $pdo->prepare("INSERT INTO rooms (room_name, max_participants) VALUES (?, ?)");
    $stmt->execute([$room_name, $max_participants]);
    header("Location: join_room.php?room_id=" . $pdo->lastInsertId());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Create a Secret Santa Room</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="room_name" class="form-label">Room Name</label>
                <input type="text" class="form-control" id="room_name" name="room_name" required>
            </div>
            <div class="mb-3">
                <label for="max_participants" class="form-label">Max Participants</label>
                <input type="number" class="form-control" id="max_participants" name="max_participants" required>
            </div>
            <button type="submit" name="create_room" class="btn btn-primary">Create Room</button>
        </form>
    </div>
</body>
</html>
