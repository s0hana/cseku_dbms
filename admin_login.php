<?php
session_start();

$error = ''; // Initialize error message variable
$conn = new mysqli("localhost", "root", "", "pulsescheduler");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT user_ID, user_name, user_password, role FROM systemuser WHERE user_name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($user['role'] !== 'Admin') {
                $error = "You do not have the required access level (Admin) to log in.";
            } elseif (password_verify($password, $user['user_password'])) {
                $_SESSION['user_id'] = $user['user_ID'];
                $_SESSION['username'] = $user['user_name'];
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Username not found.";
        }

        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PulseScheduler - Admin Login</title>
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
            color: #FFC107;
        }

        .container {
            width: 320px;
            background: white;
            padding: 25px 40px 25px 27px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin: 40px auto;
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn {
            display: block;
            width: 105%;
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            text-align: center;
            transition: background 0.3s, transform 0.2s;
        }

        .btn:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        .error {
            color: red;
            margin-bottom: 15px;
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

    <!-- Navigation Menu -->
    <header>
        <nav>
            <div class="logo">
                <h1>PulseScheduler</h1>
            </div>
        </nav>
    </header>

    <!-- Admin Login Form -->
    <div class="container">
        <h2>Admin Login</h2>

        <!-- Display error message if it is set -->
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button class="btn" type="submit">Login</button>
        </form>
    </div>
    <footer>© 2025 Pulsescheduler</footer>
</footer>
</body>
</html>