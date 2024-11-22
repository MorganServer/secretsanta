<?php
include 'db.php';

$room_code = $_GET['room_code'];
$stmt = $pdo->prepare("SELECT * FROM participants WHERE room_code = :room_code");
$stmt->execute(['room_code' => $room_code]);
$participants = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room <?= $room_code ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        setInterval(function() {
            location.reload();
        }, 3000); // Refresh every 3 seconds
    </script>
</head>
<body>
    <div class="container">
        <h1>Room Code: <?= $room_code ?></h1>
        <h3>Participants:</h3>
        <ul class="list-group">
            <?php foreach ($participants as $participant): ?>
                <li class="list-group-item"><?= $participant['name'] ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="join_room.php" class="btn btn-secondary mt-3">Join Another Room</a>
    </div>
</body>
</html>
