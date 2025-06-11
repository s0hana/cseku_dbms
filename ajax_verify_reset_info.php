<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bcn = $_POST["bcn"];
    $phone = $_POST["phone"];

    $conn->begin_transaction();

    try {
        // Step 1: BCN match check
        $stmt = $conn->prepare("SELECT user_ID FROM systemuser WHERE birth_certificate_number = ?");
        $stmt->bind_param("s", $bcn);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo "No account found with this birth certificate number.";
            exit;
        }

        $row = $result->fetch_assoc();
        $userID = $row["user_ID"];

        // Step 2: Check if phone is linked with that user
        $stmt = $conn->prepare("SELECT * FROM user_phone WHERE user_ID = ? AND phone = ?");
        $stmt->bind_param("is", $userID, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo "Phone number does not match with this account.";
            exit;
        }

        // Step 3: Check if there's already a reset request with status = 'sent'
        $stmt = $conn->prepare("SELECT * FROM password_reset WHERE birth_certificate_number = ? AND phone = ? AND status = 'Sent'");
        $stmt->bind_param("ss", $bcn, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "already_sent";
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM password_reset WHERE birth_certificate_number = ? AND phone = ? AND status = 'Pending'");
        $stmt->bind_param("ss", $bcn, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Please Wait For Admin's Response!";
            exit;
        }

        // Step 4: Insert into password_reset table
        $stmt = $conn->prepare("INSERT INTO password_reset (birth_certificate_number, phone, matched_user_ID, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("ssi", $bcn, $phone, $userID);
        $stmt->execute();

        $conn->commit();
        echo "matched";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Something went wrong. Please try again.";
    }

    $conn->close();
}
?>
