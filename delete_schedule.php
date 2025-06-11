<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_ID'])) {
    header("Location: manage_doctor_profile.php?msg=" . urlencode("Access denied. Please log in."));
    exit;
}

$doctorID = $_SESSION['user_ID'];
$chamberID = $_POST['chamber_id'] ?? null;

if (!$chamberID) {
    header("Location: manage_doctor_profile.php?msg=" . urlencode("No chamber specified."));
    exit;
}

$stmt = $conn->prepare("DELETE FROM works_in WHERE doctor_ID = ? AND chamber_ID = ?");
$stmt->bind_param("ii", $doctorID, $chamberID);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $msg = "Schedule deleted successfully."; 
} else {
    $msg = "Failed to delete the schedule. Please try again.";
}

$stmt->close();
$conn->close();

// Redirect back with a message
header("Location: manage_doctor_profile.php?msg=" . urlencode($msg));
exit;
?>
