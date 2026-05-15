<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '2') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['update_tryout'])) {
    $tryout_id = intval($_POST['tryout_id']);
    $name = trim($_POST['tryout_name']);
    $description = trim($_POST['description']);
    $notes = trim($_POST['notes']);
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);
    $location = trim($_POST['location']);

    $query = "UPDATE tryouts SET 
              name = '$name',
              description = '$description',
              notes = '$notes',
              date = '$date',
              time = '$time',
              location = '$location'
              WHERE tryout_id = '$tryout_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Notify all players registered for this tryout
        $players_query = "SELECT u.user_id FROM users u 
                          JOIN player_activity pa ON u.user_id = pa.user_id 
                          WHERE pa.tryout_id = '$tryout_id'";
        $players_result = mysqli_query($conn, $players_query);

        while ($player = mysqli_fetch_assoc($players_result)) {
            $pid = $player['user_id'];
            $notif_title = "Tryout Updated: $name";
            $notif_msg = "The tryout '$name' has been updated. New schedule: $date at $time. Location: $location";
            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message) VALUES ('$pid', '$notif_title', '$notif_msg')");
        }

        $_SESSION['success'] = "Tryout updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating tryout: " . mysqli_error($conn);
    }

    header("Location: ../../../pages/coach_dashboard.php?page=tryouts");
    exit();
}

header("Location: ../../../pages/coach_dashboard.php");
exit();
?>
