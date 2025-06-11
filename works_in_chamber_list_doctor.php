<?php
include 'db.php'; // Include your DB connection
session_start();

$doctor_id = $_SESSION['user_ID']; // The logged-in doctor's ID

$sql = "
SELECT 
    c.chamber_ID,
    c.chamber_name,
    c.opening_time,
    c.closing_time,
    c.working_days,
    a.house_no,
    a.road,
    a.area,
    a.thana,
    a.district,
    a.division,
    a.postal_code,
    GROUP_CONCAT(DISTINCT cp.phone SEPARATOR ', ') AS phones,
    GROUP_CONCAT(DISTINCT ce.email SEPARATOR ', ') AS emails
FROM 
    works_in w
JOIN 
    chamber c ON w.chamber_ID = c.chamber_ID
LEFT JOIN 
    chamber_address a ON c.chamber_ID = a.chamber_ID
LEFT JOIN 
    chamber_phone cp ON c.chamber_ID = cp.chamber_ID
LEFT JOIN 
    chamber_email ce ON c.chamber_ID = ce.chamber_ID
WHERE 
    w.doctor_ID = ?
GROUP BY 
    c.chamber_ID
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Chambers</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: your central CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fbff;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(90deg, #007BFF, #00C6FF);
            color: white;
            padding: 10px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 15px;
        }

        .logo h1 {
            font-size: 1.5rem;
            margin: 0;
            color: white;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            display: inline;
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
            max-width: 1100px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }

        th, td {
            padding: 10px 14px;
            border: 1px solid #ccc;
            text-align: left;
            vertical-align: top;
        }

        th {
    background-color: #007BFF; /* Blue color */
    color: white; /* White text */
    padding: 10px 14px;
    border: 1px solid #ccc;
    text-align: left;
    vertical-align: top;
}


        .delete-btn {
            background-color: transparent;
            border: none;
            color: red;
            font-weight: bold;
            cursor: pointer;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }

        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 25px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- ✅ Updated Header with Navigation Menu -->
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

<div class="container">
<?php if (isset($_GET['msg'])): ?>
    <p class="message" style="color: <?= strpos($_GET['msg'], 'successfully') !== false ? 'green' : 'red' ?>;">
        <?= htmlspecialchars($_GET['msg']) ?>
    </p>
<?php endif; ?>

    <h2>List of Chambers I Work at</h2>
    <table>
        <thead>
            <tr>
                <th>Chamber Name</th>
                <th>Full Address</th>
                <th>Phones</th>
                <th>Emails</th>
                <th>Opening Time</th>
                <th>Closing Time</th>
                <th>Working Days</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['chamber_name']) ?></td>
                <td>
                    <?= implode(', ', array_filter([
                        $row['house_no'],
                        $row['road'],
                        $row['area'],
                        $row['thana'],
                        $row['district'],
                        $row['division'],
                        $row['postal_code']
                    ])) ?>
                </td>
                <td><?= htmlspecialchars($row['phones']) ?></td>
                <td><?= htmlspecialchars($row['emails']) ?></td>
                <td><?= date('h:i A', strtotime($row['opening_time'])) ?></td>
                <td><?= date('h:i A', strtotime($row['closing_time'])) ?></td>
                <td><?= implode(', ', json_decode($row['working_days'], true)) ?></td>
                <td>
                <form method="POST" action="delete_schedule.php" class="delete-form">
  <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
  <input type="hidden" name="chamber_id" value="<?= $row['chamber_ID'] ?>">
  <button type="button" class="delete-btn">Delete</button>
</form>

                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>



<div id="confirmModal" class="modal" style="
  display: none;
  position: fixed;
  z-index: 999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
">
  <div class="modal-content" style="
    background-color: #fff;
    padding: 20px;
    margin: 15% auto;
    border-radius: 8px;
    width: 300px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  ">
    <p style="
      font-size: 16px;
      margin-bottom: 20px;
      color: #333;
    ">
      Are you sure you want to stop working in this chamber?
    </p>
    <button id="confirmYes" style="
      background-color: #d9534f;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 10px 16px;
      margin-right: 10px;
      cursor: pointer;
    ">Yes</button>
    <button id="confirmNo" style="
      background-color: #ccc;
      color: black;
      border: none;
      border-radius: 4px;
      padding: 10px 16px;
      cursor: pointer;
    ">Cancel</button>
  </div>
</div>


<script>
document.querySelectorAll('.delete-btn').forEach(button => {
  button.addEventListener('click', function() {
    const form = this.closest('form');
    const modal = document.getElementById('confirmModal');
    modal.style.display = 'block';

    document.getElementById('confirmYes').onclick = function() {
      modal.style.display = 'none';
      form.submit();
    };

    document.getElementById('confirmNo').onclick = function() {
      modal.style.display = 'none';
    };
  });
});
</script>

<footer>
    &copy; <?= date('Y') ?> PulseScheduler. All rights reserved.
</footer>

</body>
</html>
