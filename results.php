<?php
session_start();
include 'db.php';

$room_code = $_GET['room_code'];

// Fetch the participants and their assigned person
$stmt = $pdo->prepare("SELECT * FROM participants WHERE room_code = :room_code");
$stmt->execute(['room_code' => $room_code]);
$participants = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results for Room <?= $room_code ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Secret Santa Results for Room <?= $room_code ?></h1>
        <ul class="list-group">
            <?php foreach ($participants as $participant): ?>
                <li class="list-group-item"><?= $participant['name'] ?> is buying a gift for <?= $participant['assigned_to'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
