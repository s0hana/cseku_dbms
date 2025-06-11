<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

$user_ID = $_SESSION['user_ID'];
$phone_ID = intval($_GET['id']);

// Check ownership
$check = mysqli_query($conn, "SELECT * FROM user_phone WHERE phone_ID = $phone_ID AND user_ID = '$user_ID'");
if (mysqli_num_rows($check) == 1) {
    mysqli_query($conn, "DELETE FROM user_phone WHERE phone_ID = $phone_ID");
}

header("Location: general.php"); // or wherever your profile page is
exit();
?>
