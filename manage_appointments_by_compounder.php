<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

$compounder_id = $_SESSION['user_ID'];

$stmt = $conn->prepare("
    SELECT 
        a.appointment_ID,
        a.appointment_date,
        a.serial_no,
        a.status,
        a.remark,
        a.appointment_start_time,
        a.appointment_end_time,
        d.user_ID AS doctor_id,
        su.full_name AS doctor_name,
        su.photo AS doctor_photo,
        GROUP_CONCAT(DISTINCT dp.phone SEPARATOR ', ') AS doctor_phones,
        p.full_name AS patient_name,
        p.photo AS patient_photo,
        GROUP_CONCAT(DISTINCT pp.phone SEPARATOR ', ') AS patient_phones,
        c.chamber_name,
        CONCAT_WS(', ', ad.house_no, ad.road, ad.area, ad.thana, ad.district, ad.division, ad.postal_code) AS chamber_address,
        GROUP_CONCAT(DISTINCT cp.phone SEPARATOR ', ') AS chamber_phones
    FROM appointment a
    JOIN doctor d ON a.doctor_ID = d.user_ID
    JOIN systemuser su ON d.user_ID = su.user_ID
    LEFT JOIN user_phone dp ON d.user_ID = dp.user_ID
    JOIN systemuser p ON a.user_ID = p.user_ID
    LEFT JOIN user_phone pp ON p.user_ID = pp.user_ID
    JOIN chamber c ON a.chamber_ID = c.chamber_ID
    LEFT JOIN chamber_address ad ON c.chamber_ID = ad.chamber_ID
    LEFT JOIN chamber_phone cp ON c.chamber_ID = cp.chamber_ID
    WHERE a.compounder_ID = ?
    GROUP BY a.appointment_ID
    ORDER BY a.appointment_date DESC, a.appointment_start_time DESC
");
$stmt->bind_param("i", $compounder_id);
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
            background-color: #f4f9ff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background: linear-gradient(90deg, #007BFF, #00C6FF);
            color: white;
            padding: 15px 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        nav {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            margin: 0;
            font-size: 1.9rem;
            letter-spacing: 1px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 24px;
        }

        .nav-links a {
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #FFD54F;
        }

        .container {
            flex: 1;
            max-width: 1400px;
            margin: 40px auto;
            background: #fff;
            padding: 7px ;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.12);
        }

        h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
            color: #007BFF;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        th, td {
            padding: 12px 10px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background: #007BFF;
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #ccc;
        }

        .action-button {
            padding: 7px 14px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .action-button:hover {
            background-color: #0056b3;
        }

        .no-data {
            text-align: center;
            color: #777;
            font-style: italic;
            margin-top: 25px;
        }

        footer {
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 20px;
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
        <li><a href="maanage_compounder_profile.php">Dashboard</a></li>
        <li><a href="manage_billing_by_compounder.php">View Billing</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2>Assigned Appointments</h2>

    <div style="max-width: 1400px; margin: 0 auto 20px auto; padding: 0 30px; display: flex; gap: 20px; flex-wrap: wrap;">
    <input type="date" id="dateSearch" placeholder="Search by Appointment Date" 
           style="flex: 1; padding: 10px; min-width: 250px; border: 1px solid #007BFF; border-radius: 6px;">
           
    <input type="text" id="doctorChamberSearch" placeholder="Search by Doctor or Chamber Name" 
           style="flex: 1; padding: 10px; min-width: 250px; border: 1px solid #007BFF; border-radius: 6px;">
</div>



    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Serial</th>
                    <th>Time</th>
                    <th>Doctor</th>
                    <th>Doctor Photo</th>
                    <th>Doctor Phones</th>
                    <th>Patient</th>
                    <th>Patient Photo</th>
                    <th>Patient Phones</th>
                    <th>Chamber</th>
                    <th>Chamber Address</th>
                    <th>Chamber Phones</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['appointment_date'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['serial_no'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['appointment_start_time']) ?> - <?= htmlspecialchars($row['appointment_end_time']) ?></td>
                    <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                    <td>
                        <?php if (!empty($row['doctor_photo'])): ?>
                            <img src="<?= htmlspecialchars($row['doctor_photo']) ?>" alt="Doctor" class="photo">
                        <?php else: ?>
                            <span>N/A</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['doctor_phones'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td>
                        <?php if (!empty($row['patient_photo'])): ?>
                            <img src="<?= htmlspecialchars($row['patient_photo']) ?>" alt="Patient" class="photo">
                        <?php else: ?>
                            <span>N/A</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['patient_phones'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['chamber_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['chamber_address'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['chamber_phones'] ?? 'N/A') ?></td>
                    <td>
                        <form action="update_appointment_compounder.php" method="get">
                            <input type="hidden" name="appointment_ID" value="<?= htmlspecialchars($row['appointment_ID']) ?>">
                            <button type="submit" class="action-button">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-data">No appointments found.</div>
    <?php endif; ?>
</div>

<script>
    const dateSearch = document.getElementById('dateSearch');
    const doctorChamberSearch = document.getElementById('doctorChamberSearch');
    const tableRows = document.querySelectorAll('tbody tr');

    dateSearch.addEventListener('input', filterTable);
    doctorChamberSearch.addEventListener('input', filterTable);

    function filterTable() {
        const dateValue = dateSearch.value.toLowerCase();
        const doctorChamberValue = doctorChamberSearch.value.toLowerCase();

        tableRows.forEach(row => {
            const date = row.cells[0]?.innerText.toLowerCase() || '';
            const doctor = row.cells[4]?.innerText.toLowerCase() || '';
            const chamber = row.cells[10]?.innerText.toLowerCase() || '';

            const matchDate = date.includes(dateValue);
            const matchDoctorChamber = doctor.includes(doctorChamberValue) || chamber.includes(doctorChamberValue);

            if (matchDate && matchDoctorChamber) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>


<footer>
    <p>&copy; <?= date('Y') ?> PulseScheduler. All rights reserved.</p>
</footer>

</body>
</html>
