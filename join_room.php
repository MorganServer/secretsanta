<?php
include 'db.php';

if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
}

if (isset($_POST['join_room'])) {
    $name = $_POST['name'];
    $stmt = $pdo->prepare("INSERT INTO participants (room_id, name) VALUES (?, ?)");
    $stmt->execute([$room_id, $name]);
    header("Location: room_page.php?room_id=$room_id");
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
    <div class="container mt-5">
        <h2>Join Secret Santa Room</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" name="join_room" class="btn btn-primary">Join Room</button>
        </form>
    </div>
</body>
</html>
