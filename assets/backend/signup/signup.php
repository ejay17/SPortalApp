<?php

require '../connection/conn.php';

if (isset($_POST['signup_player'])) {
    $student_id = trim($_POST['studentid']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $given_name = trim($_POST['givenname']);
    $middle_name = trim($_POST['middlename']);
    $last_name = trim($_POST['lastname']);
    $suffix = trim($_POST['suffix']);
    $sex = trim($_POST['sex']);
    $contact_number = trim($_POST['contactnumber']);
    $social_media = trim($_POST['socialmedialink']);
    $dob = trim($_POST['dob']);
    $year_level = trim($_POST['yearlvl']);
    $institute_campus = trim($_POST['inscam']);
    $sports = $_POST['sports'] ?? [];

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $data = "INSERT INTO users (role_id, student_id, username, password, given_name, middle_name, last_name, suffix, sex, contact_number, social_media, dob, year_level, institute_campus)
    VALUES ('1','$student_id', '$username','$password_hashed','$given_name','$middle_name','$last_name','$suffix','$sex','$contact_number','$social_media','$dob','$year_level','$institute_campus') ";
    $result = mysqli_query($conn, $data);

    if ($result) {
        $user_id = mysqli_insert_id($conn);

        foreach ($sports as $sport_name) {

            $sport_query = "INSERT INTO sports (user_id, sport_name)
            VALUES ('$user_id', '$sport_name')";

            mysqli_query($conn, $sport_query);
        }
        header('Location: ../../../index.php');
    } else {
        header('Location: ../../../pages/signup.php');
    }
}
?>

<?php

if (isset($_POST['signup_coach'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $given_name = trim($_POST['givenname']);
    $middle_name = trim($_POST['middlename']);
    $last_name = trim($_POST['lastname']);
    $suffix = trim($_POST['suffix']);
    $sex = trim($_POST['sex']);
    $dob = trim($_POST['dob']);
    $institute_campus = trim($_POST['inscam']);

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $data = "INSERT INTO users (role_id, username, password, given_name, middle_name, last_name, suffix, sex, dob, institute_campus)
    VALUES ('2', '$username','$password_hashed','$given_name','$middle_name','$last_name','$suffix','$sex','$dob','$institute_campus') ";
    $result = mysqli_query($conn, $data);

    if ($result) {
        header('Location: ../../../index.php');
    } else {
        header('Location: ../../../pages/signup.php');
    }
}

?>