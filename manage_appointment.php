<?php
// manage_appointment.php

session_start();
require_once 'db.php';

$doctor_ID = $_SESSION['user_ID'] ?? null;

if (!$doctor_ID) {
    die("Doctor not logged in.");
}

$sql = "
SELECT 
    a.*,
    p.full_name AS patient_name,
    p.photo AS patient_photo,
    GROUP_CONCAT(DISTINCT pp.phone SEPARATOR ', ') AS patient_phones,

    c.chamber_name,
    GROUP_CONCAT(DISTINCT cp.phone SEPARATOR ', ') AS chamber_phones,
    
    ad.house_no, ad.road, ad.area, ad.thana, ad.district, ad.division, ad.postal_code,

    cmp.full_name AS compounder_name,
    cmp.photo AS compounder_photo,
    GROUP_CONCAT(DISTINCT cmp_phone.phone SEPARATOR ', ') AS compounder_phones

FROM appointment a
LEFT JOIN systemuser p ON a.user_ID = p.user_ID
LEFT JOIN systemuser cmp ON a.compounder_ID = cmp.user_ID

LEFT JOIN user_phone pp ON pp.user_ID = p.user_ID
LEFT JOIN user_phone cmp_phone ON cmp_phone.user_ID = cmp.user_ID

LEFT JOIN chamber c ON a.chamber_ID = c.chamber_ID
LEFT JOIN chamber_phone cp ON cp.chamber_ID = c.chamber_ID
LEFT JOIN chamber_address ad ON ad.chamber_ID = c.chamber_ID

WHERE a.doctor_ID = ?
GROUP BY a.appointment_ID

ORDER BY 
    CASE 
        WHEN a.status = 'Scheduled' THEN 1
        WHEN a.status = 'Completed' THEN 2
        WHEN a.status = 'Cancelled' THEN 3
        ELSE 4
    END,
    a.appointment_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_ID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Appointments</title>
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
            color: #007bff;
            text-align: center;
            margin-top: 30px;
        }

        table {
            width: 95%;
            border-collapse: collapse;
            margin: 25px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        img {
            max-width: 60px;
            max-height: 60px;
            border-radius: 6px;
        }

        .update-btn {
            padding: 8px 14px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            font-weight: bold;
        }

        .update-btn:hover {
            background-color: #0056b3;
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
            <li><a href="manage_billing.php">Manage Billing</a></li>
            <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<h2>All Appointments</h2>
<div style="text-align: center; margin-top: 20px;">
    <input type="text" id="searchBox" placeholder="Search by patient or chamber name..." 
           style="padding: 10px; width: 300px; font-size: 1rem; border: 1px solid #ccc; border-radius: 6px;">
</div>

<table>
    <tr>
        <th>Appointment Info</th>
        <th>Patient</th>
        <th>Chamber</th>
        <th>Compounder</th>
        <th>Update</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>
                <strong>Date:</strong> <?= $row['appointment_date'] ?><br>
                <strong>Time:</strong> <?= $row['appointment_start_time'] ?> - <?= $row['appointment_end_time'] ?><br>
                <strong>Serial:</strong> <?= $row['serial_no'] ?><br>
                <strong>Status:</strong> <?= $row['status'] ?><br>
                <strong>Remark:</strong> <?= nl2br(htmlspecialchars($row['remark']??'N/A')) ?>
            </td>

            <td>
                <img src="<?= htmlspecialchars($row['patient_photo']??'N/A') ?>" alt="Patient Photo"><br>
                <?= htmlspecialchars($row['patient_name']??'N/A') ?><br>
                <small><?= $row['patient_phones'] ?></small>
            </td>

            <td>
                <strong><?= htmlspecialchars($row['chamber_name']) ?></strong><br>
                <?= $row['house_no'] ? $row['house_no'] . ', ' : '' ?>
                <?= $row['road'] ? $row['road'] . ', ' : '' ?>
                <?= $row['area'] ? $row['area'] . ', ' : '' ?>
                <?= $row['thana'] ? $row['thana'] . ', ' : '' ?>
                <?= $row['district'] ? $row['district'] . ', ' : '' ?>
                <?= $row['division'] ? $row['division'] . ', ' : '' ?>
                <?= $row['postal_code'] ?><br>
                <small><?= $row['chamber_phones'] ?></small>
            </td>

            <td>
                <?php if ($row['compounder_ID']): ?>
                    <img src="<?= htmlspecialchars($row['compounder_photo']) ?>" alt="Compounder Photo"><br>
                    <?= htmlspecialchars($row['compounder_name']) ?><br>
                    <small><?= $row['compounder_phones'] ?></small>
                <?php else: ?>
                    <em>Not Assigned</em>
                <?php endif; ?>
            </td>

            <td>
                <form action="update_appointment.php" method="get">
                    <input type="hidden" name="appointment_ID" value="<?= $row['appointment_ID'] ?>">
                    <button type="submit" class="update-btn">Update</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<script>
document.getElementById("searchBox").addEventListener("keyup", function () {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll("table tr:not(:first-child)");

    rows.forEach(row => {
        const patientCell = row.cells[1]?.innerText.toLowerCase();
        const chamberCell = row.cells[2]?.innerText.toLowerCase();
        
        if (patientCell.includes(searchTerm) || chamberCell.includes(searchTerm)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>

<footer>
    &copy; <?= date("Y") ?> PulseScheduler. All rights reserved.
</footer>

</body>
</html>
