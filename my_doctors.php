<?php
session_start();

// Get compounder ID from session
$compounder_ID = $_SESSION['user_ID'] ?? null;
if (!$compounder_ID) {
    die("Unauthorized access");
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "pulsescheduler");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctors the compounder works for
$sql = "
    SELECT 
        s.user_ID,
        s.full_name,
        s.photo,
        GROUP_CONCAT(DISTINCT p.phone SEPARATOR ', ') AS phones,
        GROUP_CONCAT(DISTINCT e.email SEPARATOR ', ') AS emails,
        CONCAT_WS(', ', a.house_no, a.road, a.area, a.thana, a.district, a.division, a.postal_code) AS address
    FROM works_for wf
    JOIN systemuser s ON wf.doctor_ID = s.user_ID
    LEFT JOIN user_phone p ON s.user_ID = p.user_ID
    LEFT JOIN user_email e ON s.user_ID = e.user_ID
    LEFT JOIN user_address a ON s.user_ID = a.user_ID
    WHERE wf.compounder_ID = ?
    GROUP BY s.user_ID
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $compounder_ID);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}
$stmt->close();

// Fetch schedule for each doctor
for ($i = 0; $i < count($doctors); $i++) {
    $schedule_stmt = $conn->prepare("
        SELECT day_of_week, start_time, end_time
        FROM doctor_schedule_compounder
        WHERE doctor_id = ? AND compounder_id = ?
    ");
    $schedule_stmt->bind_param("ii", $doctors[$i]['user_ID'], $compounder_ID);
    $schedule_stmt->execute();
    $schedule_result = $schedule_stmt->get_result();

    $schedules = [];
    while ($row = $schedule_result->fetch_assoc()) {
        $schedules[] = [
            'day' => $row['day_of_week'],
            'start' => $row['start_time'],
            'end' => $row['end_time'],
        ];
    }
    $doctors[$i]['schedule'] = $schedules;
    $schedule_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Doctors</title>
    <style>
        /* (CSS remains the same as original) */
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
        main {
            flex: 1;
            padding: 40px 60px;
        }
        h2 {
            color: #007bff;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #1e40af;
            color: white;
        }
        img {
            max-width: 80px;
            border-radius: 8px;
        }
        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }
        .btn {
            display: inline-block;
            padding: 6px 12px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<!-- Header -->
<header>
    <nav>
        <div class="logo"><h1>PulseScheduler</h1></div>
        <ul class="nav-links">
            <li><a href="maanage_compounder_profile.php">Dashboard</a></li>
            <li><a href="chamber_info_compounder.php">My Chamber</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<main>
    <h2 style="text-align: center;">Doctors I Work For</h2>

    <?php if (empty($doctors)): ?>
        <p>You are not assigned to any doctors yet.</p>
    <?php else: ?>
        <div style="text-align: center; margin-bottom: 20px;">
            <input type="text" id="searchInput" placeholder="Search doctors by name..." style="padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <table>
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Phone(s)</th>
                    <th>Email(s)</th>
                    <th>Address</th>
                    <th>Schedule</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < count($doctors); $i++): ?>
                    <?php
                        $doc = $doctors[$i];
                        $schedules = $doc['schedule'];
                        $hasSchedule = !empty($schedules);
                    ?>
                    <?php if ($hasSchedule): ?>
                        <?php for ($j = 0; $j < count($schedules); $j++): ?>
                            <tr>
                                <td>
                                    <?php if ($doc['photo']): ?>
                                        <img src="<?= htmlspecialchars($doc['photo']) ?>" alt="Doctor Photo">
                                    <?php else: ?>
                                        <em>No photo</em>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($doc['full_name']) ?></td>
                                <td><?= htmlspecialchars($doc['phones'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($doc['emails'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($doc['address'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($schedules[$j]['day']) . ": " . htmlspecialchars($schedules[$j]['start']) . " - " . htmlspecialchars($schedules[$j]['end']) ?></td>
                                <td>
                                    <a class="btn" href="edit_schedule_compounder.php?doctor_ID=<?= urlencode($doc['user_ID']) ?>">Set Schedule</a><br>
                                    <a class="btn" style="color: white; background-color: #dc3545;" href="javascript:void(0);" onclick="showConfirmDialog('<?= urlencode($doc['user_ID']) ?>', '<?= htmlspecialchars($schedules[$j]['day']) ?>', '<?= htmlspecialchars($schedules[$j]['start']) ?>', '<?= htmlspecialchars($schedules[$j]['end']) ?>')">Remove</a>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    <?php else: ?>
                        <tr>
                            <td>
                                <?php if ($doc['photo']): ?>
                                    <img src="<?= htmlspecialchars($doc['photo']) ?>" alt="Doctor Photo">
                                <?php else: ?>
                                    <em>No photo</em>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($doc['full_name']) ?></td>
                            <td><?= htmlspecialchars($doc['phones'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($doc['emails'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($doc['address'] ?? 'N/A') ?></td>
                            <td>N/A</td>
                            <td><a class="btn" href="edit_schedule_compounder.php?doctor_ID=<?= urlencode($doc['user_ID']) ?>">Set Schedule</a></td>
                        </tr>
                    <?php endif; ?>
                <?php endfor; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<script>
    function showConfirmDialog(doctor_ID, day_of_week, start_time, end_time) {
        const modalHTML = `
            <div id="confirmModal" style="display: block; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999;">
                <div style="background: white; margin: 100px auto; padding: 20px; width: 300px; border-radius: 8px; text-align: center;">
                    <h4>Are you sure you want to remove this schedule?</h4>
                    <button onclick="removeSchedule('${doctor_ID}', '${day_of_week}', '${start_time}', '${end_time}')" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px;">Yes</button>
                    <button onclick="closeModal()" style="background-color: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin-left: 10px;">No</button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    function removeSchedule(doctor_ID, day_of_week, start_time, end_time) {
        const url = `remove_schedule_compounder.php?doctor_ID=${encodeURIComponent(doctor_ID)}&day_of_week=${encodeURIComponent(day_of_week)}&start_time=${encodeURIComponent(start_time)}&end_time=${encodeURIComponent(end_time)}`;
        window.location.href = url;
    }

    function closeModal() {
        const modal = document.getElementById('confirmModal');
        if (modal) modal.remove();
    }

    document.getElementById("searchInput").addEventListener("keyup", function () {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll("tbody tr");
        rows.forEach(row => {
            const nameCell = row.querySelector("td:nth-child(2)");
            if (nameCell) {
                const nameText = nameCell.textContent.toLowerCase();
                row.style.display = nameText.includes(searchValue) ? "" : "none";
            }
        });
    });
</script>

<!-- Footer -->
<footer>© 2025 PulseScheduler</footer>
</body>
</html>
