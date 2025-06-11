<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['schedule_id'])) {
    $schedule_id = intval($_POST['schedule_id']);
    
    $mysqli = new mysqli("localhost", "root", "", "pulsescheduler");
    if ($mysqli->connect_error) {
        die("Database error.");
    }

    $stmt = $mysqli->prepare("DELETE FROM schedule_doctor WHERE schedule_id = ?");
    $stmt->bind_param("i", $schedule_id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
