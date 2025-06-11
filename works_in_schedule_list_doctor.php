<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "pulsescheduler");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_ID = isset($_GET['user_ID']) ? intval($_GET['user_ID']) : 0;

$sql = "SELECT sd.schedule_id, sd.day_of_week, sd.start_time, sd.end_time, sd.max_patients, sd.consultation_fee, sd.room_number,
               c.chamber_name,
               a.house_no, a.road, a.area, a.thana, a.district, a.division, a.postal_code
        FROM schedule_doctor sd
        JOIN chamber c ON sd.chamber_id = c.chamber_ID
        JOIN chamber_address a ON c.chamber_ID = a.chamber_ID
        WHERE sd.doctor_id = $user_ID
        ORDER BY c.chamber_name, sd.day_of_week, sd.start_time";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Doctor's Schedule - PulseScheduler</title>
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
            margin-left: 0.5rem;
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

        main {
            max-width: 1400px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        #searchBox {
            margin: 20px auto;
            padding: 10px;
            width: 60%;
            display: block;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: var(--primary);
            color: white;
        }

        .delete-btn {
            background-color: var(--danger);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        #confirmModal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        #confirmBox {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }

        #confirmBox p {
            color: red;
            font-weight: bold;
        }

        .confirm-btn, .cancel-btn {
            margin-top: 15px;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .confirm-btn {
            background-color: var(--danger);
            color: white;
            margin-right: 10px;
        }

        .cancel-btn {
            background-color: #ccc;
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
        <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="add_chamber.php">Add Chamber</a></li>
            <li><a href="add_schedule.php">Add Schedule</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <h2>My Schedules</h2>

    <input type="text" id="searchBox" placeholder="Search chamber, address, day..." onkeyup="filterTable()">

    <table id="scheduleTable">
        <thead>
            <tr>
                <th>Chamber Name</th>
                <th>Address</th>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Max Patients</th>
                <th>Consultation Fee</th>
                <th>Room Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="row_<?= $row['schedule_id'] ?>">
                        <td><?= htmlspecialchars($row['chamber_name']) ?></td>
                        <td>
                            <?= htmlspecialchars($row['house_no']) ?>,
                            <?= htmlspecialchars($row['road']) ?>,
                            <?= htmlspecialchars($row['area']) ?>,
                            <?= htmlspecialchars($row['thana']) ?>,
                            <?= htmlspecialchars($row['district']) ?>,
                            <?= htmlspecialchars($row['division']) ?>,
                            <?= htmlspecialchars($row['postal_code']) ?>
                        </td>
                        <td><?= $row['day_of_week'] ?></td>
                        <td><?= date("g:i A", strtotime($row['start_time'])) ?></td>
                        <td><?= date("g:i A", strtotime($row['end_time'])) ?></td>
                        <td><?= $row['max_patients'] ?></td>
                        <td><?= number_format($row['consultation_fee'], 2) ?></td>
                        <td><?= $row['room_number'] ?></td>
                        <td><button class="delete-btn" onclick="confirmDelete(<?= $row['schedule_id'] ?>)">Delete</button></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No schedules found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<div id="confirmModal">
    <div id="confirmBox">
        <p>Are you sure you want to delete this schedule?</p>
        <button id="confirmBtn" class="confirm-btn">Delete</button>
        <button onclick="hideModal()" class="cancel-btn">Cancel</button>
    </div>
</div>

<footer>
    &copy; <?= date("Y") ?> PulseScheduler. All rights reserved.
</footer>

<script>
    let currentScheduleId = null;

    function confirmDelete(schedule_id) {
        currentScheduleId = schedule_id;
        document.getElementById("confirmModal").style.display = "flex";
    }

    function hideModal() {
        document.getElementById("confirmModal").style.display = "none";
        currentScheduleId = null;
    }

    document.getElementById("confirmBtn").addEventListener("click", function() {
        if (!currentScheduleId) return;

        fetch("delete_schedule_ajax.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "schedule_id=" + currentScheduleId
        })
        .then(res => res.text())
        .then(response => {
            if (response.trim() === "success") {
                document.getElementById("row_" + currentScheduleId).remove();
                console.log(`%cSchedule ${currentScheduleId} deleted successfully.`, "color: green");
            } else {
                console.error(`%cFailed to delete schedule. Server said: ${response}`, "color: red");
            }
            hideModal();
        })
        .catch(err => {
            console.error(`%cError while deleting schedule: ${err}`, "color: red");
            hideModal();
        });
    });

    function filterTable() {
        const query = document.getElementById("searchBox").value.toLowerCase();
        const rows = document.querySelectorAll("#scheduleTable tbody tr");
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? "" : "none";
        });
    }
</script>

</body>
</html>
