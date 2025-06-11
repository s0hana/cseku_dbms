<?php
require_once 'db.php';

$doctor_id = $_GET['doctor_id'] ?? 0;
$chamber_id = $_GET['chamber_id'] ?? 0;


$sql = "SELECT day_of_week, start_time, end_time, max_patients, consultation_fee, schedule_id
        FROM schedule_doctor 
        WHERE doctor_ID = ? AND chamber_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $doctor_id, $chamber_id);
$stmt->execute();
$result = $stmt->get_result();

$response = [];

while ($row = $result->fetch_assoc()) {
    $response[] = [
        'schedule_id_add' => $row['schedule_id'],
        'day' => $row['day_of_week'],
        'start_time' => $row['start_time'],
        'end_time' => $row['end_time'],
        'max_patients' => $row['max_patients'],
        'consultation_fee' => $row['consultation_fee']
    ];
}

echo json_encode($response);
?>
