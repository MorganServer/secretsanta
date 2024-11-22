<?php
session_start();

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_GET['room_code'] ?? '';

if (empty($roomCode)) {
    die("Room code is required");
}

// Get all participants and their picked status
$result = $conn->query("SELECT * FROM participants WHERE room_code = '$roomCode'");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

// Check if the number of participants is even
if (count($participants) % 2 !== 0) {
    echo "<p>Waiting for an even number of participants. Please wait...</p>";
    exit();
}

// Get picked participants
$result = $conn->query("SELECT receiver FROM results WHERE room_code = '$roomCode'");
$picked = [];
while ($row = $result->fetch_assoc()) {
    $picked[] = $row['receiver'];
}

// Get the current player's turn from session
$currentPlayer = $_SESSION['user_name'] ?? '';

// Store the current turn index in the session (rotating through the participants)
$turnIndex = $_SESSION['turn_index'] ?? 0;
$nextPlayer = $participants[$turnIndex]['name'];

// Determine if the current player can pick
$canPick = $currentPlayer === $nextPlayer;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pick_for_me']) && $canPick) {
    $player = $currentPlayer;

    // Get valid names for the current player (excluding self and already picked)
    $validNames = array_diff(array_column($participants, 'name'), $picked, [$player]);

    if (empty($validNames)) {
        die("No valid names left");
    }

    $selectedName = $validNames[array_rand($validNames)];

    // Save the result
    $stmt = $conn->prepare("INSERT INTO results (room_code, giver, receiver) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $roomCode, $player, $selectedName);
    $stmt->execute();

    // Update the turn index for the next player
    $_SESSION['turn_index'] = ($turnIndex + 1) % count($participants);

    // Redirect to the same page to update the game state
    header("Location: room_page.php?room_code=$roomCode");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Santa Room</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to the Secret Santa Room</h1>

        <div id="participants">
            <h3>Participants:</h3>
            <ul>
                <?php foreach ($participants as $participant): ?>
                    <li><?php echo htmlspecialchars($participant['name']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div id="turn-info">
            <h3>It's <?php echo htmlspecialchars($nextPlayer); ?>'s turn!</h3>

            <?php if ($canPick): ?>
                <form method="POST">
                    <button type="submit" name="pick_for_me">Pick for Me</button>
                </form>
            <?php else: ?>
                <p>Wait for your turn!</p>
            <?php endif; ?>
        </div>
        
        <div id="picked-names">
            <h3>Picked Names:</h3>
            <ul>
                <?php foreach ($picked as $name): ?>
                    <li><?php echo htmlspecialchars($name['receiver']); ?> has been picked.</li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>
