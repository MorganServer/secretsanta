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

// Fetch participants ordered by their turn order
$stmt = $conn->prepare("SELECT * FROM participants WHERE room_code = ? ORDER BY turn_order ASC");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$participantsResult = $stmt->get_result();
$participants = [];

while ($row = $participantsResult->fetch_assoc()) {
    $participants[] = $row;
}

// Check if it's the current user's turn
$currentTurnIndex = $_SESSION['turn_index'] ?? 0;
$currentParticipant = $participants[$currentTurnIndex] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $currentParticipant) {
    // Randomly assign a name for the current participant
    $availableParticipants = array_filter($participants, function($p) use ($currentParticipant) {
        return $p['name'] !== $currentParticipant['name'] && $p['picked_name'] === null;
    });

    $randomIndex = array_rand($availableParticipants);
    $pickedParticipant = $availableParticipants[$randomIndex];

    $stmt = $conn->prepare("UPDATE participants SET picked_name = ? WHERE id = ?");
    $stmt->bind_param("si", $pickedParticipant['name'], $currentParticipant['id']);
    $stmt->execute();

    // Move to next participant's turn
    $_SESSION['turn_index']++;

    if ($_SESSION['turn_index'] >= count($participants)) {
        header("Location: results.php?room_code=$roomCode");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pick a Name</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Pick a Name</h1>
        <?php if ($currentParticipant): ?>
            <h3><?php echo htmlspecialchars($currentParticipant['name']); ?>, choose a name!</h3>
            <form method="POST">
                <button type="submit" class="btn btn-primary btn-block">Pick a Name</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
