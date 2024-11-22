<?php
include 'db.php';

if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    // Fetch participants with assigned names
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
    <title>Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Results: Secret Santa Pairs</h2>

        <ul class="list-group">
            <?php foreach ($participants as $participant): ?>
                <li class="list-group-item"><?= htmlspecialchars($participant['name']) ?> -> <?= htmlspecialchars($participant['assigned_name']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
