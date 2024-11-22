<?php
session_start();
include 'db.php';

// When the user submits the form to create a room
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $room_code = strtoupper(bin2hex(random_bytes(3))); // Random 6-character code

    // Insert the room and creator into the participants table
    $stmt = $pdo->prepare("INSERT INTO participants (name, room_code, status) VALUES (:name, :room_code, 'admin')");
    $stmt->execute(['name' => $name, 'room_code' => $room_code]);

    // Store the user's name and room code in the session
    $_SESSION['name'] = $name;
    $_SESSION['room_code'] = $room_code;

    // Redirect to the room page
    header("Location: room_page.php?room_code=$room_code");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Create a Secret Santa Room</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Room</button>
        </form>
    </div>
</body>
</html>
