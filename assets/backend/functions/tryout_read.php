<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '2') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$coach_id = $_SESSION['user_id'];

// Get tryout participants for a specific tryout
if (isset($_GET['tryout_id'])) {
    $tryout_id = intval($_GET['tryout_id']);
    
    $query = "SELECT u.user_id, u.given_name, u.middle_name, u.last_name, u.suffix, u.student_id, u.year_level, u.institute_campus, u.sex
              FROM users u
              JOIN player_activity pa ON u.user_id = pa.user_id
              WHERE pa.tryout_id = '$tryout_id' AND u.role_id = '1'
              ORDER BY u.last_name, u.given_name";
    $result = mysqli_query($conn, $query);
    
    $players = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $players[] = $row;
    }
    
    echo json_encode(['players' => $players]);
    exit();
}

// Get all tryouts for this coach with participant counts
$query = "SELECT t.tryout_id, t.name, t.description, t.notes, t.date, t.time, t.location, s.sport_name,
          (SELECT COUNT(*) FROM player_activity pa WHERE pa.tryout_id = t.tryout_id) as participant_count
          FROM tryouts t
          JOIN sports s ON t.sport_id = s.sport_id
          WHERE s.user_id = '$coach_id'
          ORDER BY t.date DESC";
$result = mysqli_query($conn, $query);

$tryouts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tryouts[] = $row;
}

echo json_encode(['tryouts' => $tryouts]);
exit();
?>
