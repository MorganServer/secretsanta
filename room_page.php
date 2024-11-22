<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_SESSION['room_code'];
$userName = $_SESSION['user_name'];

// Fetch the current turn from the database
$result = $conn->query("SELECT current_turn FROM rooms WHERE room_code = '$roomCode'");
$currentTurn = $result->fetch_assoc()['current_turn'];

// Fetch the list of participants
$participantsResult = $conn->query("SELECT name, picked_name FROM participants WHERE room_code = '$roomCode'");
$participants = [];
while ($row = $participantsResult->fetch_assoc()) {
    $participants[] = $row;
}

// Check if the user has already been picked
$isPicked = false;
foreach ($participants as $participant) {
    if ($participant['name'] == $userName && $participant['picked_name'] != NULL) {
        $isPicked = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room - Secret Santa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Room Code: <?php echo $roomCode; ?></h1>
        <h3 class="text-center">Current Turn: <?php echo $currentTurn; ?></h3>

        <div class="row">
            <?php foreach ($participants as $participant): ?>
                <div class="col-md-4">
                    <p><?php echo $participant['name']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pick a Name Button (Only shows for current player) -->
        <?php if ($userName == $currentTurn && !$isPicked): ?>
            <form id="pick-form">
                <button type="submit" class="btn btn-primary btn-block">Pick a Name</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
