<?php
// update_billing.php
require_once 'db.php';

if (!isset($_GET['appointment_ID'])) {
    echo "<p style='color:red;'>Appointment ID not specified.</p>";
    exit;
}

$appointment_ID = intval($_GET['appointment_ID']);

// Fetch billing data
$stmt = $conn->prepare("SELECT * FROM billing WHERE appointment_ID = ?");
$stmt->bind_param("i", $appointment_ID);
$stmt->execute();
$result = $stmt->get_result();
$billing = $result->fetch_assoc();

if (!$billing) {
    echo "<p style='color:red;'>Billing record not found.</p>";
    exit;
}

// Get doctor_ID from appointment
$stmt = $conn->prepare("SELECT doctor_ID FROM appointment WHERE appointment_ID = ?");
$stmt->bind_param("i", $appointment_ID);
$stmt->execute();
$doctor_result = $stmt->get_result();
$doctor_row = $doctor_result->fetch_assoc();

if (!$doctor_row) {
    echo "<p style='color:red;'>Doctor not found for this appointment.</p>";
    exit;
}

$doctor_ID = $doctor_row['doctor_ID'];

// Fetch compounders under the doctor with phones, chamber name, and address
$query = "
    SELECT cu.user_ID, su.full_name,
           GROUP_CONCAT(DISTINCT up.phone ORDER BY up.phone SEPARATOR ', ') AS phones,
           c.chamber_name,
           CONCAT_WS(', ', a.house_no, a.road, a.area, a.thana, a.district, a.division, a.postal_code) AS full_address
    FROM works_for wf
    JOIN compounder cu ON wf.compounder_ID = cu.user_ID
    JOIN systemuser su ON cu.user_ID = su.user_ID
    LEFT JOIN user_phone up ON cu.user_ID = up.user_ID
    LEFT JOIN chamber c ON cu.chamber_ID = c.chamber_ID
    LEFT JOIN chamber_address a ON c.chamber_ID = a.chamber_ID
    WHERE wf.doctor_ID = ?
    GROUP BY cu.user_ID
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_ID);
$stmt->execute();
$compounders = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $compounder_ID = $_POST['compounder_ID'] ?: null;
    $discount = $_POST['discount'];
    $payment_date = $_POST['payment_date'];
    $payment_status = $_POST['payment_status'];
    $additional_fees = $_POST['additional_fees'];
    $remark = $_POST['remark'];

    $stmt = $conn->prepare("UPDATE billing SET compounder_ID = ?, discount = ?, payment_date = ?, payment_status = ?, additional_fees = ?, remark = ? WHERE appointment_ID = ?");
    $stmt->bind_param("idssssi", $compounder_ID, $discount, $payment_date, $payment_status, $additional_fees, $remark, $appointment_ID);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Billing information updated successfully.</p>";
    } else {
        echo "<p style='color:red;'>Failed to update billing information.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Billing</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content {
            max-width: 1300px;
            margin: 40px auto;
            padding: 0 30px;
            flex: 1;
            text-align: center;
        }
        
        header {
            background: linear-gradient(90deg, #007BFF, #00C6FF);
            color: white;
            padding: 10px 20px;
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
            font-size: 1.8rem;
            margin: 0;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 24px;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.05em;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #FFC107;
        }

        h2 {
            color: #007bff;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }

        form {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 10px;
            width: 600px;
            max-width: 100%;
            margin: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #34495e;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            grid-column: span 2; /* Makes the submit button span both columns */
        }

        input[type="submit"]:hover {
            background-color: #007bff;
        }

        textarea {
            grid-column: span 2;
        }

        /* Ensuring proper spacing between form fields */
        .half-width {
            grid-column: span 1;
        }

        .full-width {
            grid-column: span 2;
        }
        footer {
    background-color: #007bff; /* Blue color */
    color: white; /* Light text color */
    text-align: center;
    padding: 1.5rem;
    margin-top: 2rem;
    font-size: 0.9rem;
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
        <li><a href="manage_appointment.php">Manage Appointments</a></li>
        <li><a href="manage_billing.php">See Billing</a></li>
        <li><a href="manage_doctor_profile.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="content">
    <h2>Update Billing Information</h2>

    <form method="post">
        <div class="half-width">
            <label for="compounder_ID">Compounder</label>
            <select name="compounder_ID">
                <option value="">-- Select Compounder --</option>
                <?php while ($c = $compounders->fetch_assoc()): ?>
                    <option value="<?= $c['user_ID'] ?>" <?= $billing['compounder_ID'] == $c['user_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['phones']) ?>)
                        - <?= htmlspecialchars($c['chamber_name']??"N/A") ?>, <?= htmlspecialchars($c['full_address']??"N/A") ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="discount">Discount (৳)</label>
            <input type="number" step="0.01" name="discount" value="<?= htmlspecialchars($billing['discount']) ?>" required>

            <label for="payment_status">Payment Status</label>
            <select name="payment_status" required>
                <option value="Paid" <?= $billing['payment_status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                <option value="Free" <?= $billing['payment_status'] === 'Free' ? 'selected' : '' ?>>Free</option>
                <option value="Pending" <?= $billing['payment_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            </select>

        </div>

        <div class="half-width">
            <label for="payment_date">Payment Date</label>
            <input type="date" name="payment_date" value="<?= htmlspecialchars($billing['payment_date']) ?>">

            <label for="additional_fees">Additional Fees (৳)</label>
            <input type="number" step="0.01" name="additional_fees" value="<?= htmlspecialchars($billing['additional_fees']) ?>">

            <label for="remark">Remark</label>
            <textarea name="remark" rows="4"><?= htmlspecialchars($billing['remark']) ?></textarea>
        </div>

        <input type="submit" value="Update Billing">
    </form>
</div>
<footer>
    <p>&copy; <?php echo date('Y'); ?> PulseScheduler. All rights reserved.</p>
</footer>
</body>
</html>
