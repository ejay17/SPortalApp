<?php

require '../connection/conn.php';

if (isset($_POST['signup_player'])) {
    $student_id = trim($_POST['studentid']);
    $username = trim($_POST['username_player']);
    $password = trim($_POST['password_player']);
    $given_name = trim($_POST['givenname_player']);
    $middle_name = trim($_POST['middlename_player']);
    $last_name = trim($_POST['lastname_player']);
    $suffix = trim($_POST['suffix_player']);
    $sex = trim($_POST['sex_player']);
    $contact_number = trim($_POST['contactnumber']);
    $social_media = trim($_POST['socialmedialink']);
    $dob = trim($_POST['dob_player']);
    $year_level = trim($_POST['yearlvl']);
    $institute_campus = trim($_POST['inscam_player']);
    $sports = $_POST['sports'] ?? [];

    // Check if username already exists
    $check = "SELECT username FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check);

    if (mysqli_num_rows($check_result) > 0) {
        session_start();
        $_SESSION['error'] = "Username already exists. Please choose a different one.";
        header('Location: ../../../pages/signup.php');
        exit();
    }

    // Check if student ID already exists
    $check_sid = "SELECT student_id FROM users WHERE student_id = '$student_id'";
    $check_sid_result = mysqli_query($conn, $check_sid);

    if (mysqli_num_rows($check_sid_result) > 0) {
        session_start();
        $_SESSION['error'] = "Student ID already registered.";
        header('Location: ../../../pages/signup.php');
        exit();
    }

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $data = "INSERT INTO users (role_id, student_id, username, password, given_name, middle_name, last_name, suffix, sex, contact_number, social_media, dob, year_level, institute_campus)
    VALUES ('1','$student_id', '$username','$password_hashed','$given_name','$middle_name','$last_name','$suffix','$sex','$contact_number','$social_media','$dob','$year_level','$institute_campus') ";
    $result = mysqli_query($conn, $data);

    if ($result) {
        $user_id = mysqli_insert_id($conn);

        foreach ($sports as $sport_name) {
            $sport_name = trim($sport_name);
            $sport_query = "INSERT INTO sports (user_id, sport_name)
            VALUES ('$user_id', '$sport_name')";
            mysqli_query($conn, $sport_query);
        }

        session_start();
        $_SESSION['success'] = "Account created successfully! Please log in.";
        header('Location: ../../../index.php');
    } else {
        session_start();
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
        header('Location: ../../../pages/signup.php');
    }
}

?>

<?php

if (isset($_POST['signup_coach'])) {
    $username = trim($_POST['username_coach']);
    $password = trim($_POST['password_coach']);
    $given_name = trim($_POST['givenname_coach']);
    $middle_name = trim($_POST['middlename_coach']);
    $last_name = trim($_POST['lastname_coach']);
    $suffix = trim($_POST['suffix_coach']);
    $sex = trim($_POST['sex_coach']);
    $dob = trim($_POST['dob_coach']);
    $institute_campus = trim($_POST['inscam_coach']);
    $sport_name = trim($_POST['sport']);

    // Check if username already exists
    $check = "SELECT username FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check);

    if (mysqli_num_rows($check_result) > 0) {
        session_start();
        $_SESSION['error'] = "Username already exists. Please choose a different one.";
        header('Location: ../../../pages/signup.php');
        exit();
    }

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $data = "INSERT INTO users (role_id, username, password, given_name, middle_name, last_name, suffix, sex, dob, institute_campus)
    VALUES ('2', '$username','$password_hashed','$given_name','$middle_name','$last_name','$suffix','$sex','$dob','$institute_campus') ";
    $result = mysqli_query($conn, $data);

    if ($result) {
        $user_id = mysqli_insert_id($conn);

        $sport_query = "INSERT INTO sports (user_id, sport_name)
            VALUES ('$user_id', '$sport_name')";
        mysqli_query($conn, $sport_query);

        session_start();
        $_SESSION['success'] = "Account created successfully! Please log in.";
        header('Location: ../../../index.php');
    } else {
        session_start();
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
        header('Location: ../../../pages/signup.php');
    }
}

?>
