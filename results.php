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
    <title>Secret Santa Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Secret Santa Results for Room: <?= $room_code ?></h1>
        <ul class="list-group">
            <?php foreach ($participants as $participant): ?>
                <li class="list-group-item"><?= $participant['name'] ?> will buy a gift for <?= $participant['assigned_to'] ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="join_room.php" class="btn btn-primary mt-3">Join Another Room</a>
    </div>
</body>
</html>
