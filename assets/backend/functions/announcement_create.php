<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '2') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['create_announcement'])) {
    $coach_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $sport_name = trim($_POST['sport_name']);

    $query = "INSERT INTO announcements (user_id, title, content, sport_name)
              VALUES ('$coach_id', '$title', '$content', '$sport_name')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Notify all players who have this sport
        $players_query = "SELECT DISTINCT u.user_id FROM users u 
                          JOIN sports s ON u.user_id = s.user_id 
                          WHERE s.sport_name = '$sport_name' AND u.role_id = '1'";
        $players_result = mysqli_query($conn, $players_query);

        while ($player = mysqli_fetch_assoc($players_result)) {
            $pid = $player['user_id'];
            $notif_title = "New Announcement: $title";
            $notif_msg = substr($content, 0, 200);
            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message) VALUES ('$pid', '$notif_title', '$notif_msg')");
        }

        $_SESSION['success'] = "Announcement posted successfully!";
    } else {
        $_SESSION['error'] = "Error posting announcement: " . mysqli_error($conn);
    }

    header("Location: ../../../pages/coach_dashboard.php?page=announcements");
    exit();
}

header("Location: ../../../pages/coach_dashboard.php");
exit();
?>
