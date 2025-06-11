<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bmdc_number = $_POST['bmdc_number'];

    $conn = new mysqli('localhost', 'root', '', 'pulsescheduler');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM doctor WHERE registration_number = '$bmdc_number'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
        $doctor_name = $doctor['user_ID']; // Display actual doctor info as needed
    } else {
        $error_message = "BMDC number not found!";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PulseScheduler - Verify BMDC Number</title>
    <style>
        :root {
            --primary: #007BFF;
            --primary-light: #00C6FF;
            --secondary: #2c3e50;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e3f2fd;
            color: var(--dark);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
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
            padding: 15px 50px;
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
            color: var(--warning);
        }

        .main-container {
            max-width: 600px;
            width: 100%;
            margin: 30px auto;
            padding: 0 20px;
            flex: 1;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        h2 {
            text-align: center;
            color: var(--secondary);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--secondary);
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        button {
            padding: 12px 20px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            width: 100%;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .error {
            color: var(--danger);
            background-color: rgba(220, 53, 69, 0.1);
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            text-align: center;
        }

        .success {
            color: var(--success);
            background-color: rgba(40, 167, 69, 0.1);
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            text-align: center;
        }

        footer {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
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
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-container">
        <div class="card">
            <h2>Verify BMDC Number</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="bmdc_number">BMDC Number:</label>
                    <input type="text" id="bmdc_number" name="bmdc_number" required>
                </div>
                <button type="submit">Verify</button>
            </form>

            <?php if (isset($error_message)) echo "<div class='error'>$error_message</div>"; ?>
            <?php if (isset($doctor_name)) echo "<div class='success'>Doctor found. User ID: $doctor_name</div>"; ?>
        </div>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> PulseScheduler. All rights reserved.
    </footer>
</body>
</html>
