<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

$user_ID = $_SESSION['user_ID'];
$email_ID = intval($_GET['id']);

$check = mysqli_query($conn, "SELECT * FROM user_email WHERE email_ID = $email_ID AND user_ID = '$user_ID'");
if (mysqli_num_rows($check) == 1) {
    mysqli_query($conn, "DELETE FROM user_email WHERE email_ID = $email_ID");
}

header("Location: general.php");
exit();
?>
