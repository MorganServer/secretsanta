<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_SESSION['room_code'];
$userName = $_SESSION['user_name'];

// Get all participants
$stmt = $conn->prepare("SELECT name FROM participants WHERE room_code = ? ORDER BY turn_order ASC");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$result = $stmt->get_result();
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room: <?php echo $roomCode; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Room Code: <?php echo $roomCode; ?></h1>

        <h3>Participants:</h3>
        <div class="row">
            <?php foreach ($participants as $participant): ?>
                <div class="col-md-3 text-center">
                    <p><?php echo $participant['name']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Start the game if all participants are in -->
        <?php if (count($participants) > 1): ?>
            <form method="POST" action="start_game.php">
                <button type="submit" class="btn btn-primary btn-block">Start Game</button>
            </form>
        <?php else: ?>
            <p>Waiting for more players...</p>
        <?php endif; ?>
    </div>
</body>
</html>
