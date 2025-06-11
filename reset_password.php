<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $birth_certificate_number = $_POST['birth_certificate_number'];
    $new_password = $_POST['new_password'];

    $conn = new mysqli('localhost', 'root', '', 'pulsescheduler');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM systemuser WHERE user_name = '$username' AND birth_cirtificate_number = '$birth_certificate_number'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $sql_update = "UPDATE systemuser SET user_password = '$new_password' WHERE user_name = '$username'";
        if ($conn->query($sql_update) === TRUE) {
            $success_message = "Password updated successfully!";
        } else {
            $error_message = "Error updating password!";
        }
    } else {
        $error_message = "Invalid username or birth certificate number!";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PulseScheduler - Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--warning);
        }

        main {
            flex: 1;
            max-width: 600px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
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
                <li><a href="logout_admin.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="card">
            <h2>Reset User Password</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="birth_certificate_number">Birth Certificate Number:</label>
                    <input type="text" id="birth_certificate_number" name="birth_certificate_number" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <button type="submit">Reset Password</button>
            </form>

            <?php if (isset($error_message)) echo "<div class='error'>$error_message</div>"; ?>
            <?php if (isset($success_message)) echo "<div class='success'>$success_message</div>"; ?>
        </div>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> PulseScheduler. All rights reserved.
    </footer>
</body>
</html>
