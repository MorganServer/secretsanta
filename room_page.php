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

// Get participants
$result = $conn->query("SELECT * FROM participants WHERE room_code = '$roomCode'");
if (!$result) {
    die("Error retrieving participants: " . $conn->error);
}

$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

// Check if the creator is logged in (session)
$creatorName = $_SESSION['user_name'] ?? '';
$isCreator = $participants[0]['name'] === $creatorName;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isCreator) {
    // Reorder participants
    $order = $_POST['order'] ?? [];
    if (empty($order)) {
        die("No order provided for reordering participants.");
    }

    foreach ($order as $index => $name) {
        $stmt = $conn->prepare("UPDATE participants SET turn_order = ? WHERE room_code = ? AND name = ?");
        $stmt->bind_param("iss", $index, $roomCode, $name);
        if (!$stmt->execute()) {
            die("Error updating turn order: " . $stmt->error);
        }
    }

    // Start the game by setting the first turn index
    $_SESSION['turn_index'] = 0;

    // Redirect to the pick_name page
    header("Location: pick_name.php?room_code=$roomCode");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Room Participants</h1>
        <ul>
            <?php foreach ($participants as $participant): ?>
                <li><?php echo htmlspecialchars($participant['name']); ?></li>
            <?php endforeach; ?>
        </ul>

        <?php if ($isCreator): ?>
            <h3>Reorder Participants</h3>
            <form method="POST">
                <ul>
                    <?php foreach ($participants as $participant): ?>
                        <li>
                            <input type="text" name="order[]" value="<?php echo htmlspecialchars($participant['name']); ?>" required>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button type="submit">Reorder and Start Game</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
