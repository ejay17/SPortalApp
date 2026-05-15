<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '2') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['delete_tryout'])) {
    $tryout_id = intval($_POST['tryout_id']);

    // Get tryout name before deleting for notification
    $tryout_query = "SELECT t.name, s.sport_name FROM tryouts t 
                     JOIN sports s ON t.sport_id = s.sport_id 
                     WHERE t.tryout_id = '$tryout_id'";
    $tryout_result = mysqli_query($conn, $tryout_query);
    $tryout_row = mysqli_fetch_assoc($tryout_result);

    // Notify registered players before delete cascades
    $players_query = "SELECT user_id FROM player_activity WHERE tryout_id = '$tryout_id'";
    $players_result = mysqli_query($conn, $players_query);

    while ($player = mysqli_fetch_assoc($players_result)) {
        $pid = $player['user_id'];
        $notif_title = "Tryout Cancelled";
        $notif_msg = "The tryout '{$tryout_row['name']}' has been cancelled.";
        mysqli_query($conn, "INSERT INTO notifications (user_id, title, message) VALUES ('$pid', '$notif_title', '$notif_msg')");
    }

    $query = "DELETE FROM tryouts WHERE tryout_id = '$tryout_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['success'] = "Tryout deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting tryout: " . mysqli_error($conn);
    }

    header("Location: ../../../pages/coach_dashboard.php?page=tryouts");
    exit();
}

header("Location: ../../../pages/coach_dashboard.php");
exit();
?>
