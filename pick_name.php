<?php
session_start();

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_GET['room_code'] ?? '';
$currentPlayer = $_SESSION['user_name'] ?? '';

if (empty($roomCode) || empty($currentPlayer)) {
    die("Room code and player are required");
}

// Get participants
$result = $conn->query("SELECT * FROM participants WHERE room_code = '$roomCode' ORDER BY turn_order ASC");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

// Get the current player's turn from session
$turnIndex = $_SESSION['turn_index'] ?? 0;
$nextPlayer = $participants[$turnIndex]['name'];

// Check if it's the current player's turn
$canPick = $currentPlayer === $nextPlayer;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canPick) {
    // Pick a random name (excluding self and already picked)
    $picked = $_POST['picked'] ?? [];
    $validNames = array_diff(array_column($participants, 'name'), $picked, [$currentPlayer]);

    if (empty($validNames)) {
        die("No valid names left.");
    }

    $selectedName = $validNames[array_rand($validNames)];

    // Save the result
    $stmt = $conn->prepare("INSERT INTO results (room_code, giver, receiver) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $roomCode, $currentPlayer, $selectedName);
    $stmt->execute();

    // Update turn index for the next player
    $_SESSION['turn_index'] = ($turnIndex + 1) % count($participants);

    // Redirect to the same page to update the game state
    header("Location: pick_name.php?room_code=$roomCode");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pick a Name</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Pick a Name</h1>

        <h2>It's <?php echo htmlspecialchars($nextPlayer); ?>'s turn!</h2>

        <?php if ($canPick): ?>
            <form method="POST">
                <button type="submit" name="pick_for_me">Pick for Me</button>
            </form>
        <?php else: ?>
            <p>Wait for your turn!</p>
        <?php endif; ?>

        <div id="picked-names">
            <h3>Picked Names:</h3>
            <ul>
                <?php
                $result = $conn->query("SELECT * FROM results WHERE room_code = '$roomCode'");
                while ($row = $result->fetch_assoc()):
                ?>
                    <li><?php echo htmlspecialchars($row['giver']); ?> picked <?php echo htmlspecialchars($row['receiver']); ?></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>
