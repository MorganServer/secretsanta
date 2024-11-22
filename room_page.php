<?php
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_GET['room_code'];
$result = $conn->query("SELECT * FROM participants WHERE room_code = '$roomCode'");
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
    <title>Room - <?php echo $roomCode; ?></title>
    <link rel="stylesheet" href="styles.css">
    <script>
        let roomCode = '<?php echo $roomCode; ?>';

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

        function checkTurn() {
            fetch(`get_turn.php?room_code=${roomCode}`)
            .then(response => response.json())
            .then(data => {
                const currentPlayer = data.current_turn;
                const currentUser = document.getElementById('current-user').innerText;

                document.getElementById('current-turn-display').innerText = currentPlayer;

                if (currentPlayer === currentUser) {
                    document.getElementById('pick-button').style.display = 'block';
                } else {
                    document.getElementById('pick-button').style.display = 'none';
                }
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
        <h3>Current Turn: <span id="current-turn-display">Loading...</span></h3>
        <p id="current-user" style="display: none;">John</p> <!-- Replace 'John' dynamically -->

        <button id="start-button" onclick="startGame()">Start Game</button>
        <button id="pick-button" style="display: none;" onclick="pickName()">Pick for Me</button>
    </div>
</body>
</html>
