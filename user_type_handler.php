<?php
$pdo = new PDO("mysql:host=localhost;dbname=pulsescheduler", "root", "");

$user_ID = $_POST['user_ID'];
$type = $_POST['type'];

if ($type === "doctor") {
    $pdo->prepare("INSERT INTO doctor (user_ID) VALUES (?)")->execute([$user_ID]);
} elseif ($type === "compounder") {
    $pdo->prepare("INSERT INTO compounder (user_ID) VALUES (?)")->execute([$user_ID]);
}
// general user - do nothing

echo "OK";
?>
