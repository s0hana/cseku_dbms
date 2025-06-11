<?php
session_start();
$doctor_ID = $_SESSION['user_ID']; // Ensure this is set after login

// Database connection
$host = 'localhost';
$db = 'pulsescheduler';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Handle deletion if form submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_compounder'])) {
        $compounder_ID = $_POST['compounder_ID'];
        $stmt = $pdo->prepare("DELETE FROM works_for WHERE doctor_ID = ? AND compounder_ID = ?");
        $stmt->execute([$doctor_ID, $compounder_ID]);
        header("Location: compounder_list_doctor.php"); // Refresh
        exit();
    }

    // Fetch compounders for doctor
    $stmt = $pdo->prepare("
        SELECT su.user_ID, su.full_name, su.photo, c.qualification,
               ch.chamber_name, ca.address_ID AS chamber_address_ID, ua.address_ID AS user_address_ID
        FROM works_for wf
        JOIN systemuser su ON wf.compounder_ID = su.user_ID
        JOIN compounder c ON su.user_ID = c.user_ID
        LEFT JOIN chamber ch ON c.chamber_ID = ch.chamber_ID
        LEFT JOIN chamber_address ca ON ch.chamber_ID = ca.chamber_ID
        LEFT JOIN user_address ua ON su.user_ID = ua.user_ID
        WHERE wf.doctor_ID = ?
    ");
    $stmt->execute([$doctor_ID]);
    $compounders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch related emails, phones, addresses
    $compounderEmails = [];
    $compounderPhones = [];
    $compounderAddresses = [];
    $chamberAddresses = [];

    foreach ($compounders as $comp) {
        $uid = $comp['user_ID'];

        // Emails
        $stmtEmail = $pdo->prepare("SELECT email FROM user_email WHERE user_ID = ?");
        $stmtEmail->execute([$uid]);
        $compounderEmails[$uid] = $stmtEmail->fetchAll(PDO::FETCH_COLUMN);

        // Phones
        $stmtPhone = $pdo->prepare("SELECT phone FROM user_phone WHERE user_ID = ?");
        $stmtPhone->execute([$uid]);
        $compounderPhones[$uid] = $stmtPhone->fetchAll(PDO::FETCH_COLUMN);

        // User Address
        if ($comp['user_address_ID']) {
            $stmtAddr = $pdo->prepare("SELECT * FROM user_address WHERE address_ID = ?");
            $stmtAddr->execute([$comp['user_address_ID']]);
            $addr = $stmtAddr->fetch(PDO::FETCH_ASSOC);
            $compounderAddresses[$uid] = implode(", ", array_filter([
                $addr['house_no'], $addr['road'], $addr['area'], $addr['thana'],
                $addr['district'], $addr['division'], $addr['postal_code']
            ]));
        }

        // Chamber Address
        if ($comp['chamber_address_ID']) {
            $stmtChAddr = $pdo->prepare("SELECT * FROM chamber_address WHERE address_ID = ?");
            $stmtChAddr->execute([$comp['chamber_address_ID']]);
            $chAddr = $stmtChAddr->fetch(PDO::FETCH_ASSOC);
            $chamberAddresses[$uid] = implode(", ", array_filter([
                $chAddr['house_no'], $chAddr['road'], $chAddr['area'], $chAddr['thana'],
                $chAddr['district'], $chAddr['division'], $chAddr['postal_code']
            ]));
        }
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Compounder List</title>
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

        main {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td img {
            height: 80px;
            border-radius: 6px;
        }

        button {
            padding: 6px 12px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #c82333;
        }

        .delete-msg {
            display: none;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
            font-weight: bold;
        }

        .delete-msg.success {
            background-color: #28a745;
            color: white;
        }

        .delete-msg.error {
            background-color: #dc3545;
            color: white;
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

<!-- Header -->
<header>
    <nav>
        <div class="logo">
            <h1>PulseScheduler</h1>
        </div>
        <ul class="nav-links">
            <li><a href="add_compounder.php">Add Compounder</a></li>
            <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<!-- Main -->
<main>
    <h2>My Compounders</h2>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Photo</th>
                <th>Full Address</th>
                <th>Email(s)</th>
                <th>Phone(s)</th>
                <th>Qualification</th>
                <th>Chamber Name</th>
                <th>Chamber Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($compounders)): ?>
                <?php foreach ($compounders as $comp): ?>
                    <tr>
                        <td><?= htmlspecialchars($comp['full_name']) ?></td>
                        <td>
                            <?php if ($comp['photo']): ?>
                                <img src="<?= htmlspecialchars($comp['photo']) ?>" alt="Photo">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($compounderAddresses[$comp['user_ID']] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars(implode(", ", $compounderEmails[$comp['user_ID']])) ?></td>
                        <td><?= htmlspecialchars(implode(", ", $compounderPhones[$comp['user_ID']])) ?></td>
                        <td><?= htmlspecialchars($comp['qualification'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($comp['chamber_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($chamberAddresses[$comp['user_ID']] ?? 'N/A') ?></td>
                        <td>
                            <button onclick="openDialog(<?= $comp['user_ID'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9">No compounders assigned.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<!-- Dialog for Delete Confirmation -->
<dialog id="deleteDialog">
    <form method="POST" id="deleteForm">
        <p>Are you sure you want to remove this compounder?</p>
        <input type="hidden" name="compounder_ID" id="compounder_ID">
        <div style="margin-top: 20px; text-align: center;">
            <button type="submit" name="delete_compounder" style="background-color: #ef4444; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin-right: 10px;">Yes, Delete</button>
            <button type="button" onclick="closeDialog()" style="background-color: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 5px;">Cancel</button>
        </div>
    </form>
</dialog>

<!-- Footer -->
<footer>
    &copy; <?= date("Y") ?> PulseScheduler. All rights reserved.
</footer>

<!-- Script -->
<script>
function openDialog(compounderId) {
    document.getElementById('compounder_ID').value = compounderId;
    document.getElementById('deleteDialog').showModal();
}

function closeDialog() {
    document.getElementById('deleteDialog').close();
}
</script>

</body>
</html>
