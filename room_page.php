<?php
session_start();

// Check if the room code is passed via URL
if (!isset($_GET['room_code'])) {
    die("Room code is required.");
}

$roomCode = $_GET['room_code'];

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the list of participants in the room
$stmt = $conn->prepare("SELECT name FROM participants WHERE room_code = ?");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any participants
if ($result->num_rows == 0) {
    die("No participants found in this room.");
}

$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room - Secret Santa</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            padding-top: 50px;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            width: 100%;
        }
        .participants {
            list-style: none;
            padding: 0;
        }
        .participants li {
            background-color: #e9ecef;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Room Code: <?php echo $roomCode; ?></h2>
    <h3>Participants:</h3>
    <ul class="participants">
        <?php foreach ($participants as $participant) : ?>
            <li><?php echo htmlspecialchars($participant); ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Check if the game can start -->
    <?php if (count($participants) % 2 == 0) : ?>
        <form action="start_game.php" method="POST">
            <input type="hidden" name="room_code" value="<?php echo $roomCode; ?>">
            <button type="submit" class="btn btn-primary">Start Game</button>
        </form>
    <?php else : ?>
        <p>You need an even number of participants to start the game.</p>
    <?php endif; ?>
</div>

</body>
</html>
