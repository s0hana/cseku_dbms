<?php
session_start();
$doctorID = $_SESSION['user_ID'] ?? null;

if (!$doctorID) {
    die("❌ Doctor not logged in.");
}

$mysqli = new mysqli("localhost", "root", "", "pulsescheduler");
if ($mysqli->connect_errno) {
    die("❌ Connection failed: " . $mysqli->connect_error);
}

$message = "";

function isWithinChamberHours($start, $end, $openTime, $closeTime) {
    $opensOvernight = strtotime($closeTime) <= strtotime($openTime);

    if (!$opensOvernight) {
        return ($start >= $openTime && $end <= $closeTime);
    } else {
        // Overnight case: 22:00 – 06:00
        return (
            ($start >= $openTime || $start < $closeTime) &&
            ($end > $openTime || $end <= $closeTime)
        );
    }
}

function isOverlapping($newStart, $newEnd, $existingStart, $existingEnd) {
    return (
        ($newStart >= $existingStart && $newStart < $existingEnd) ||
        ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
        ($newStart <= $existingStart && $newEnd >= $existingEnd)
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chamber_id'])) {
    $chamberID = intval($_POST['chamber_id']);
    $success = true;

    // Ensure doctor works in the chamber
    $check = $mysqli->prepare("SELECT 1 FROM works_in WHERE doctor_ID = ? AND chamber_ID = ?");
    $check->bind_param("ii", $doctorID, $chamberID);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        $insertWI = $mysqli->prepare("INSERT INTO works_in (doctor_ID, chamber_ID) VALUES (?, ?)");
        $insertWI->bind_param("ii", $doctorID, $chamberID);
        $insertWI->execute();
        $insertWI->close();
    }
    $check->close();

    // Fetch chamber info
    $stmt = $mysqli->prepare("SELECT working_days, opening_time, closing_time FROM chamber WHERE chamber_ID = ?");
    $stmt->bind_param("i", $chamberID);
    $stmt->execute();
    $stmt->bind_result($workingDaysJson, $openTimeRaw, $closeTimeRaw);
    $stmt->fetch();
    $stmt->close();

    $workingDays = json_decode($workingDaysJson, true);
    $openTime = date("H:i", strtotime($openTimeRaw));
    $closeTime = date("H:i", strtotime($closeTimeRaw));

    $days = $_POST['day_of_week'] ?? [];
    $starts = $_POST['start_time'] ?? [];
    $ends = $_POST['end_time'] ?? [];
    $patients = $_POST['max_patients'] ?? [];
    $fees = $_POST['consultation_fee'] ?? [];
    $room_numbers = $_POST['room_number'] ?? [];

    $insertStmt = $mysqli->prepare("INSERT INTO schedule_doctor (doctor_id, chamber_id, day_of_week, start_time, end_time, max_patients, consultation_fee, room_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($days); $i++) {
        $day = $days[$i];
        $start = date("H:i", strtotime($starts[$i]));
        $end = date("H:i", strtotime($ends[$i]));
        $maxP = intval($patients[$i]);
        $fee = floatval($fees[$i]);
        $room = $room_numbers[$i];

        if (!in_array($day, $workingDays)) {
            $message = "❌ Chamber is closed on $day.";
            $success = false;
            break;
        }

        if (!isWithinChamberHours($start, $end, $openTime, $closeTime)) {
            $message = "❌ Schedule $start – $end on $day is outside chamber hours ($openTime – $closeTime).";
            $success = false;
            break;
        }

        // Check for overlapping schedules
        $existing = $mysqli->prepare("SELECT start_time, end_time FROM schedule_doctor WHERE doctor_id = ? AND day_of_week = ?");
        $existing->bind_param("is", $doctorID, $day);
        $existing->execute();
        $result = $existing->get_result();

        while ($row = $result->fetch_assoc()) {
            $exStart = date("H:i", strtotime($row['start_time']));
            $exEnd = date("H:i", strtotime($row['end_time']));

            if (isOverlapping(strtotime($start), strtotime($end), strtotime($exStart), strtotime($exEnd))) {
                $message = "❌ Overlapping schedule on $day ($start – $end conflicts with $exStart – $exEnd).";
                $success = false;
                break 2;
            }
        }
        $existing->close();

        // Insert schedule
        $insertStmt->bind_param("iisssids", $doctorID, $chamberID, $day, $start, $end, $maxP, $fee, $room);
        if (!$insertStmt->execute()) {
            $message = "❌ Failed to insert schedule on $day ($start – $end).";
            $success = false;
            break;
        }
    }

    $insertStmt->close();

    if ($success) {
        $message = "✅ Schedule saved successfully!";
        header("Location: works_in_schedule_list_doctor.php?user_ID=$doctorID");
        exit;
    }
}

