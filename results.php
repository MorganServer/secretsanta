<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_SESSION['room_code'];

// Get all participants and their picks
$result = $conn->query("SELECT name, picked_name FROM participants WHERE room_code = '$roomCode'");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Santa Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Secret Santa Results</h1>

        <div class="row">
            <?php foreach ($participants as $participant): ?>
                <div class="col-md-4 text-center">
                    <h3><?php echo $participant['name']; ?></h3>
                    <p>Picked: <?php echo $participant['picked_name']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="room_page.php?room_code=<?php echo $roomCode; ?>" class="btn btn-primary btn-block mt-4">Back to Room</a>
    </div>
</body>
</html>
