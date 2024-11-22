<?php
session_start();
include 'db.php';

$room_code = $_GET['room_code'];

// Fetch the participants for this room
$stmt = $pdo->prepare("SELECT * FROM participants WHERE room_code = :room_code");
$stmt->execute(['room_code' => $room_code]);
$participants = $stmt->fetchAll();

// Check if all participants have joined
$all_joined = count($participants) > 1; // If more than 1 participant, assume everyone has joined

// Store participant names for the session and check if current user is logged in
$participant_names = array_column($participants, 'name');

if (!isset($_SESSION['name'])) {
    $_SESSION['name'] = ''; // Default if not set
}

// Check if the current user is the admin
$is_admin = false;
foreach ($participants as $participant) {
    if ($participant['name'] == $_SESSION['name'] && $participant['status'] == 'admin') {
        $is_admin = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room <?= $room_code ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Auto-refresh every 3 seconds to show updated participants in real time
        setInterval(function() {
            location.reload();
        }, 3000);
    </script>
</head>
<body>
    <div class="container">
        <h1>Room Code: <?= $room_code ?></h1>
        <h3>Participants:</h3>
        <ul class="list-group">
            <?php foreach ($participants as $participant): ?>
                <li class="list-group-item"><?= $participant['name'] ?> (<?= ucfirst($participant['status']) ?>)</li>
            <?php endforeach; ?>
        </ul>

        <?php if ($all_joined): ?>
            <h4>All participants have joined. Ready to start the selection!</h4>
            
            <!-- Only show the "Start Selection" button for the admin -->
            <?php if ($is_admin): ?>
                <form method="POST" action="start_game.php?room_code=<?= $room_code ?>">
                    <button type="submit" class="btn btn-success mt-3">Start Selection</button>
                </form>
            <?php endif; ?>

        <?php else: ?>
            <p>Waiting for more participants to join...</p>
        <?php endif; ?>
    </div>
</body>
</html>