?>


<?php $message = $message ?? ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Schedule - PulseScheduler</title>
    <style>
        :root {
            --primary: #007BFF;
            --primary-light: #00C6FF;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --hover-yellow: #ffcc00;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        header {
            background-color: var(--primary);
            color: var(--white);
            padding: 1rem 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav ul li {
            margin-left: 1rem;
        }

        nav ul li a {
            color: var(--white);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            background-color: var(--hover-yellow);
            color: var(--dark);
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 30px auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .schedule-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .schedule-row > div {
            flex: 1 1 150px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        select, input {
            padding: 8px;
            width: 100%;
        }

        .message {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
        }

        .success {
            color: var(--success);
        }

        .error {
            color: var(--danger);
        }

        button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0;
        }

        #chamber_results div {
            background: #eee;
            padding: 5px;
            cursor: pointer;
            margin-bottom: 2px;
            border-radius: 4px;
        }

        #chamber_results div:hover {
            background: #ddd;
        }

        footer {
            background-color: var(--primary);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">PulseScheduler</div>
    <nav>
        <ul>
            <li><a href="works_in_schedule_list_doctor.php?user_ID=<?php echo $doctorID; ?>">My Schedules</a></li>
            <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<form method="POST">
    <h2>Setup Doctor Schedule</h2>

    <label>Search Chamber:</label>
    <input type="text" id="chamber_search" placeholder="Search by name or address">
    <input type="hidden" id="chamber_id" name="chamber_id">
    <div id="chamber_results"></div>
    <br>
    <button type="button" onclick="window.location.href='add_chamber.php'">+ Add Chamber</button>
    
    <br>
    <div id="schedule_container">
        <div class="schedule-row">
            <div>
                <label>Day:</label>
                <select name="day_of_week[]" required>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
            </div>
            <div>
                <label>Start Time:</label>
                <input type="time" name="start_time[]" required>
            </div>
            <div>
                <label>End Time:</label>
                <input type="time" name="end_time[]" required>
            </div>
            <div>
                <label>Max Patients:</label>
                <input type="number" name="max_patients[]" required>
            </div>
            <div>
                <label>Consultation Fee:</label>
                <input type="number" name="consultation_fee[]" step="0.01" required>
            </div>
            <div>
                <label>Room Number:</label>
                <input type="room" name="room_number[]" style="width: 25%;"required>
            </div>
        </div>
    </div>

    <button type="button" onclick="addScheduleRow()">+ Add More Schedule</button>
    <button type="submit">Save Schedule</button>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
</form>

<footer>
    &copy; <?= date("Y") ?> PulseScheduler. All rights reserved.
</footer>

<script>
    document.getElementById('chamber_search').addEventListener('input', function () {
        const query = this.value;
        if (query.length < 1) return;

        fetch('search_chambers.php?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                const resultBox = document.getElementById('chamber_results');
                resultBox.innerHTML = '';
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.textContent = item.label;
                    div.onclick = () => {
                        document.getElementById('chamber_search').value = item.label;
                        document.getElementById('chamber_id').value = item.value;
                        resultBox.innerHTML = '';
                    };
                    resultBox.appendChild(div);
                });
            });
    });

    function addScheduleRow() {
        const container = document.getElementById('schedule_container');
        const row = document.createElement('div');
        row.className = 'schedule-row';
        row.innerHTML = `
            <div>
                <select name="day_of_week[]" required>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
            </div>
            <div><input type="time" name="start_time[]" required></div>
            <div><input type="time" name="end_time[]" required></div>
            <div><input type="number" name="max_patients[]" required></div>
            <div><input type="number" name="consultation_fee[]" step="0.01" required></div>
            <div><input type="room" name="room_number[]" required></div>

        `;
        container.appendChild(row);
    }
</script>

</body>
</html>
