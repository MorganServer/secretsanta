<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomCode = $_POST['room_code'];
    $name = $_POST['name'];

    // Debugging: Output room code and name to check if they are received correctly
    echo "Room Code: $roomCode<br>";
    echo "Name: $name<br>";

    // Check if the room exists
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE room_code = ?");
    $stmt->bind_param("s", $roomCode);
    $stmt->execute();
    $result = $stmt->get_result();

    // Debugging: Check if the room exists
    if ($result->num_rows == 0) {
        // Room doesn't exist
        $error = "Room code not found. Please check the code and try again.";
    } else {
        // Room exists, proceed with joining the room
        $stmt = $conn->prepare("INSERT INTO participants (room_code, name, family_group) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $roomCode, $name, $name); // Assume each person is their own family group
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $name;
            $_SESSION['room_code'] = $roomCode;

            // Redirect to the room page
            header("Location: room_page.php?room_code=$roomCode");
            exit();
        } else {
            $error = "An error occurred while joining the room. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Room - Secret Santa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .btn-success {
            width: 100%;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center mb-4">Join a Secret Santa Room</h1>

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="join_room.php">
        <div class="mb-3">
            <label for="room_code" class="form-label">Room Code</label>
            <input type="text" id="room_code" name="room_code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Join Room</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
