<?php

require '../connection/conn.php';

if (isset($_POST['signup_player'])) {
    $student_id = trim($_POST['student_id']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $given_name = trim($_POST['given_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $suffix = trim($_POST['suffix']);
    $sex = trim($_POST['sex']);
    $contact_number = trim($_POST['contact_number']);
    $social_media = trim($_POST['social_media']);
    $dob = trim($_POST['dob']);
    $year_level = trim($_POST['year_level']);
    $institute_campus = trim($_POST['institute_campus']);

    $data = "INSERT INTO users (role_id, student_id, username, password, given_name, middle_name, last_name, suffix, sex, contact_number, social_media, dob, year_level, institute_campus
    VALUES ('1','$student_id', '$username','$password','$given_name','$middle_name','$last_name','$suffix','$sex','$contact_number','$social_media','$dob','$year_level','$institute_campus') ";
    $result = mysqli_query($conn, $data);

    if ($result){
        header('Location: ../../../index.php');
    } else{
        echo "Error: " . $e->getMessage();
    }
}
?>

<?php

if (isset($_POST['signup_coach'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $given_name = trim($_POST['given_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $suffix = trim($_POST['suffix']);
    $sex = trim($_POST['sex']);
    $dob = trim($_POST['dob']);
    $year_level = trim($_POST['year_level']);
    $institute_campus = trim($_POST['institute_campus']);

    $data = "INSERT INTO users (role_id, username, password, given_name, middle_name, last_name, suffix, sex, dob, institute_campus
    VALUES ('2', '$username','$password','$given_name','$middle_name','$last_name','$suffix','$sex','$dob','$institute_campus') ";
    $result = mysqli_query($conn, $data);

    if ($result){
        header('Location: ../../../index.php');
    } else{
        echo "Error: " . $e->getMessage();
    }
}

?>