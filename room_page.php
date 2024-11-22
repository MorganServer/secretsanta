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

$result = $conn->query("SELECT * FROM participants WHERE room_code = '$roomCode'");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

// Check if the creator is logged in (session)
$creatorName = $_SESSION['user_name'] ?? '';
$isCreator = $participants[0]['name'] === $creatorName;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isCreator) {
    $order = $_POST['order'] ?? [];
    if (empty($order)) {
        die("No order provided for reordering participants.");
    }

    foreach ($order as $index => $name) {
        $stmt = $conn->prepare("UPDATE participants SET turn_order = ? WHERE room_code = ? AND name = ?");
        $stmt->bind_param("iss", $index, $roomCode, $name);
        $stmt->execute();
    }

    $_SESSION['turn_index'] = 0;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Room Participants</h1>
        <ul class="list-group">
            <?php foreach ($participants as $participant): ?>
                <li class="list-group-item d-inline-block">
                    <?php echo htmlspecialchars($participant['name']); ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($isCreator): ?>
            <h3 class="my-4">Reorder Participants</h3>
            <form method="POST">
                <ul class="list-group">
                    <?php foreach ($participants as $participant): ?>
                        <li class="list-group-item">
                            <input type="text" name="order[]" value="<?php echo htmlspecialchars($participant['name']); ?>" required>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button type="submit" class="btn btn-primary btn-block mt-3">Reorder and Start Game</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
