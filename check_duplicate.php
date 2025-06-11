<?php
$conn = new mysqli("localhost", "root", "", "pulsescheduler");

if (isset($_GET['type']) && isset($_GET['value'])) {
    $type = $_GET['type'];
    $value = $_GET['value'];

    if ($type === 'email') {
        $stmt = $conn->prepare("SELECT * FROM chamber_email WHERE email = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        echo $result->num_rows > 0 ? "❌ Email already exists" : "✅ Email available";
    }

    if ($type === 'phone') {
        $stmt = $conn->prepare("SELECT * FROM chamber_phone WHERE phone = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        echo $result->num_rows > 0 ? "❌ Phone already exists" : "✅ Phone available";
    }
}
?>
