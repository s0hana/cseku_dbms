<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

$doctor_ID = mysqli_real_escape_string($conn, $_SESSION['user_ID']);
$message = '';
$message_color = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $birth_certificate = mysqli_real_escape_string($conn, $_POST['birth_certificate']);

    // Step 1: Check if compounder exists
    $check_user = "SELECT * FROM systemuser WHERE birth_certificate_number = '$birth_certificate'";
    $user_result = mysqli_query($conn, $check_user);

    if (!$user_result || mysqli_num_rows($user_result) != 1) {
        $message = "No user found with this birth certificate number.";
        $message_color = "red";
    } else {
        $compounder_user = mysqli_fetch_assoc($user_result);
        $compounder_ID = $compounder_user['user_ID'];

        // Step 2: Check if already a compounder for this doctor
        $check_existing = "SELECT * FROM works_for WHERE doctor_ID = '$doctor_ID' AND compounder_ID = '$compounder_ID'";
        $existing_result = mysqli_query($conn, $check_existing);

        if (mysqli_num_rows($existing_result) > 0) {
            $message = "This compounder is already added to your list.";
            $message_color = "red";
        } else {
            // Step 3: Insert into compounder table if not exists
            $check_compounder_table = "SELECT * FROM compounder WHERE user_ID = '$compounder_ID'";
            $compounder_result = mysqli_query($conn, $check_compounder_table);

            if (mysqli_num_rows($compounder_result) == 0) {
                $insert_compounder = "INSERT INTO compounder (user_ID) VALUES ('$compounder_ID')";
                mysqli_query($conn, $insert_compounder);
            }

            // Step 4: Insert into works_for
            $insert_relation = "INSERT INTO works_for (doctor_ID, compounder_ID) VALUES ('$doctor_ID', '$compounder_ID')";
            if (mysqli_query($conn, $insert_relation)) {
                $message = "Compounder added successfully!";
                $message_color = "green";
            } else {
                $message = "Failed to add compounder. Try again.";
                $message_color = "red";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Compounder - PulseScheduler</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #333;
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

        .form-container {
            width: 400px;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin: 50px auto;
            text-align: center;
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: left;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        button {
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            font-size: 1em;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            text-align: center;
            margin-top: 1rem;
            font-weight: bold;
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
        <li><a href="compounder_list_doctor.php">My Compounders</a></li>
        <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="form-container">
    <h2>Add a Compounder</h2>
    <form method="POST">
        <label for="birth_certificate">Compounder's Birth Certificate Number:</label>
        <input type="text" name="birth_certificate" id="birth_certificate" required>

        <button type="submit">Add Compounder</button>
    </form>

    <?php if (!empty($message)): ?>
        <p class="message" style="color: <?php echo $message_color; ?>;"><?php echo $message; ?></p>
    <?php endif; ?>
</div>

<footer>© 2025 Pulsescheduler</footer>

</body>
</html>