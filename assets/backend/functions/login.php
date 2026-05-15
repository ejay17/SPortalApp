<?php
session_start();

require '../connection/conn.php';

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $data = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $data);

    if (mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {

            $role = $row['role_id'];

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role_id'] = $role;
            $_SESSION['username'] = $row['username'];

            if ($role == '1') {
                header("Location: ../../../pages/player_dashboard.php");
                exit();

            } elseif ($role == '2') {
                header("Location: ../../../pages/coach_dashboard.php");
                exit();

            } else {
                $_SESSION['error'] = "Invalid role.";
                header("Location: ../../../index.php");
                exit();
            }

        } else {
            $_SESSION['error'] = "Incorrect password.";
            header("Location: ../../../index.php");
            exit();
        }

    } else {
        $_SESSION['error'] = "No user found with that username.";
        header("Location: ../../../index.php");
        exit();
    }

}
?>
