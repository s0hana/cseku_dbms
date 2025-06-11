<?php
// update_appointment.php
require_once 'db.php';

if (!isset($_GET['appointment_ID'])) {
    echo "<div class='error'>Appointment ID not specified.</div>";
    exit;
}

$appointment_ID = intval($_GET['appointment_ID']);

// Fetch appointment data
$stmt = $conn->prepare("SELECT * FROM appointment WHERE appointment_ID = ?");
$stmt->bind_param("i", $appointment_ID);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();

if (!$appointment) {
    echo "<div class='error'>Appointment not found.</div>";
    exit;
}

// Fetch compounders under the doctor with details
$doctor_ID = $appointment['doctor_ID'];
$query = "
    SELECT cu.user_ID, su.full_name, 
           GROUP_CONCAT(DISTINCT up.phone ORDER BY up.phone SEPARATOR ', ') AS phones,
           c.chamber_name, 
           CONCAT_WS(', ', ca.house_no, ca.road, ca.area, ca.thana, ca.district, ca.division, ca.postal_code) AS full_address
    FROM works_for wf
    JOIN compounder cu ON wf.compounder_ID = cu.user_ID
    JOIN systemuser su ON cu.user_ID = su.user_ID
    LEFT JOIN user_phone up ON cu.user_ID = up.user_ID
    LEFT JOIN chamber c ON cu.chamber_ID = c.chamber_ID
    LEFT JOIN chamber_address ca ON c.chamber_ID = ca.chamber_ID
    WHERE wf.doctor_ID = ?
    GROUP BY cu.user_ID
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_ID);
$stmt->execute();
$compounders = $stmt->get_result();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // after form submission
    $compounder_ID = isset($_POST['compounder_ID']) && $_POST['compounder_ID'] !== '' ? $_POST['compounder_ID'] : NULL;
    $serial_no = $_POST['serial_no'];
    $appointment_date = $_POST['appointment_date'];
    $start_time = $_POST['appointment_start_time'];
    $end_time = $_POST['appointment_end_time'];
    $status = $_POST['status'];
    $remark = $_POST['remark'];

    $stmt = $conn->prepare("UPDATE appointment SET compounder_ID=?, serial_no=?, appointment_date=?, appointment_start_time=?, appointment_end_time=?, status=?, remark=? WHERE appointment_ID=?");
    $stmt->bind_param("iisssssi", $compounder_ID, $serial_no, $appointment_date, $start_time, $end_time, $status, $remark, $appointment_ID);

    if ($stmt->execute()) {
        $message = "<div class='success'>Appointment updated successfully.</div>";

        if ($status === 'Completed') {
            $checkBilling = $conn->prepare("SELECT 1 FROM billing WHERE appointment_ID = ?");
            $checkBilling->bind_param("i", $appointment_ID);
            $checkBilling->execute();
            $result = $checkBilling->get_result();

            if ($result->num_rows === 0) {
                $today = date('Y-m-d');
                $insertBilling = $conn->prepare("
                    INSERT INTO billing (appointment_ID, compounder_ID, discount, payment_date, payment_status, additional_fees, remark)
                    VALUES (?, ?, 0.00, ?, 'Pending', 0.00, ?)
                ");
                $insertBilling->bind_param("isss", $appointment_ID, $compounder_ID, $today, $remark);
                if ($insertBilling->execute()) {
                    $message .= "<div class='info'>Billing record created.</div>";
                } else {
                    $message .= "<div class='warning'>Appointment updated, but billing record creation failed.</div>";
                }
            }
        }
    } else {
        $message = "<div class='error'>Failed to update appointment.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Appointment</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background: linear-gradient(90deg, #007BFF, #00C6FF);
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 15px;
        }

        .logo h1 {
            font-size: 1.5rem;
            margin: 0;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 1em;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #FFC107;
        }

        h2 {
            text-align: center;
            margin: 30px 0 10px;
            color: #0077cc;
        }

        form {
            background-color: #fff;
            max-width: 900px;
            margin: 0 auto 40px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="number"],
        select,
        
textarea {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    box-sizing: border-box;
    width: 80%; /* Ei line ta add korlam */
}

        textarea {
            resize: vertical;
        }

        .form-group.full-width {
            grid-column: span 2;
            display: flex;
            justify-content: center;
        }

        input[type="submit"] {
            background-color: #0077cc;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 50%;
            margin: 0 auto;
            display: block;
        }

        input[type="submit"]:hover {
            background-color: #005fa3;
        }

        .success, .info, .error, .warning {
            max-width: 900px;
            margin: 20px auto;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <div class="logo">
            <h1>PulseScheduler</h1>
        </div>
        <ul class="nav-links">
            <li><a href="manage_billing.php">See Billing</a></li>
            <li><a href="manage_appointment.php">Appointments</a></li>
            <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<h2>Update Appointment</h2>
<?= $message ?>

<form method="post">
    <div class="form-group">
        <label for="serial_no">Serial No</label>
        <input type="number" name="serial_no" value="<?= htmlspecialchars($appointment['serial_no']) ?>" required>
    </div>

    <div class="form-group">
        <label for="appointment_date">Appointment Date</label>
        <input type="date" name="appointment_date" value="<?= htmlspecialchars($appointment['appointment_date']) ?>" required>
    </div>

    <div class="form-group">
        <label for="appointment_start_time">Start Time</label>
        <input type="time" name="appointment_start_time" value="<?= htmlspecialchars($appointment['appointment_start_time']) ?>" required>
    </div>

    <div class="form-group">
        <label for="appointment_end_time">End Time</label>
        <input type="time" name="appointment_end_time" value="<?= htmlspecialchars($appointment['appointment_end_time']) ?>" required>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select name="status">
            <option value="Scheduled" <?= $appointment['status'] === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
            <option value="Completed" <?= $appointment['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
            <option value="Cancelled" <?= $appointment['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
    </div>

    <div class="form-group">
        <label for="compounder_ID">Compounder</label>
        <select name="compounder_ID">
            <option value="">-- Select Compounder --</option>
            <?php while ($c = $compounders->fetch_assoc()): ?>
                <option value="<?= $c['user_ID'] ?>" <?= $c['user_ID'] == $appointment['compounder_ID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['full_name']) ?> (<?= $c['phones'] ?>) - <?= htmlspecialchars($c['chamber_name']??'N/A') ?>, <?= htmlspecialchars($c['full_address']??'N/A') ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group half-width">
        <label for="remark">Remark</label>
        <textarea name="remark" rows="3"><?= htmlspecialchars($appointment['remark'] ?? "N/A") ?></textarea>
    </div>

    <div class="form-group full-width">
        <input type="submit" value="Update Appointment">
    </div>
</form>

<footer>
    <p>&copy; <?= date("Y") ?> PulseScheduler. All rights reserved.</p>
</footer>

</body>
</html>