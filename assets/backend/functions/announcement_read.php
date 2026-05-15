<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

if ($role_id == '2') {
    // Coach: get own announcements
    $query = "SELECT a.*, u.given_name, u.last_name FROM announcements a 
              JOIN users u ON a.user_id = u.user_id 
              WHERE a.user_id = '$user_id'
              ORDER BY a.created_at DESC";
} else {
    // Player: get announcements for their sports
    $query = "SELECT a.*, u.given_name, u.last_name FROM announcements a 
              JOIN users u ON a.user_id = u.user_id 
              WHERE a.sport_name IN (SELECT sport_name FROM sports WHERE user_id = '$user_id')
              ORDER BY a.created_at DESC";
}

$result = mysqli_query($conn, $query);

$announcements = [];
while ($row = mysqli_fetch_assoc($result)) {
    $announcements[] = $row;
}

echo json_encode(['announcements' => $announcements]);
exit();
?>

