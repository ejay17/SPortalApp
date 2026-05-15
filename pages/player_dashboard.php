<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Dashboard</title>
</head>

<body>
    <?php
    session_start();

    require '../assets/backend/connection/conn.php';

    $user_id = $_SESSION['user_id'];

    $data = "SELECT username FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $data);

    if (mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);
    ?>

        <h3>Welcome back</h3>
        <h1><?php echo $row['username']; ?></h1>

    <?php
    }
    ?>
</body>

</html>