<?php
session_start();

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomCode = $_POST['room_code'];
    $names = explode(',', $_POST['names']); // Split names by commas
    $names = array_map('trim', $names); // Trim whitespace

    foreach ($names as $name) {
        if (!empty($name)) {
            // Join each name into the room
            $stmt = $conn->prepare("INSERT INTO participants (room_code, name) VALUES (?, ?)");
            $stmt->bind_param("ss", $roomCode, $name);
            $stmt->execute();
        }
    }

    // Set session for the last entered name as the main user
    $_SESSION['user_name'] = end($names);

    header("Location: room_page.php?room_code=$roomCode");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Room</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Join a Secret Santa Room</h1>
        <form method="POST">
            <div class="form-group">
                <label for="room_code">Room Code:</label>
                <input type="text" id="room_code" name="room_code" value="<?php echo htmlspecialchars($_GET['room_code'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="names">Names (comma-separated):</label>
                <input type="text" id="names" name="names" placeholder="e.g., Garrett, Jane, Sarah" required>
            </div>
            <button type="submit">Join</button>
        </form>
    </div>
</body>
</html>
