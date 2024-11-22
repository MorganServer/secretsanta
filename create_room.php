<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the creator's name from the form
    $creatorName = $_POST['creator_name'];

    // Generate a random room code
    $roomCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);

    // Insert the room into the database
    $stmt = $conn->prepare("INSERT INTO rooms (room_code, created_at) VALUES (?, NOW())");
    if ($stmt === false) {
        die("Error preparing query: " . $conn->error);
    }
    $stmt->bind_param("s", $roomCode);

    // Check if the room was created successfully
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }

    // Auto-join the creator into the room
    $stmt = $conn->prepare("INSERT INTO participants (room_code, name, turn_order) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing query: " . $conn->error);
    }
    $turnOrder = 1; // Creator is the first participant
    $stmt->bind_param("ssi", $roomCode, $creatorName, $turnOrder);

    // Check if the creator was added successfully
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }

    // Redirect to the room page after successful creation
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            padding-top: 50px;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create a Secret Santa Room</h2>
    <form method="POST">
        <div class="form-group">
            <label for="creator_name">Your Name:</label>
            <input type="text" class="form-control" id="creator_name" name="creator_name" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Room</button>
    </form>
</div>

</body>
</html>
