<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - PulseScheduler</title>
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

        /* About Us Section */
        section.about-us {
            padding: 50px;
            text-align: center;
            background: #ffffff;
            color: #333;
        }

        section.about-us h2 {
            font-size: 2em;
            margin-bottom: 20px;
        }

        section.about-us p {
            font-size: 1em;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            text-align: left;
        }

        /* Footer */
        footer {
            background:linear-gradient(90deg, #007BFF, #00C6FF);
            color: white;
            text-align: center;
            padding: 25px;
            font-size: 1em;
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
                <li><a href="javascript:history.back()">Back</a></li>
                <li><a href="doctor_list.php">Doctors</a></li>
                <li><a href="Contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- About Us Section -->
    <section class="about-us">
        <h2>About Us</h2>
        <p>
        Our mission is to create easy and improved access to healthcare for users. 
        We are a team that believes technology and innovation can make healthcare services 
        more effective and affordable. On our website, you can find skilled and experienced doctors 
        who genuinely care about your health.
        </p>
        <p>
        We believe that taking care of your health should be easy and affordable
        and we are working toward that goal.
        </p>
        <p>
        We’d love to hear from you! Whether you have questions, feedback or need assistance, feel free to reach out to us.
        </p>
    </section>

    <!-- Footer -->
     <br><br>
    <footer>
        <p>PulseScheduler. All Rights Reserved.</p>
    </footer>

</body>
</html>
