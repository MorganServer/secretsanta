<?php
session_start();
$conn = new mysqli('localhost', 'dbadmin', 'DBadmin123!', 'secret_santa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roomCode = $_SESSION['room_code'];
$userName = $_SESSION['user_name'];

// Get participants in the room, ordered by turn_order
$result = $conn->query("SELECT id, name, picked_name FROM participants WHERE room_code = '$roomCode' ORDER BY turn_order ASC");
$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

// Get the current turn (from session or database)
$stmt = $conn->prepare("SELECT current_turn FROM rooms WHERE room_code = ?");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$currentTurnResult = $stmt->get_result();
$currentTurn = $currentTurnResult->fetch_assoc()['current_turn'];

// Check if the current user is the one whose turn it is
$isMyTurn = $userName == $currentTurn;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room: <?php echo $roomCode; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Room Code: <?php echo $roomCode; ?></h1>

        <h3>Participants:</h3>
        <div class="row">
            <?php foreach ($participants as $participant): ?>
                <div class="col-md-3 text-center">
                    <p><?php echo $participant['name']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Show Pick My Secret Santa Button if it's the user's turn -->
        <?php if ($isMyTurn): ?>
            <div class="text-center mt-3">
                <button id="pickButton" class="btn btn-success">Pick my Secret Santa</button>
                <p id="pickedName" class="mt-3"></p>
            </div>
        <?php else: ?>
            <p class="text-warning">Waiting for <?php echo $currentTurn; ?>'s turn...</p>
        <?php endif; ?>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // If the button is clicked, make an API call to pick a Secret Santa
        document.getElementById("pickButton")?.addEventListener("click", function() {
            fetch('pick_name.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    room_code: '<?php echo $roomCode; ?>',
                    user_name: '<?php echo $userName; ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                // Show the name they picked
                document.getElementById("pickedName").innerText = "You got " + data.picked_name;
                // Disable the pick button
                document.getElementById("pickButton").disabled = true;

                // Redirect to the next turn (this can be a page refresh or an automatic update in your case)
                setTimeout(function() {
                    location.reload(); // Refresh the page to move to the next turn
                }, 1000);
            })
            .catch(error => console.log('Error picking Secret Santa:', error));
        });
    </script>
</body>
</html>
