<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '1') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['join_tryout'])) {
    $user_id = $_SESSION['user_id'];
    $tryout_id = intval($_POST['tryout_id']);

    // Check if already joined
    $check = "SELECT * FROM player_activity WHERE user_id = '$user_id' AND tryout_id = '$tryout_id'";
    $check_result = mysqli_query($conn, $check);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "You have already joined this tryout.";
    } else {
        $query = "INSERT INTO player_activity (tryout_id, user_id) VALUES ('$tryout_id', '$user_id')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            // Notify the coach
            $coach_query = "SELECT u.user_id FROM users u 
                            JOIN sports s ON u.user_id = s.user_id 
                            JOIN tryouts t ON t.sport_id = s.sport_id 
                            WHERE t.tryout_id = '$tryout_id' AND u.role_id = '2' LIMIT 1";
            $coach_result = mysqli_query($conn, $coach_query);
            $coach_row = mysqli_fetch_assoc($coach_result);

            if ($coach_row) {
                $coach_id = $coach_row['user_id'];
                $player_name = $_SESSION['username'];
                $tryout_query = "SELECT name FROM tryouts WHERE tryout_id = '$tryout_id'";
                $tryout_result = mysqli_query($conn, $tryout_query);
                $tryout_row = mysqli_fetch_assoc($tryout_result);

                $notif_title = "New Player Joined";
                $notif_msg = "$player_name has joined your tryout '{$tryout_row['name']}'.";
                mysqli_query($conn, "INSERT INTO notifications (user_id, title, message) VALUES ('$coach_id', '$notif_title', '$notif_msg')");
            }

            $_SESSION['success'] = "Successfully joined the tryout!";
        } else {
            $_SESSION['error'] = "Error joining tryout: " . mysqli_error($conn);
        }
    }

    header("Location: ../../../pages/player_dashboard.php?page=tryouts");
    exit();
}

header("Location: ../../../pages/player_dashboard.php");
exit();
?>
