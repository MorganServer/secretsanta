<?php
include 'db.php';

if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    // Fetch room details
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch();

    // Fetch participants
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE room_id = ?");
    $stmt->execute([$room_id]);
    $participants = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Room: <?= htmlspecialchars($room['room_name']) ?></h2>
        <h4>Max Participants: <?= $room['max_participants'] ?></h4>

        <h5>Participants:</h5>
        <ul class="list-group">
            <?php foreach ($participants as $participant): ?>
                <li class="list-group-item"><?= htmlspecialchars($participant['name']) ?></li>
            <?php endforeach; ?>
        </ul>

        <a href="start_game.php?room_id=<?= $room_id ?>" class="btn btn-primary mt-3">Start Game</a>
    </div>
</body>
</html>
