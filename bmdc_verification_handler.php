<?php
$pdo = new PDO("mysql:host=localhost;dbname=pulsescheduler", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_ID = $_POST['user_ID'];
$bmdc = $_POST['bmdc_number'];

// 1. Check if this BMDC number already exists
$check = $pdo->prepare("SELECT COUNT(*) FROM doctor_bmdc_verification WHERE bmdc_number = ?");
$check->execute([$bmdc]);
$exists = $check->fetchColumn();

if ($exists > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "❌ This BMDC number has already been used."
    ]);
    exit;
}

// 2. Insert if not exists
$stmt = $pdo->prepare("INSERT INTO doctor_bmdc_verification (user_ID, bmdc_number, status) VALUES (?, ?, 'pending')");
$stmt->execute([$user_ID, $bmdc]);

echo json_encode(["status" => "success", "message" => "✅ BMDC number has been submitted."]);
?>
