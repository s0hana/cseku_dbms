<?php
$pdo = new PDO("mysql:host=localhost;dbname=pulsescheduler", "root", "");

$full_name = $_POST['full_name'];
$birth_certificate = $_POST['birth_certificate'];
$phone = $_POST['phone'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$email = isset($_POST['email']) ? $_POST['email'] : null;

// Check for unique username and birth certificate
$stmt = $pdo->prepare("SELECT * FROM systemuser WHERE user_name = ? OR birth_certificate_number = ?");
$stmt->execute([$username, $birth_certificate]);

if ($stmt->rowCount() > 0) {
    echo json_encode(["status" => "error", "message" => "Username or Birth Certificate already exists."]);
    exit;
}

// Insert into systemuser
$stmt = $pdo->prepare("INSERT INTO systemuser (user_name, user_password, full_name, birth_certificate_number) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $password, $full_name, $birth_certificate]);
$user_ID = $pdo->lastInsertId();

// Insert phone
$stmt = $pdo->prepare("INSERT INTO user_phone (user_ID, phone) VALUES (?, ?)");
$stmt->execute([$user_ID, $phone]);

//insert email
$stmt = $pdo->prepare("INSERT INTO user_email (user_ID, email) VALUES (?, ?)");
$stmt->execute([$user_ID, $email]);

echo json_encode(["status" => "success", "user_ID" => $user_ID]);
?>
