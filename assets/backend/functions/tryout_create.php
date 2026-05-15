<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '2') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['create_tryout'])) {
    $coach_id = $_SESSION['user_id'];
    $sport_name = trim($_POST['sport_name']);
    $name = trim($_POST['tryout_name']);
    $description = trim($_POST['description']);
    $notes = trim($_POST['notes']);
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);
    $location = trim($_POST['location']);

    // Get the sport_id for this coach's sport
    $sport_query = "SELECT sport_id FROM sports WHERE user_id = '$coach_id' AND sport_name = '$sport_name' LIMIT 1";
    $sport_result = mysqli_query($conn, $sport_query);

    if (mysqli_num_rows($sport_result) > 0) {
        $sport_row = mysqli_fetch_assoc($sport_result);
        $sport_id = $sport_row['sport_id'];
    } else {
        // Create sport entry if not exists
        $insert_sport = "INSERT INTO sports (user_id, sport_name) VALUES ('$coach_id', '$sport_name')";
        mysqli_query($conn, $insert_sport);
        $sport_id = mysqli_insert_id($conn);
    }

    $query = "INSERT INTO tryouts (sport_id, name, description, notes, date, time, location)
              VALUES ('$sport_id', '$name', '$description', '$notes', '$date', '$time', '$location')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Notify all players who have this sport
        $players_query = "SELECT DISTINCT u.user_id FROM users u 
                          JOIN sports s ON u.user_id = s.user_id 
                          WHERE s.sport_name = '$sport_name' AND u.role_id = '1'";
        $players_result = mysqli_query($conn, $players_query);

        while ($player = mysqli_fetch_assoc($players_result)) {
            $pid = $player['user_id'];
            $notif_title = "New Tryout: $name";
            $notif_msg = "A new tryout for $sport_name has been scheduled on $date at $time. Location: $location";
            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message) VALUES ('$pid', '$notif_title', '$notif_msg')");
        }

        $_SESSION['success'] = "Tryout created successfully!";
    } else {
        $_SESSION['error'] = "Error creating tryout: " . mysqli_error($conn);
    }

    header("Location: ../../../pages/coach_dashboard.php?page=tryouts");
    exit();
}

header("Location: ../../../pages/coach_dashboard.php");
exit();
?>

