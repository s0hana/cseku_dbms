<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

function errorResponse($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

if (!isset($_SESSION['user_ID'])) {
    errorResponse('User not logged in.');
}


if (!isset($_POST['doctor_id'], $_POST['chamber_id'], $_POST['appointment_date'], $_POST['slot'])) {
    errorResponse('Incomplete form data.');
}

$user_ID = $_SESSION['user_ID'];
$doctor_ID = $_POST['doctor_id'];
$chamber_ID = $_POST['chamber_id'];
$date = $_POST['appointment_date'];
$schedule = json_decode($_POST['slot'], true);

// Check if JSON decoding failed
if (!$schedule || !isset($schedule['day'], $schedule['schedule_id_add'], $schedule['start_time'], $schedule['end_time'], $schedule['max_patients'], $schedule['consultation_fee'])) {
    errorResponse("Invalid schedule data.");
}

// Check if the selected date matches the available schedule day
$day = date('l', strtotime($date));
if ($day !== $schedule['day']) {
    errorResponse("❌ The doctor is not available at this chamber on that day!");
}

// Check if doctor is unavailable on that date
$sql = "SELECT * FROM unavailability 
        WHERE doctor_id = ? AND ? BETWEEN start_date AND end_date";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    errorResponse("DB prepare error (unavailability): " . $conn->error);
}
$stmt->bind_param("is", $doctor_ID, $date);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    errorResponse("❌ The doctor is unavailable on the selected date. Reason: ". $row['reason']);
}


// Check number of current bookings
$sql = "SELECT COUNT(*) as booked FROM appointment 
        WHERE doctor_ID = ? AND chamber_ID = ? AND appointment_date = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    errorResponse("DB prepare error: " . $conn->error);
}
$stmt->bind_param("iis", $doctor_ID, $chamber_ID, $date);
$stmt->execute();
$result = $stmt->get_result();
$booked = $result->fetch_assoc()['booked'];

if ($booked >= $schedule['max_patients']) {
    errorResponse("❌ All appointment slots are full for that day.");
}

$serial = $booked + 1;

//calculate

$sql = "SELECT max_consultation_duration FROM doctor WHERE user_ID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    errorResponse("DB prepare error (duration): " . $conn->error);
}
$stmt->bind_param("i", $doctor_ID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    errorResponse("❌ Doctor not found.");
}
$duration_minutes = (int)$result->fetch_assoc()['max_consultation_duration'];

// Calculate start and end time for this serial
$start_time = DateTime::createFromFormat('H:i:s', $schedule['start_time']);
if (!$start_time) {
    errorResponse("❌ Invalid time format in schedule start_time.");
}
$start_offset = ($serial - 1) * $duration_minutes;
$end_offset = $serial * $duration_minutes;

$appointment_start = clone $start_time;
$appointment_start->modify("+$start_offset minutes");
$appointment_end = clone $start_time;
$appointment_end->modify("+$end_offset minutes");

$appointment_start_str = $appointment_start->format('H:i:s');
$appointment_end_str = $appointment_end->format('H:i:s');

// Insert appointment
$sql = "INSERT INTO appointment (user_ID, doctor_ID, chamber_ID, serial_no, appointment_date, appointment_start_time, appointment_end_time, status, sch_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Scheduled', ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    errorResponse("DB prepare error (insert): " . $conn->error);
}
$stmt->bind_param("iiiisssi", $user_ID, $doctor_ID, $chamber_ID, $serial, $date, $appointment_start_str, $appointment_end_str, $schedule['schedule_id_add']);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'serial_no' => $serial]);
} else {
    errorResponse("❌ Failed to save the appointment to the database: " . $stmt->error);
}
?>
