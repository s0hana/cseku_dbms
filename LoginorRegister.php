<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PulseScheduler - Login & Register</title>
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

        /* Navigation Bar */
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

        /* Login & Register Section */
        .container {
            width: 320px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin: 40px auto; /* Reduced margin to move form up */
        }

        h2 {
            color: #007bff;
        }

        .btn {
            display: block;
            width: 80%;
            background: #007bff;
            color: white;
            padding: 10px;
            margin: 8px auto;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s, transform 0.2s;
        }

        .btn:hover {
            background: #0056b3;
            transform: scale(1.05);
        }
        footer {
            background: #007BFF;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }

        footer p {
            font-size: 16px;
        }

        footer a {
            color: #FFC107;
            text-decoration: none;
            font-weight: 600;
        }

        footer a:hover {
            color: #e0a800;
        }

        /* Responsive */
        @media (max-width: 500px) {
            .container {
                width: 100%;
                padding: 20px;
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
            <ul class="nav-links">
                <li><a href="Home.php">Home</a></li>
                <li><a href="AboutUs.php">About Us</a></li>
                <li><a href="Contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Login & Register Section -->
    <div class="container">
        <h2>PulseScheduler</h2>
        <a href="login.php" class="btn">Login</a>
        <a href="registration.php" class="btn">Register</a>
    </div>
    <br><br>
    <footer id="footer">
        <p>&copy; 2025 Pulsescheduler</p>
    </footer>
</body>
</html>
