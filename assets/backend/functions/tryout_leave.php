<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '1') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['leave_tryout'])) {
    $user_id = $_SESSION['user_id'];
    $tryout_id = intval($_POST['tryout_id']);

    $query = "DELETE FROM player_activity WHERE user_id = '$user_id' AND tryout_id = '$tryout_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['success'] = "You have left the tryout.";
    } else {
        $_SESSION['error'] = "Error leaving tryout: " . mysqli_error($conn);
    }

    header("Location: ../../../pages/player_dashboard.php?page=my_tryouts");
    exit();
}

header("Location: ../../../pages/player_dashboard.php");
exit();
?>
