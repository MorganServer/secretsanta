<?php
include 'db.php';

if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    // Fetch participants for randomization
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE room_id = ?");
    $stmt->execute([$room_id]);
    $participants = $stmt->fetchAll();

    // Randomize Secret Santa pairs
    $names = array_column($participants, 'name');
    shuffle($names);

    // Update pairs in database
    foreach ($participants as $index => $participant) {
        $stmt = $pdo->prepare("UPDATE participants SET assigned_name = ? WHERE id = ?");
        $stmt->execute([$names[$index], $participant['id']]);
    }

    header("Location: results.php?room_id=$room_id");
}
?>
