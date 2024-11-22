<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_SESSION['room_code'];
$userName = $_SESSION['user_name'];

// Get participants in the room
$result = $conn->query("SELECT name, turn_order FROM participants WHERE room_code = '$roomCode' ORDER BY turn_order ASC");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

// Check if everyone is in the room
if (count($participants) > 1) {
    $allJoined = true;
} else {
    $allJoined = false;
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

        <?php if ($allJoined): ?>
            <!-- Form to start the game -->
            <form action="start_game.php" method="POST">
                <input type="hidden" name="room_code" value="<?php echo $roomCode; ?>">
                <button type="submit" class="btn btn-primary btn-block">Start Game</button>
            </form>
        <?php else: ?>
            <p class="text-warning">Waiting for more participants...</p>
        <?php endif; ?>
    </div>
</body>
</html>
