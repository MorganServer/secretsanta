<?php
session_start();
include 'db.php';

// Check if the user is logged in (i.e., has a name in the session)
if (!isset($_SESSION['name'])) {
    header('Location: join_room.php');
    exit();
}

$room_code = $_GET['room_code'];
$participant_name = $_SESSION['name'];

// Fetch the participants to ensure that only the admin can start the game
$stmt = $pdo->prepare("SELECT * FROM participants WHERE room_code = :room_code");
$stmt->execute(['room_code' => $room_code]);
$participants = $stmt->fetchAll();

$is_admin = false;
foreach ($participants as $participant) {
    if ($participant['name'] == $participant_name && $participant['status'] == 'admin') {
        $is_admin = true;
        break;
    }
}

if (!$is_admin) {
    // Redirect if the current user is not the admin
    header("Location: room_page.php?room_code=$room_code");
    exit();
}

// If the admin has clicked the start button, we can begin the Secret Santa process
// Randomize the name assignments
$names = array_column($participants, 'name');
shuffle($names);

// Assign each participant a random name (excluding their own name)
foreach ($participants as $key => $participant) {
    // Ensure that no one is assigned to themselves
    $assigned_to = $names[$key];
    while ($assigned_to == $participant['name']) {
        shuffle($names); // Re-shuffle if the person picks themselves
        $assigned_to = $names[$key];
    }

    // Update the participant with the assigned name
    $stmt = $pdo->prepare("UPDATE participants SET assigned_to = :assigned_to WHERE id = :id");
    $stmt->execute(['assigned_to' => $assigned_to, 'id' => $participant['id']]);
}

// Redirect participants to the page where they can pick a name (if not yet selected)
header("Location: pick_name.php?room_code=$room_code");
exit();
?>
