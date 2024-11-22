<?php
$roomCode = $_GET['room_code'];

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get participant names
$result = $conn->query("SELECT name FROM participants WHERE room_code = '$roomCode'");
$names = [];
while ($row = $result->fetch_assoc()) {
    $names[] = $row['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Page</title>
    <script>
        let names = <?php echo json_encode($names); ?>;
        let currentIndex = 0;

        function startGame() {
            document.getElementById('current-player').innerText = names[currentIndex];
            document.getElementById('game-controls').style.display = 'block';
            document.getElementById('start-btn').style.display = 'none';
        }

        function pickName() {
            let playerName = names[currentIndex];
            fetch('pick_name.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ room_code: '<?php echo $roomCode; ?>', player: playerName }),
            })
            .then(response => response.json())
            .then(data => {
                alert(playerName + " picked: " + data.name);
                currentIndex++;
                if (currentIndex < names.length) {
                    document.getElementById('current-player').innerText = names[currentIndex];
                } else {
                    alert("Game finished! Check final results.");
                    location.href = 'results.php?room_code=<?php echo $roomCode; ?>';
                }
            });
        }
    </script>
</head>
<body>
    <h2>Room: <?php echo $roomCode; ?></h2>
    <h3>Participants in the Hat:</h3>
    <ul>
        <?php foreach ($names as $name) { echo "<li>$name</li>"; } ?>
    </ul>
    <button id="start-btn" onclick="startGame()">Start Game</button>
    <div id="game-controls" style="display: none;">
        <h3>Current Player: <span
