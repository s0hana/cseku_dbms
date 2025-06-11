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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $start_date_raw = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date_raw = mysqli_real_escape_string($conn, $_POST['end_date']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    // Convert dd/mm/yyyy to yyyy-mm-dd
    $start_date_parts = explode('/', $start_date_raw);
    $end_date_parts = explode('/', $end_date_raw);

    if (count($start_date_parts) === 3 && count($end_date_parts) === 3) {
        $start_date = $start_date_parts[2] . '-' . $start_date_parts[1] . '-' . $start_date_parts[0];
        $end_date = $end_date_parts[2] . '-' . $end_date_parts[1] . '-' . $end_date_parts[0];
    } else {
        $error_msg = "Please enter dates in dd/mm/yyyy format.";
    }

    if (!$start_date || !$end_date) {
        $error_msg = "Start and end dates are required.";
    } elseif ($start_date > $end_date) {
        $error_msg = "End date must be after or equal to start date.";
    } else {
        $query = "INSERT INTO unavailability (doctor_id, start_date, end_date, reason)
                  VALUES ('$doctor_id', '$start_date', '$end_date', '$reason')";
        if (mysqli_query($conn, $query)) {
            $success_msg = "Unavailability added successfully.";
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Unavailability - PulseScheduler</title>
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
            max-width: 600px;
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

        label {
            display: block;
            margin: 1rem 0 0.5rem;
            font-weight: 600;
        }

        input, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
        }

        textarea { resize: vertical; }

        .btn {
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background: #2563eb;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn:hover { background: #1e40af; }

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
    </style>
</head>
<body>

<header>
    <nav>
        <div class="logo">
            <h1>PulseScheduler</h1>
        </div>
        <ul class="nav-links">
            <li><a href="show_unavailability.php">See Unavailabilities</a></li>
            <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2 style="text-align: center;">Add Unavailability</h2>

    <?php if ($success_msg): ?>
        <div class="message success"><?php echo $success_msg; ?></div>
    <?php elseif ($error_msg): ?>
        <div class="message error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="start_date">Start Date</label>
        <input type="text" name="start_date" id="start_date" placeholder="dd/mm/yyyy" required>

        <label for="end_date">End Date</label>
        <input type="text" name="end_date" id="end_date" placeholder="dd/mm/yyyy" required>

        <label for="reason">Reason (optional)</label>
        <textarea name="reason" id="reason" rows="4" placeholder="e.g., personal leave, vacation, conference"></textarea>

        <button type="submit" class="btn">Add Unavailability</button>
    </form>

</div>

<footer>© 2025 Pulsescheduler</footer>

</body>
</html>
