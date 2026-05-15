<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '2') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['delete_announcement'])) {
    $announcement_id = intval($_POST['announcement_id']);
    $coach_id = $_SESSION['user_id'];

    // Verify this announcement belongs to this coach
    $check = "SELECT * FROM announcements WHERE announcement_id = '$announcement_id' AND user_id = '$coach_id'";
    $check_result = mysqli_query($conn, $check);

    if (mysqli_num_rows($check_result) > 0) {
        $query = "DELETE FROM announcements WHERE announcement_id = '$announcement_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $_SESSION['success'] = "Announcement deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting announcement: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Announcement not found or you don't have permission to delete it.";
    }

    header("Location: ../../../pages/coach_dashboard.php?page=announcements");
    exit();
}

header("Location: ../../../pages/coach_dashboard.php");
exit();
?>
