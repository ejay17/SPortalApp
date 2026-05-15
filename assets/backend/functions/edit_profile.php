<?php
session_start();
require '../connection/conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['edit_profile'])) {
    $given_name = trim($_POST['given_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $suffix = trim($_POST['suffix']);
    $contact_number = trim($_POST['contact_number']);
    $social_media = trim($_POST['social_media']);
    $institute_campus = trim($_POST['institute_campus']);

    $role_id = $_SESSION['role_id'];

    if ($role_id == '1') {
        // Player-specific fields
        $year_level = trim($_POST['year_level']);
        $student_id = trim($_POST['student_id']);

        $query = "UPDATE users SET 
            given_name = '$given_name',
            middle_name = '$middle_name',
            last_name = '$last_name',
            suffix = '$suffix',
            contact_number = '$contact_number',
            social_media = '$social_media',
            institute_campus = '$institute_campus',
            year_level = '$year_level',
            student_id = '$student_id'
            WHERE user_id = '$user_id'";
    } else {
        $query = "UPDATE users SET 
            given_name = '$given_name',
            middle_name = '$middle_name',
            last_name = '$last_name',
            suffix = '$suffix',
            contact_number = '$contact_number',
            social_media = '$social_media',
            institute_campus = '$institute_campus'
            WHERE user_id = '$user_id'";
    }

    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating profile: " . mysqli_error($conn);
    }

    // Redirect back to dashboard
    if ($role_id == '1') {
        header("Location: ../../../pages/player_dashboard.php?page=profile");
    } else {
        header("Location: ../../../pages/coach_dashboard.php?page=profile");
    }
    exit();
}

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Get current password hash
    $query = "SELECT password FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if (!password_verify($current_password, $row['password'])) {
        $_SESSION['error'] = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
    } else {
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = "UPDATE users SET password = '$new_hashed' WHERE user_id = '$user_id'";
        mysqli_query($conn, $update);
        $_SESSION['success'] = "Password changed successfully!";
    }

    $role_id = $_SESSION['role_id'];
    if ($role_id == '1') {
        header("Location: ../../../pages/player_dashboard.php?page=profile");
    } else {
        header("Location: ../../../pages/coach_dashboard.php?page=profile");
    }
    exit();
}

// If no valid action, redirect back
$role_id = $_SESSION['role_id'] ?? '1';
if ($role_id == '1') {
    header("Location: ../../../pages/player_dashboard.php");
} else {
    header("Location: ../../../pages/coach_dashboard.php");
}
exit();
?>
