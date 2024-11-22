<?php
session_start();

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get room code and current user
$roomCode = $_GET['room_code'];
$currentUser = $_SESSION['user_name'] ?? null;

// Fetch participants
$result = $conn->query("SELECT * FROM participants WHERE room_code = '$roomCode'");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

// Fetch current turn
$result = $conn->query("SELECT current_turn FROM rooms WHERE room_code = '$roomCode'");
$currentTurn = $result->fetch_assoc()['current_turn'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room - <?php echo $roomCode; ?></title>
    <link rel="stylesheet" href="styles.css">
    <script>
        let roomCode = '<?php echo $roomCode; ?>';

        function checkTurn() {
            fetch(`get_turn.php?room_code=${roomCode}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('current-turn-display').innerText = data.current_turn;
            });
        }

        setInterval(checkTurn, 2000); // Poll every 2 seconds
    </script>
</head>
<body>
    <div class="container">
        <h1>Room: <?php echo $roomCode; ?></h1>
        <h3>Participants</h3>
        <ul>
            <?php foreach ($participants as $participant): ?>
                <li><?php echo $participant['name']; ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Current Turn: <span id="current-turn-display"><?php echo $currentTurn; ?></span></h3>

        <!-- "Pick" button only visible for the current player -->
        <?php if ($currentTurn === $currentUser): ?>
            <button id="pick-button" onclick="pickName()">Pick for Me</button>
        <?php else: ?>
            <p>Waiting for <?php echo $currentTurn; ?> to pick a name...</p>
        <?php endif; ?>

        <button id="start-button" onclick="startGame()">Start Game</button>
    </div>

    <script>
        function startGame() {
            fetch('start_game.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ room_code: roomCode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.current_turn) {
                    alert(`Game started! It's ${data.current_turn}'s turn.`);
                    location.reload();
                } else {
                    alert('Error starting game.');
                }
            });
        }

        function pickName() {
            fetch('pick_name.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ room_code: roomCode, player: '<?php echo $currentUser; ?>' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    alert(`You picked: ${data.name}`);
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
