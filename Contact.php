<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

$messageSent = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // SMTP server setup
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pulsescheduler@gmail.com';     
        $mail->Password   = 'zedy bnxq omjy hofg';       
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Email content
        $mail->setFrom($email, $name);
        $mail->addAddress('pulsescheduler@gmail.com');       

        $mail->isHTML(false);
        $mail->Subject = "New message from $name";
        $mail->Body    = "Name: $name\nEmail: $email\nMessage:\n$message";

        $mail->send();
        $messageSent = "✅ Your message has been sent successfully!";
    } catch (Exception $e) {
        $messageSent = "❌ Message could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - PulseScheduler</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: #f0f8ff;
            margin: 0;
            padding: 0;
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
            width: 50%;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input, textarea {
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            margin-top: 15px;
            text-align: center;
            font-size: 1.1em;
            color: green;
        }
        .error {
            color: red;
        }
        footer {
            background: #0056b3;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 1em;
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
                <li><a href="javascript:history.back()">Back</a></li>
                <li><a href="doctor_list.php">Doctors</a></li>
                <li><a href="AboutUs.php">About Us</a></li>
            </ul>
        </nav>
    </header>

<div class="container">
    <h2>Contact Us ✅</h2>

    <?php if (!empty($messageSent)): ?>
        <div class="message <?php echo (strpos($messageSent, '❌') !== false) ? 'error' : ''; ?>">
            <?php echo $messageSent; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="5" required></textarea>

        <button type="submit">Send</button>
    </form>
</div>
<footer>
        <p>PulseScheduler. All Rights Reserved.</p>
    </footer>
</body>
</html>
