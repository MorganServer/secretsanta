<?php
include 'db.php';

$room_code = $_GET['room_code'];

// Store the participant's name in session (when joining)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['name'] = $_POST['name']; // Set participant name in session
}

$stmt = $pdo->prepare("SELECT * FROM participants WHERE room_code = :room_code");
$stmt->execute(['room_code' => $room_code]);
$participants = $stmt->fetchAll();

// Check if all participants have joined
$all_joined = count($participants) > 1;

$participant_names = array_column($participants, 'name');

// Check if the current user is one of the participants and set session variable
if (!isset($_SESSION['name'])) {
    $_SESSION['name'] = ''; // If not set, default it
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
        setInterval(function() {
            location.reload();
        }, 3000); // Refresh every 3 seconds to get real-time updates
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

        <?php if ($all_joined): ?>
            <h4>All participants have joined. Ready to start the selection!</h4>
            <?php if ($_SESSION['name']): ?>
                <!-- Only show "Pick Selection" for the current user -->
                <form method="POST" action="pick_name.php">
                    <button type="submit" class="btn btn-success mt-3">Pick Selection</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p>Waiting for more participants to join...</p>
        <?php endif; ?>
    </div>
</body>
</html>
