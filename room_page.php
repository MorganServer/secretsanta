<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_GET['room_code'];
$_SESSION['room_code'] = $roomCode;

// Fetch participants in the room
$stmt = $conn->prepare("SELECT name FROM participants WHERE room_code = ? ORDER BY turn_order");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$result = $stmt->get_result();

$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row['name'];
}

// Check if all participants are in the room
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM participants WHERE room_code = ?");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$totalParticipants = $stmt->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Room Code: <?php echo $roomCode; ?></h1>
        <h3 class="text-center">Participants</h3>
        <div class="d-flex justify-content-center flex-wrap">
            <?php foreach ($participants as $participant) { ?>
                <div class="m-2 p-2 border"><?php echo $participant; ?></div>
            <?php } ?>
        </div>

        <?php if ($totalParticipants > 1) { ?>
            <form action="start_game.php" method="POST" class="text-center">
                <button type="submit" class="btn btn-primary">Start Game</button>
            </form>
        <?php } else { ?>
            <p class="text-center">Waiting for more participants...</p>
        <?php } ?>
    </div>
</body>
</html>
