<?php
session_start();

$compounder_ID = $_SESSION['user_ID'] ?? null;
$doctor_ID = $_GET['doctor_ID'] ?? null;

if (!$compounder_ID || !$doctor_ID) {
    die("Unauthorized access or missing doctor ID.");
}

$conn = new mysqli("localhost", "root", "", "pulsescheduler");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days = $_POST['day'];
    $starts = $_POST['start_time'];
    $ends = $_POST['end_time'];

    $count = count($days);
    $hasConflict = false;
    $errors = [];

    for ($i = 0; $i < $count; $i++) {
        $day = $days[$i];
        $start = $starts[$i];
        $end = $ends[$i];

        $stmt = $conn->prepare("
            SELECT * FROM doctor_schedule_compounder
            WHERE doctor_id = ? AND compounder_id = ? AND day_of_week = ?
            AND (
                (start_time < ? AND end_time > ?) OR
                (start_time < ? AND end_time > ?) OR
                (start_time >= ? AND end_time <= ?)
            )
        ");
        $stmt->bind_param("iisssssss", $doctor_ID, $compounder_ID, $day, $end, $end, $start, $start, $start, $end);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $hasConflict = true;
            $errors[] = "Overlap found on $day between $start and $end.";
        }

        $stmt->close();
    }

    if ($hasConflict) {
        $_SESSION['errors'] = $errors;
        header("Location: edit_schedule_compounder.php?doctor_ID=$doctor_ID");
        exit;
    }

    // No conflict — clear old and insert new
    $stmt = $conn->prepare("DELETE FROM doctor_schedule_compounder WHERE doctor_id = ? AND compounder_id = ?");
    $stmt->bind_param("ii", $doctor_ID, $compounder_ID);
    $stmt->execute();
    $stmt->close();

    for ($i = 0; $i < $count; $i++) {
        $day = $days[$i];
        $start = $starts[$i];
        $end = $ends[$i];

        $stmt = $conn->prepare("INSERT INTO doctor_schedule_compounder (doctor_id, compounder_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $doctor_ID, $compounder_ID, $day, $start, $end);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: my_doctors.php");
    exit;
}

// Load existing schedule
$schedule = [];
$stmt = $conn->prepare("SELECT day_of_week, start_time, end_time FROM doctor_schedule_compounder WHERE doctor_id = ? AND compounder_id = ?");
$stmt->bind_param("ii", $doctor_ID, $compounder_ID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $schedule[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Schedule - PulseScheduler</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            color: var(--white);
            padding: 1rem 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo h1 {
            font-size: 1.5rem;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--hover-yellow);
        }

        main {
            flex: 1;
            background: white;
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #1e40af;
            color: white;
        }

        td select, td input {
            width: 105%;
            padding: 9px;
        }

        .btn {
            background-color: var(--primary);
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-secondary {
            background-color: var(--secondary);
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-secondary:hover {
            background-color: #495057;
        }

        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }

        footer {
            background-color: var(--primary);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: auto;
            font-size: 0.9rem;
        }
    </style>
    <script>
        function addRow() {
            const table = document.getElementById('scheduleTable').getElementsByTagName('tbody')[0];
            const index = table.rows.length;

            const row = table.insertRow();
            row.innerHTML = `
                <td>
                    <select name="day[${index}]" required>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                    </select>
                </td>
                <td><input type="time" name="start_time[${index}]" required></td>
                <td><input type="time" name="end_time[${index}]" required></td>
            `;
        }

        window.onload = function () {
            const scheduleData = <?php echo json_encode($schedule); ?>;
            const tableBody = document.getElementById('scheduleTable').getElementsByTagName('tbody')[0];
            scheduleData.forEach((item, index) => {
                const row = tableBody.insertRow();
                row.innerHTML = `
                    <td>
                        <select name="day[${index}]" required>
                            <option value="Saturday" ${item.day_of_week === 'Saturday' ? 'selected' : ''}>Saturday</option>
                            <option value="Sunday" ${item.day_of_week === 'Sunday' ? 'selected' : ''}>Sunday</option>
                            <option value="Monday" ${item.day_of_week === 'Monday' ? 'selected' : ''}>Monday</option>
                            <option value="Tuesday" ${item.day_of_week === 'Tuesday' ? 'selected' : ''}>Tuesday</option>
                            <option value="Wednesday" ${item.day_of_week === 'Wednesday' ? 'selected' : ''}>Wednesday</option>
                            <option value="Thursday" ${item.day_of_week === 'Thursday' ? 'selected' : ''}>Thursday</option>
                            <option value="Friday" ${item.day_of_week === 'Friday' ? 'selected' : ''}>Friday</option>
                        </select>
                    </td>
                    <td><input type="time" name="start_time[${index}]" value="${item.start_time}" required></td>
                    <td><input type="time" name="end_time[${index}]" value="${item.end_time}" required></td>
                `;
            });
        };
    </script>
</head>
<body>

<header>
    <nav>
        <div class="logo"><h1>PulseScheduler</h1></div>
        <ul class="nav-links">
            <li><a href="maanage_compounder_profile.php">My Profile</a></li>
            <li><a href="my_doctors.php">My Doctors</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <h2>Set Schedule with Doctor</h2>
    <form method="POST">
        <table id="scheduleTable">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic rows will be populated here -->
            </tbody>
        </table>

        <div class="action-buttons">
            <button type="button" class="btn btn-secondary" onclick="addRow()">Add Row</button>
            <button type="submit" class="btn">Save Schedule</button>
        </div>
    </form>
</main>

<footer>
    &copy; <?php echo date("Y"); ?> PulseScheduler. All rights reserved.
</footer>

</body>
</html>
