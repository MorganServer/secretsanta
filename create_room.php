<?php
include 'db.php';

function generateRoomCode() {
    return strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_code = generateRoomCode();
    $stmt = $pdo->prepare("INSERT INTO rooms (room_code) VALUES (:room_code)");
    $stmt->execute(['room_code' => $room_code]);

    // Automatically add the room creator as an admin participant
    $creator_name = $_POST['creator_name']; // Getting the name of the room creator

    $stmt = $pdo->prepare("INSERT INTO participants (room_code, name, status) VALUES (:room_code, :name, 'admin')");
    $stmt->execute(['room_code' => $room_code, 'name' => $creator_name]);

    header("Location: room_page.php?room_code=$room_code");
    exit();
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
    <div class="container">
        <h1>Create Secret Santa Room</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="creator_name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="creator_name" name="creator_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Room</button>
        </form>
    </div>
</body>
</html>
