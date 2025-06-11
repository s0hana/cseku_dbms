<?php
session_start();
include 'db_connection.php';

$doctor_id = $_SESSION['doctor_id']; // Ensure doctor is logged in

function sanitize($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value));
}

if (isset($_POST['create_new'])) {
    // Create a new chamber
    $name = sanitize($conn, $_POST['new_name']);
    $address = sanitize($conn, $_POST['new_address']);
    $open_time = $_POST['open_time'];
    $close_time = $_POST['close_time'];
    $working_days = isset($_POST['working_days']) ? implode(',', $_POST['working_days']) : '';
    $phones = explode(',', sanitize($conn, $_POST['phones']));
    $emails = explode(',', sanitize($conn, $_POST['emails']));

    // Insert chamber
    $query = "INSERT INTO chambers (name, address, open_time, close_time, working_days) 
              VALUES ('$name', '$address', '$open_time', '$close_time', '$working_days')";
    if (mysqli_query($conn, $query)) {
        $chamber_id = mysqli_insert_id($conn);

        // Insert phones
        foreach ($phones as $phone) {
            $phone = sanitize($conn, $phone);
            if (!empty($phone)) {
                mysqli_query($conn, "INSERT INTO chamber_phones (chamber_id, phone) VALUES ($chamber_id, '$phone')");
            }
        }

        // Insert emails
        foreach ($emails as $email) {
            $email = sanitize($conn, $email);
            if (!empty($email)) {
                mysqli_query($conn, "INSERT INTO chamber_emails (chamber_id, email) VALUES ($chamber_id, '$email')");
            }
        }

        // Insert schedule
        $day = sanitize($conn, $_POST['new_day']);
        $start_time = $_POST['new_start_time'];
        $end_time = $_POST['new_end_time'];
        $max_patients = (int)$_POST['new_max_patients'];
        $fee = (float)$_POST['new_consultation_fee'];

        $schedule_query = "INSERT INTO works_in (doctor_id, chamber_id, day, start_time, end_time, max_patients, consultation_fee)
                           VALUES ($doctor_id, $chamber_id, '$day', '$start_time', '$end_time', $max_patients, $fee)";
        mysqli_query($conn, $schedule_query);

        header("Location: manage_doctor_profile.php?success=new_chamber_added");
        exit();
    } else {
        echo "Error creating new chamber: " . mysqli_error($conn);
    }

} else {
    // Add schedule to an existing chamber
    $chamber_id = (int)$_POST['chamber_id'];
    $day = sanitize($conn, $_POST['day']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $max_patients = (int)$_POST['max_patients'];
    $fee = (float)$_POST['consultation_fee'];

    $query = "INSERT INTO works_in (doctor_id, chamber_id, day, start_time, end_time, max_patients, consultation_fee)
              VALUES ($doctor_id, $chamber_id, '$day', '$start_time', '$end_time', $max_patients, $fee)";
    if (mysqli_query($conn, $query)) {
        header("Location: manage_doctor_profile.php?success=schedule_added");
        exit();
    } else {
        echo "Error adding schedule: " . mysqli_error($conn);
    }
}

?>
