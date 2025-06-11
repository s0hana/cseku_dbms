<?php
session_start();
require_once 'db.php'; 
$compounder_ID = $_SESSION['user_ID'] ?? null;

if (!$compounder_ID) {
    die("Unauthorized access");
}
$query = "
SELECT 
    b.*,
    ap.*,
    b.remark as billing_remark,
    su_p.full_name AS patient_name,
    su_p.photo AS patient_photo,
    GROUP_CONCAT(DISTINCT up.phone SEPARATOR ', ') AS patient_phones,

    su_d.full_name AS doctor_name,
    su_d.photo AS doctor_photo,
    GROUP_CONCAT(DISTINCT dp.phone SEPARATOR ', ') AS doctor_phones,

    c.chamber_name,
    CONCAT_WS(', ',
        a.house_no, a.road, a.area,
        a.thana, a.district, a.division, a.postal_code
    ) AS chamber_full_address,
    GROUP_CONCAT(DISTINCT cp.phone SEPARATOR ', ') AS chamber_phones

FROM billing b
JOIN appointment ap ON b.appointment_ID = ap.appointment_ID

JOIN systemuser su_p ON ap.user_ID = su_p.user_ID
LEFT JOIN user_phone up ON su_p.user_ID = up.user_ID

JOIN systemuser su_d ON ap.doctor_ID = su_d.user_ID
LEFT JOIN user_phone dp ON su_d.user_ID = dp.user_ID

JOIN chamber c ON ap.chamber_ID = c.chamber_ID
LEFT JOIN chamber_address a ON c.chamber_ID = a.chamber_ID
LEFT JOIN chamber_phone cp ON c.chamber_ID = cp.chamber_ID

WHERE b.compounder_ID = $compounder_ID
GROUP BY b.appointment_ID
ORDER BY 
    CASE b.payment_status
        WHEN 'Pending' THEN 1
        WHEN 'Paid' THEN 2
        WHEN 'Free' THEN 3
        ELSE 4
    END,
    b.payment_date DESC

";

$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Billing by Compounder</title>
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
    width: 97%;
    max-width: 100%;
    margin: 0 auto 30px auto; /*center table horizontally */
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 7px;
    border-radius: 2px;
    overflow: hidden;
    table-layout: fixed;
}


        th, td {
            padding: 3px 3px;
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
            padding: 6px 6px;
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
            <li><a href="maanage_compounder_profile.php">Dashboard</a></li>
            <li><a href="manage_appointments_by_compounder.php">Manage Appointments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<h2>Manage Billing (Compounder)</h2>

<div style="max-width: 1300px; margin: 0 auto 30px auto; padding: 0 30px; display: flex; gap: 20px; flex-wrap: wrap;">
    <input type="date" id="dateSearch" placeholder="Search by Appointment Date" style="flex: 1; padding: 10px; min-width: 267px;">
    <input type="text" id="doctorChamberSearch" placeholder="Search by Doctor or Chamber Name" style="flex: 1; padding: 10px; min-width: 250px;">
</div>



<table>
    <thead>
        <tr>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Patient</th>
            <th>Patient Phones</th>
            <th>Doctor</th>
            <th>Doctor Phones</th>
            <th>Chamber Name & Address</th>
            <th>Chamber Phones</th>
            <th>Discount</th>
            <th>Additional Fees</th>
            <th>Payment Date</th>
            <th>Payment Status</th>
            <th>Remark</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                <td><?= htmlspecialchars($row['appointment_start_time']) ?></td>
                <td>
                    <img src="<?= htmlspecialchars($row['patient_photo']) ?>" alt="Patient">
                    <br><?= htmlspecialchars($row['patient_name']) ?>
                </td>
                <td><?= htmlspecialchars($row['patient_phones']) ?></td>
                <td>
                    <img src="<?= htmlspecialchars($row['doctor_photo']) ?>" alt="Doctor">
                    <br><?= htmlspecialchars($row['doctor_name']) ?>
                </td>
                <td><?= htmlspecialchars($row['doctor_phones']) ?></td>
                <td><?= htmlspecialchars($row['chamber_name']) ?>, <?= htmlspecialchars($row['chamber_full_address']) ?></td>
                <td><?= htmlspecialchars($row['chamber_phones']) ?></td>
                <td><?= htmlspecialchars($row['discount']) ?></td>
                <td><?= htmlspecialchars($row['additional_fees']) ?></td>
                <td><?= htmlspecialchars($row['payment_date']) ?></td>
                <td><?= htmlspecialchars($row['payment_status']) ?></td>
                <td><?= htmlspecialchars($row['billing_remark']) ?></td>
                <td>
                    <br>
                    <a class="update-btn" href="update_billing_compounder.php?appointment_ID=<?= $row['appointment_ID'] ?>">Update</a><br><br>
                    <a class="update-btn" href="see_billing_compounder.php?appointment_ID=<?= $row['appointment_ID'] ?>">Info</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="15">No billing records found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('dateSearch');
    const doctorChamberInput = document.getElementById('doctorChamberSearch');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const dateValue = dateInput.value.toLowerCase();
        const doctorChamberValue = doctorChamberInput.value.toLowerCase();

        tableRows.forEach(row => {
            const appointmentDate = row.cells[0]?.textContent.toLowerCase() || '';
            const doctorName = row.cells[4]?.textContent.toLowerCase() || '';
            const chamberName = row.cells[6]?.textContent.toLowerCase() || '';

            const matchesDate = appointmentDate.includes(dateValue);
            const matchesDoctorChamber = doctorName.includes(doctorChamberValue) || chamberName.includes(doctorChamberValue);

            if ((dateValue === '' || matchesDate) && (doctorChamberValue === '' || matchesDoctorChamber)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    dateInput.addEventListener('input', filterTable);
    doctorChamberInput.addEventListener('input', filterTable);
});
</script>


<footer>
    &copy; <?= date("Y") ?> PulseScheduler. All rights reserved.
</footer>

</body>
</html>
