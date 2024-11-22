<?php
include 'db.php';

if (!isset($_SESSION['name'])) {
    header('Location: join_room.php');
    exit();
}

$room_code = $_GET['room_code'];
$participant_name = $_SESSION['name'];

// Fetch participants to ensure the session's user is valid
$stmt = $pdo->prepare("SELECT * FROM participants WHERE room_code = :room_code");
$stmt->execute(['room_code' => $room_code]);
$participants = $stmt->fetchAll();

$names = array_column($participants, 'name');
$names = array_diff($names, [$participant_name]); // Exclude the current user's name

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Randomly assign a name (but not to themselves)
    $picked_name = $_POST['picked_name'];

    // Update the participant with the selected name
    $stmt = $pdo->prepare("UPDATE participants SET assigned_to = :assigned_to WHERE name = :name AND room_code = :room_code");
    $stmt->execute(['assigned_to' => $picked_name, 'name' => $participant_name, 'room_code' => $room_code]);

    // Check if all participants have picked a name
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants WHERE room_code = :room_code AND assigned_to IS NOT NULL");
    $stmt->execute(['room_code' => $room_code]);
    $count = $stmt->fetchColumn();

    if ($count == count($participants)) {
        // Redirect to results page when all have made selections
        header("Location: results.php?room_code=$room_code");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pick Secret Santa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1><?= $participant_name ?>, pick your Secret Santa</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="picked_name" class="form-label">Pick a name</label>
                <select class="form-select" name="picked_name" required>
                    <option value="">Select a name</option>
                    <?php foreach ($names as $name): ?>
                        <option value="<?= $name ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit Selection</button>
        </form>
    </div>
</body>
</html>
