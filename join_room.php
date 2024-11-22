<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_code = $_POST['room_code'];
    $name = $_POST['name'];

    // Check if the room exists
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_code = :room_code");
    $stmt->execute(['room_code' => $room_code]);
    $room = $stmt->fetch();

    if ($room) {
        // Add participant to the room
        $stmt = $pdo->prepare("INSERT INTO participants (room_code, name) VALUES (:room_code, :name)");
        $stmt->execute(['room_code' => $room_code, 'name' => $name]);
        header("Location: room_page.php?room_code=$room_code");
        exit();
    } else {
        $error = "Room code does not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Join Secret Santa Room</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="room_code" class="form-label">Room Code</label>
                <input type="text" class="form-control" id="room_code" name="room_code" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Join Room</button>
        </form>
    </div>
</body>
</html>
