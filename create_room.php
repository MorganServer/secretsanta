<?php
include 'db.php';

function generateRoomCode() {
    return strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_code = generateRoomCode();
    $stmt = $pdo->prepare("INSERT INTO rooms (room_code) VALUES (:room_code)");
    $stmt->execute(['room_code' => $room_code]);

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
            <button type="submit" class="btn btn-primary">Create Room</button>
        </form>
    </div>
</body>
</html>
