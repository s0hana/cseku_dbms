<?php
session_start();
include 'db.php';
if (!isset($_SESSION["user_ID"])) {
    die("Access denied. Please log in.");
}

$doctor_ID = $_SESSION["user_ID"];

$query = "SELECT 
    a.appointment_ID,
    a.appointment_date,
    a.serial_no,

    -- Billing
    b.additional_fees,
    b.remark,
    b.discount,
    b.payment_status,
    sd.consultation_fee,
    ROUND((sd.consultation_fee + b.additional_fees) * (1 - b.discount / 100), 2) AS final_amount,

    -- Patient
    su_p.full_name AS patient_name,
    su_p.photo AS patient_photo,
    GROUP_CONCAT(DISTINCT pp.phone SEPARATOR ', ') AS patient_phones,

    -- Compounder
    su_c.full_name AS compounder_name,
    su_c.photo AS compounder_photo,
    GROUP_CONCAT(DISTINCT cp.phone SEPARATOR ', ') AS compounder_phones,

    -- Chamber
    ch.chamber_name AS chamber_name,
    CONCAT_WS(', ', ca.house_no, ca.road, ca.area, ca.thana, ca.district, ca.division, ca.postal_code) AS full_address,
    GROUP_CONCAT(DISTINCT chp.phone SEPARATOR ', ') AS chamber_phones

FROM appointment a

JOIN billing b ON a.appointment_ID = b.appointment_ID
JOIN works_in w ON a.doctor_ID = w.doctor_ID AND a.chamber_ID = w.chamber_ID

-- Schedule Doctor
JOIN schedule_doctor sd ON a.sch_id = sd.schedule_id  -- Fixed the join condition to use sch_id

-- Patient
JOIN systemuser su_p ON a.user_ID = su_p.user_ID
LEFT JOIN user_phone pp ON su_p.user_ID = pp.user_ID

-- Compounder
LEFT JOIN systemuser su_c ON b.compounder_ID = su_c.user_ID
LEFT JOIN user_phone cp ON su_c.user_ID = cp.user_ID

-- Chamber
JOIN chamber ch ON a.chamber_ID = ch.chamber_ID
LEFT JOIN chamber_phone chp ON ch.chamber_ID = chp.chamber_ID
LEFT JOIN chamber_address ca ON ch.chamber_ID = ca.chamber_ID

WHERE a.doctor_ID = ?
  AND a.status = 'Completed'

GROUP BY a.appointment_ID
ORDER BY 
    CASE b.payment_status
        WHEN 'Pending' THEN 0
        ELSE 1
    END,
    a.appointment_date DESC,
    a.serial_no ASC;
";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_ID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Appointments - Billing</title>
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

        .content {
            max-width: 1300px;
            margin: 40px auto;
            padding: 0 30px;
            flex: 1;
        }

        h2 {
            color: #007bff;
            margin-bottom: 25px;
            font-size: 1.8rem;
            text-align: center;
        }

        table {
    width: 95%;
    max-width: 100%;
    margin: 0 auto 30px auto; /*center table horizontally */
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    table-layout: fixed;
}


        th, td {
            padding: 14px 16px;
            border: 1px solid #ccc;
            vertical-align: top;
            text-align: left;
            word-wrap: break-word;
        }

        th {
            background-color:rgb(12, 133, 254);
            color: white;
        }

        img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 5px;
        }

        .update-btn {
            padding: 6px 12px;
            background-color:rgb(7, 88, 218);
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 6px;
        }

        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 25px 0;
            margin-top: auto;
            font-size: 0.95em;
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
            <li><a href="chamber_list_doctor.php">Chambers</a></li>
            <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="content">
    <h2>Completed Appointments - Billing</h2>

    <div style="text-align: center; margin-bottom: 15px;">
    <input type="text" id="searchBox" placeholder="Search by patient or chamber..." 
           style="padding: 8px; width: 300px; border-radius: 6px; border: 1px solid #ccc;">
</div>


    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Serial</th>
            <th>Patient</th>
            <th>Compounder</th>
            <th>Chamber</th>
            <th>Billing Info</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['appointment_date']??'N/A') ?></td>
                <td><?= htmlspecialchars($row['serial_no']??'N/A') ?></td>

                <!-- Patient -->
                <td>
                    <img src="<?= htmlspecialchars($row['patient_photo']??'N/A') ?>" alt="Patient Photo"><br>
                    <?= htmlspecialchars($row['patient_name']??'N/A') ?><br>
                    <?= htmlspecialchars($row['patient_phones']??'N/A') ?>
                </td>

                <!-- Compounder -->
                <td>
                    <?php if ($row['compounder_name']): ?>
                        <img src="<?= htmlspecialchars($row['compounder_photo']??'N/A') ?>" alt="Compounder Photo"><br>
                        <?= htmlspecialchars($row['compounder_name']??'N/A') ?><br>
                        <?= htmlspecialchars($row['compounder_phones']??'N/A') ?>
                    <?php else: ?>
                       Not Assigned Yet
                    <?php endif; ?>
                </td>

                <!-- Chamber -->
                <td>
                    <strong><?= htmlspecialchars($row['chamber_name']??'N/A') ?></strong><br>
                    <?= htmlspecialchars($row['full_address']??'N/A') ?><br>
                    <?= htmlspecialchars($row['chamber_phones']??'N/A') ?>
                </td>

                <!-- Billing -->
                <td>
                    Consultation Fee: ৳<?= htmlspecialchars($row['consultation_fee']??'N/A') ?><br>
                    Additional Fees: ৳<?= htmlspecialchars($row['additional_fees']??'N/A') ?><br>
                    Discount: <?= htmlspecialchars($row['discount']??'N/A') ?>%<br>
                    Final Amount: <strong>৳<?= htmlspecialchars($row['final_amount']??'N/A') ?></strong><br>
                    Payment Status: <?= htmlspecialchars($row['payment_status']??'N/A') ?><br>
                    Remark:  <?= htmlspecialchars($row['remark']??'N/A') ?>
                </td>

                <!-- Action -->
                <td>
                    <form action="update_billing.php" method="get">
                        <input type="hidden" name="appointment_ID" value="<?= $row['appointment_ID'] ?>">
                        <button class="update-btn" type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('searchBox').addEventListener('keyup', function () {
    const query = this.value.toLowerCase();
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        const patient = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const chamber = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

        if (patient.includes(query) || chamber.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<footer>
    &copy; <?= date("Y") ?> PulseScheduler. All rights reserved.
</footer>

</body>
</html>
