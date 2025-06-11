<?php
session_start();
include 'db.php';

$loggedInUserID = $_SESSION['user_ID'];

$sql = "
SELECT 
    a.appointment_ID,
    a.appointment_date,
    a.appointment_start_time,
    a.appointment_end_time,
    a.remark,
    a.serial_no,
    a.status,
    d.user_ID AS doctor_ID,   -- doctor ID from doctor table
    su.full_name AS doctor_name,
    su.photo AS doctor_photo,
    GROUP_CONCAT(DISTINCT up.phone SEPARATOR ', ') AS doctor_phones,
    c.chamber_name,
    GROUP_CONCAT(DISTINCT cp.phone SEPARATOR ', ') AS chamber_phones,
    CONCAT_WS(', ',
        ad.house_no, ad.road, ad.area, ad.thana, ad.district, ad.division, ad.postal_code
    ) AS full_address,
    sd.start_time,   -- schedule start time from schedule_doctor
    sd.end_time,     -- schedule end time from schedule_doctor
    sd.schedule_id,           -- schedule ID from schedule_doctor
    sd.consultation_fee,      -- consultation fee from schedule_doctor
    sd.room_number            -- room number from schedule_doctor
FROM appointment a
JOIN doctor d ON a.doctor_ID = d.user_ID
JOIN systemuser su ON d.user_ID = su.user_ID
LEFT JOIN user_phone up ON up.user_ID = d.user_ID
JOIN chamber c ON a.chamber_ID = c.chamber_ID
LEFT JOIN chamber_phone cp ON cp.chamber_ID = c.chamber_ID
LEFT JOIN chamber_address ad ON ad.chamber_ID = c.chamber_ID
LEFT JOIN schedule_doctor sd 
  ON sd.schedule_id = a.sch_id   -- Linking appointment's sch_id with schedule_doctor's schedule_id

WHERE a.user_ID = ?
GROUP BY a.appointment_ID
ORDER BY a.appointment_date DESC;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $loggedInUserID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
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

        .container {
            max-width: 95%;
            background: white;
            padding: 30px 40px;
            margin: 50px auto;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color:rgb(7, 138, 238);
            color: white;
        }

        img.doctor-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            margin-bottom: 5px;
        }

        .rate-btn {
            padding: 6px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .rate-btn:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            table {
                font-size: 14px;
            }
            .rate-btn {
                font-size: 12px;
                padding: 5px 10px;
            }
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
            <li><a href="general.php">Back</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2>My Appointments</h2>

    <div style="margin-bottom: 20px; text-align: center;">
    <input type="text" id="searchBox" placeholder="Search by Doctor's or Chamber's Name..." style="padding: 8px; width: 100%; max-width: 300px;">
    <input type="date" id="dateSearchBox" style="padding: 8px; width: 100%; max-width: 300px;">
    </div>


    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Serial</th>
                <th>Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Remark</th>
                <th>Room no.</th>
                <th>Doctor</th>
                <th>Doctor Phones</th>
                <th>Chamber</th>
                <th>Chamber Address</th>
                <th>Chamber Phones</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                <td><?= htmlspecialchars($row['serial_no']) ?></td>
                <td><?= isset($row['appointment_start_time']) ? date("h:i A", strtotime($row['appointment_start_time'])) : 'N/A' ?></td>
                <td><?= isset($row['appointment_end_time']) ? date("h:i A", strtotime($row['appointment_end_time'])) : 'N/A' ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['remark']??'N/A') ?></td>
                <td><?= htmlspecialchars($row['room_number']??'N/A') ?></td>
                <td>
                    <img src="<?= htmlspecialchars($row['doctor_photo']??'N/A') ?>" alt="Doctor" class="doctor-photo">
                    <?= htmlspecialchars($row['doctor_name']) ?>
                </td>
                <td><?= htmlspecialchars($row['doctor_phones']??'N/A') ?></td>
                <td><?= htmlspecialchars($row['chamber_name']??'N/A') ?></td>
                <td><?= htmlspecialchars($row['full_address']??'N/A') ?></td>
                <td><?= htmlspecialchars($row['chamber_phones']??'N/A') ?></td>
                <td>
            <a href="rate_doctor.php?doctor_ID=<?= $row['doctor_ID'] ?>">
        <button class="rate-btn">Give Ratings</button>
        </a>
        <?php if ($row['status'] == 'Completed') { ?>
        <a href="billing.php?appointment_ID=<?= $row['appointment_ID'] ?>">
            <button class="billing-btn">View Billing</button>
        </a>

<?php } ?>
            </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('searchBox').addEventListener('input', function () {
    let query = this.value.trim().toLowerCase();
    let rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        let doctorCell = row.querySelector('td:nth-child(8)'); // Doctor column
        let chamberCell = row.querySelector('td:nth-child(10)'); // Chamber column

        let doctorName = doctorCell.textContent.trim().toLowerCase();
        let chamberName = chamberCell.textContent.trim().toLowerCase();

        if (doctorName.includes(query) || chamberName.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});


document.getElementById('dateSearchBox').addEventListener('input', function() {
    let dateQuery = this.value.trim(); // Get the search query (date)
    let table = document.querySelector('table'); // Get the table
    let rows = table.querySelectorAll('tbody tr'); // Get all rows in the table body

    // Loop through all rows and hide/show based on the date query
    rows.forEach(row => {
        let dateCell = row.querySelector('td:nth-child(1)'); // Get the date cell (column 1)
        let appointmentDate = dateCell.textContent.trim(); // Appointment date

        // If the date contains the query, show the row; otherwise, hide it
        if (appointmentDate.includes(dateQuery)) {
            row.style.display = ''; // Show row
        } else {
            row.style.display = 'none'; // Hide row
        }
    });
});
</script>


<footer>
    <p>&copy; 2025 PulseScheduler | <a href="Contact.php" style="color: white;">Contact Us</a></p>
</footer>
</body>
</html>
