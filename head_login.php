<?php
session_start();

$error = '';
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
            if ($user['role'] !== 'Head') {
                $error = "You do not have the required access level (Head) to log in.";
            } elseif (password_verify($password, $user['user_password'])) {
                $_SESSION['user_id'] = $user['user_ID'];
                $_SESSION['username'] = $user['user_name'];
                header("Location: head_dashboard.php");
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
  <title>PulseScheduler | Head Admin Login</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #e6f0ff, #ffffff);
      display: flex;
      flex-direction: column;
    }

    header, footer {
      background: #007bff;
      color: white;
      padding: 15px 0;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    header h1 {
      margin: 0;
      font-size: 26px;
    }

    footer {
      font-size: 14px;
      margin-top: auto;
      box-shadow: 0 -2px 6px rgba(0,0,0,0.1);
    }

    main {
      flex: 1;
      padding: 40px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    form {
      max-width: 400px;
      width: 100%;
      padding: 25px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    form h2 {
      text-align: center;
      color: #007bff;
      margin-bottom: 20px;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-top: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background: #007bff;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #0056b3;
    }

    .error-msg {
      background-color: #ffe5e5;
      color: red;
      padding: 10px 15px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 15px;
      font-weight: bold;
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
    }

    .logo h1 {
      margin: 0;
      font-size: 28px;
      font-weight: 700;
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 25px;
      margin: 0;
      padding: 0;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .nav-links a:hover {
      color: #FFC107;
    }
  </style>
</head>
<body>

<header>
  <nav>
    <div class="logo">
      <h1>PulseScheduler</h1>
    </div>
    
  </nav>
</header>

<main>
  <form method="POST">
    <h2>🔐 Head Admin Login</h2>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
</main>

<footer>
  <p>&copy; 2025 PulseScheduler. All rights reserved.</p>
</footer>

</body>
</html>
