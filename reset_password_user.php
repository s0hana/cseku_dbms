<?php
include 'db.php';

$bcn = $_GET["bcn"] ?? '';
$phone = $_GET["phone"] ?? '';

if (empty($bcn) || empty($phone)) {
    echo "<p style='color:red; text-align:center;'>Invalid or missing reset credentials.</p>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bcn = $_POST["bcn"];
    $phone = $_POST["phone"];
    $newPass = password_hash($_POST["new_password"], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("SELECT user_ID FROM systemuser WHERE birth_certificate_number = ?");
    $stmt->bind_param("s", $bcn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $userID = $result->fetch_assoc()['user_ID'];

        $stmt = $conn->prepare("SELECT 1 FROM user_phone WHERE user_ID = ? AND phone = ?");
        $stmt->bind_param("is", $userID, $phone);
        $stmt->execute();
        $phoneMatch = $stmt->get_result();

        if ($phoneMatch->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE systemuser SET user_password = ? WHERE user_ID = ?");
            $stmt->bind_param("si", $newPass, $userID);
            $stmt->execute();

            echo "<script>alert('✅ Password updated successfully!'); window.close();</script>";
        } else {
            echo "<script>alert('❌ Phone number mismatch.');</script>";
        }
    } else {
        echo "<script>alert('❌ Invalid birth certificate number.');</script>";
    }
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f0f8ff;
        }
        .container {
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Set New Password</h2>
    <form method="POST">
        <input type="hidden" name="bcn" value="<?= htmlspecialchars($bcn) ?>">
        <input type="hidden" name="phone" value="<?= htmlspecialchars($phone) ?>">
        <label>New Password:</label>
        <input type="password" name="new_password" required>
        <input type="submit" value="Update Password">
    </form>
</div>

</body>
</html>
