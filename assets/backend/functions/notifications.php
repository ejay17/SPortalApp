<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

// Get unread notifications count
if (isset($_GET['count'])) {
    $query = "SELECT COUNT(*) as unread FROM notifications WHERE user_id = '$user_id' AND is_read = 0";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    echo json_encode(['unread_count' => intval($row['unread'])]);
    exit();
}

// Mark all as read
if (isset($_POST['mark_read'])) {
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE user_id = '$user_id' AND is_read = 0");
    echo json_encode(['success' => true]);
    exit();
}

// Mark single as read
if (isset($_POST['mark_read_id'])) {
    $notif_id = intval($_POST['mark_read_id']);
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE notification_id = '$notif_id' AND user_id = '$user_id'");
    echo json_encode(['success' => true]);
    exit();
}

// Get all notifications
$query = "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = $row;
}

echo json_encode(['notifications' => $notifications]);
exit();
?>
