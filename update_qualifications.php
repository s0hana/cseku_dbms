<?php
session_start();

$host = "localhost";
$dbname = "pulsescheduler";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_ID = $_SESSION['user_ID'] ?? null;
    $qualification = $_POST['qualification'] ?? null;

    if ($user_ID && $qualification) {
        $stmt = $conn->prepare("UPDATE compounder SET qualification = ? WHERE user_ID = ?");
        $stmt->bind_param("si", $qualification, $user_ID);

        if ($stmt->execute()) {
            $successMessage = "Qualification updated successfully.";
        } else {
            $errorMessage = "Error updating qualification: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $errorMessage = "Invalid input.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Qualification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .form-container {
            max-width: 450px;
            background: white;
            padding: 30px 40px;
            margin: 50px auto;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        textarea {
            width: 90%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: vertical;
            min-height: 120px;
        }

        .message {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        button {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1em;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
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
            <li><a href="maanage_compounder_profile.php">Dashboard</a></li>
            <li><a href="my_doctors.php">My Doctors</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="form-container">
    <h2>Update Qualification</h2>

    <?php if ($successMessage): ?>
        <div class="message success"><?php echo $successMessage; ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="message error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form action="update_qualifications.php" method="POST">
        <label for="qualification">Qualification:</label>
        <textarea name="qualification" id="qualification" required placeholder="Enter your qualification..."></textarea>

        <button type="submit">Update</button>
    </form>
</div>

<footer>
    © 2025 PulseScheduler
</footer>

</body>
</html>
