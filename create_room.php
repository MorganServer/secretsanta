<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate a random 6-character room code
    $roomCode = strtoupper(bin2hex(random_bytes(3)));

    // Create the room
    $stmt = $conn->prepare("INSERT INTO rooms (room_code) VALUES (?)");
    $stmt->bind_param("s", $roomCode);
    $stmt->execute();

    $_SESSION['room_code'] = $roomCode;

    // Redirect to the room page
    header("Location: room_page.php?room_code=$roomCode");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Create a Secret Santa Room</h1>
        <form method="POST">
            <button type="submit" class="btn btn-primary btn-block">Create Room</button>
        </form>
    </div>
</body>
</html>
