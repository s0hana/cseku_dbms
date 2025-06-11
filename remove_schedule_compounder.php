<?php
session_start();

// Get compounder ID from session
$compounder_ID = $_SESSION['user_ID'] ?? null;
if (!$compounder_ID) {
    die("Unauthorized access");
}

// Get the schedule details from the URL
$doctor_ID = $_GET['doctor_ID'] ?? null;
$day_of_week = $_GET['day_of_week'] ?? null;
$start_time = $_GET['start_time'] ?? null;
$end_time = $_GET['end_time'] ?? null;

if (!$doctor_ID || !$day_of_week || !$start_time || !$end_time) {
    die("Invalid schedule details.");
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "pulsescheduler");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete the specific schedule
$sql = "DELETE FROM doctor_schedule_compounder WHERE doctor_id = ? AND compounder_id = ? AND day_of_week = ? AND start_time = ? AND end_time = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $doctor_ID, $compounder_ID, $day_of_week, $start_time, $end_time);

if ($stmt->execute()) {
    echo "Schedule removed successfully.";
} else {
    echo "Error removing schedule.";
}

$stmt->close();
$conn->close();

// Redirect back to the previous page
header("Location: my_doctors.php"); // Adjust this based on your desired redirection
exit;
?>
