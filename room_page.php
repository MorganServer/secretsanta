<?php
$roomCode = $_GET['room_code'];

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch room participants
$result = $conn->query("SELECT name FROM participants WHERE room_code = '$roomCode'");
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
    <title>Room: <?php echo $roomCode; ?></title>
    <link rel="stylesheet" href="styles.css">
    <script>
        const roomCode = "<?php echo $roomCode; ?>";

        function refreshParticipants() {
            fetch(`refresh_participants.php?room_code=${roomCode}`)
                .then(response => response.json())
                .then(data => {
                    const participantList = document.getElementById('participant-list');
                    participantList.innerHTML = '';
                    data.participants.forEach(name => {
                        const li = document.createElement('li');
                        li.textContent = name;
                        participantList.appendChild(li);
                    });

                    // Check if even number of participants
                    const startButton = document.getElementById('start-btn');
                    if (data.participants.length % 2 === 0) {
                        startButton.disabled = false;
                    } else {
                        startButton.disabled = true;
                    }
                });
        }

        setInterval(refreshParticipants, 3000); // Refresh every 3 seconds
    </script>
</head>
<body>
    <div class="container">
        <h1>Room: <?php echo $roomCode; ?></h1>
        <h2>Participants:</h2>
        <ul id="participant-list">
            <?php foreach ($participants as $name) { echo "<li>$name</li>"; } ?>
        </ul>
        <button id="start-btn" onclick="startGame()" disabled>Start Game</button>
        <form method="POST" action="join_room.php">
            <input type="hidden" name="room_code" value="<?php echo $roomCode; ?>">
            <label for="name">Your Name(s):</label>
            <input type="text" id="name" name="name" required>
            <button type="submit">Join Room</button>
        </form>
    </div>
</body>
</html>
