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

// Fetch participants and their picked names
$stmt = $conn->prepare("SELECT name, picked_name FROM participants WHERE room_code = ?");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$participantsResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Secret Santa Results</h1>
        <ul class="list-group">
            <?php while ($row = $participantsResult->fetch_assoc()): ?>
                <li class="list-group-item">
                    <?php echo htmlspecialchars($row['name']); ?> picked <?php echo htmlspecialchars($row['picked_name']); ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
