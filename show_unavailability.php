<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

$user_ID = mysqli_real_escape_string($conn, $_SESSION['user_ID']);

// Get doctor's ID
$doctor_query = "SELECT * FROM doctor WHERE user_ID='$user_ID'";
$doctor_result = mysqli_query($conn, $doctor_query);

if (!$doctor_result || mysqli_num_rows($doctor_result) != 1) {
    echo "Doctor not found!";
    exit();
}

$doctor = mysqli_fetch_assoc($doctor_result);
$doctor_id = $doctor['user_ID'];

$success_msg = '';
$error_msg = '';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $unavailability_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $delete_query = "DELETE FROM unavailability WHERE unavailability_id='$unavailability_id' AND doctor_id='$doctor_id'";

    if (mysqli_query($conn, $delete_query)) {
        $success_msg = "Unavailability record deleted successfully.";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}

// Fetch all unavailability records for the doctor
$query = "SELECT * FROM unavailability WHERE doctor_id='$doctor_id' ORDER BY start_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Show Unavailability - PulseScheduler</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background: #f9fafb;
            color: #111827;
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
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        h2 {
            margin-bottom: 1rem;
            color: #1e3a8a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #2563eb;
            color: white;
        }
        .btn {
            padding: 0.5rem 1rem;
            background-color: #e11d48;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #9b1c2f;
        }
        .message {
            margin-top: 1rem;
            padding: 0.75rem;
            border-radius: 8px;
        }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #991b1b; }
        .back-link {
            display: block;
            margin-top: 2rem;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            color: #2563eb;
        }
        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }
        /* Modal (Dialog Box) Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        /* Search Box */
        .search-box {
            margin-bottom: 1rem;
            text-align: center;
        }
        .search-box input {
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            width: 200px;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <div class="logo">
            <h1 >PulseScheduler</h1>
        </div>
        <ul class="nav-links">
            <li><a href="add_unavailability.php">Add Unavailability</a></li>
            <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2 style="text-align: center;">Your Unavailability Records</h2>

    <?php if ($success_msg): ?>
        <div class="message success"><?php echo $success_msg; ?></div>
    <?php elseif ($error_msg): ?>
        <div class="message error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Search Box -->
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search by Start Date" onkeyup="filterRecords()">
    </div>

    <table id="unavailabilityTable">
        <thead>
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo date("d/m/Y", strtotime($row['start_date'])); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($row['end_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td>
                            <button class="btn" onclick="confirmDelete(<?php echo $row['unavailability_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No unavailability records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<!-- Modal (Confirmation Dialog) -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p>Are you sure you want to delete this record?</p>
        <button class="btn" id="confirmDeleteBtn">Yes</button>
        <button class="btn" onclick="closeModal()">No</button>
    </div>
</div>
<br><br><br><br>
<footer>© 2025 PulseScheduler</footer>

<script>
    let currentDeleteId = null;

    // Open the modal dialog
    function confirmDelete(unavailabilityId) {
        currentDeleteId = unavailabilityId;
        document.getElementById('confirmationModal').style.display = 'block';
    }

    // Close the modal dialog
    function closeModal() {
        document.getElementById('confirmationModal').style.display = 'none';
    }

    // Handle the delete confirmation
    document.getElementById('confirmDeleteBtn').onclick = function() {
        if (currentDeleteId) {
            window.location.href = '?delete_id=' + currentDeleteId;
        }
    };

    // Filter table records based on search input
    function filterRecords() {
        let input = document.getElementById('searchInput');
        let filter = input.value.toLowerCase();
        let table = document.getElementById('unavailabilityTable');
        let tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName('td')[0];
            if (td) {
                let textValue = td.textContent || td.innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>

</body>
</html>
