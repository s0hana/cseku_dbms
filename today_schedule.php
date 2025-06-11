<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_ID'])) {
    echo "Please login as a doctor.";
    exit;
}

$doctor_ID = $_SESSION['user_ID'];
$today = date('Y-m-d');

$sql = "
SELECT 
    a.serial_no,
    a.appointment_start_time,
    a.appointment_end_time,
    
    -- Patient info
    p.full_name AS patient_name,
    p.photo AS patient_photo,
    p.medical_history,
    GROUP_CONCAT(DISTINCT pp.phone SEPARATOR ', ') AS patient_phones,

    -- Compounder info
    c.full_name AS compounder_name,
    c.photo AS compounder_photo,
    GROUP_CONCAT(DISTINCT cp.phone SEPARATOR ', ') AS compounder_phones,

    -- Chamber info
    ch.chamber_name,
    CONCAT_WS(', ', ad.house_no, ad.road, ad.area, ad.thana, ad.district, ad.division, ad.postal_code) AS full_address,
    GROUP_CONCAT(DISTINCT chp.phone SEPARATOR ', ') AS chamber_phones

FROM appointment a

LEFT JOIN systemuser p ON a.user_ID = p.user_ID
LEFT JOIN user_phone pp ON p.user_ID = pp.user_ID

LEFT JOIN systemuser c ON a.compounder_ID = c.user_ID
LEFT JOIN user_phone cp ON c.user_ID = cp.user_ID

LEFT JOIN chamber ch ON a.chamber_ID = ch.chamber_ID
LEFT JOIN chamber_address ad ON ch.chamber_ID = ad.chamber_ID
LEFT JOIN chamber_phone chp ON ch.chamber_ID = chp.chamber_ID

WHERE a.doctor_ID = ? AND a.appointment_date = ?

GROUP BY a.appointment_ID
ORDER BY a.serial_no ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $doctor_ID, $today);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Today's Appointments - PulseScheduler</title>
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
    text-align: center; /*this will center the h2 */
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
        }
    
        table {
    width: 100%;
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
    border: 1px solid #ddd;
    padding: 18px 20px;
    text-align: left;
    vertical-align: top;
    word-wrap: break-word;
}
    
        th {
            background-color:rgb(13, 131, 250);
            color: white;
        }
    
        img {
            max-width: 100px;
            height: auto;
            border-radius: 6px;
            margin-top: 8px;
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
            <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="manage_appointment.php">Manage Appointments</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="content">
    <h2>Today's Appointments</h2>

    <input type="text" id="searchBox" placeholder="Search Chamber Name..." style="padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px;">
<div id="resultBox" style="margin-top: 20px;"></div>


    <table>
        <thead>
            <tr>
                <th>Serial</th>
                <th>Time</th>
                <th>Patient</th>
                <th>Compounder</th>
                <th>Chamber</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr><td colspan="5">No appointments for today.</td></tr>
            <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['serial_no']??'N/A') ?></td>
                        <td><?= htmlspecialchars($row['appointment_start_time']??'N/A') ?> - <?= htmlspecialchars($row['appointment_end_time']) ?></td>

                        <td>
                            <strong><?= htmlspecialchars($row['patient_name']??'N/A') ?></strong><br>
                            <img src="<?= htmlspecialchars($row['patient_photo']??'') ?>" alt="Patient"><br>
                            <small><?= htmlspecialchars($row['patient_phones']??'N/A') ?></small><br>
                            <small><?= htmlspecialchars($row['medical_history']??'N/A') ?></small>
                        </td>

                        <td>
                            <?php if($row['compounder_name']!=null): ?>
                            <strong><?= htmlspecialchars($row['compounder_name']??'N/A') ?></strong><br>
                            <img src="<?= htmlspecialchars($row['compounder_photo']??'') ?>" alt="Compounder"><br>
                            <small><?= htmlspecialchars($row['compounder_phones']??'N/A') ?></small>
                            <?php else: ?>
                                <small>Not Assigned Yet</small>
                            <?php endif; ?>
        
                        </td>

                        <td>
                            <strong><?= htmlspecialchars($row['chamber_name']??'N/A') ?></strong><br>
                            <?= htmlspecialchars($row['full_address']??'N/A') ?><br>
                            <small><?= htmlspecialchars($row['chamber_phones']??'N/A') ?></small>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('searchBox').addEventListener('input', function() {
    let query = this.value.trim().toLowerCase(); // Get the search query
    let table = document.querySelector('table'); // Get the table
    let rows = table.querySelectorAll('tbody tr'); // Get all rows in the table body

    // Loop through all rows and hide/show based on the search query
    rows.forEach(row => {
        let chamberCell = row.querySelector('td:nth-child(5)'); // Get the chamber cell (column 5)
        let chamberName = chamberCell.textContent.trim().toLowerCase();

        // If the chamber name contains the query, show the row; otherwise, hide it
        if (chamberName.includes(query)) {
            row.style.display = ''; // Show row
        } else {
            row.style.display = 'none'; // Hide row
        }
    });
});
</script>



<footer>
    &copy; <?= date('Y') ?> PulseScheduler. All rights reserved.
</footer>

</body>
</html>
