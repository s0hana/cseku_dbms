<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PulseScheduler - Doctor Appointment Management System</title>
  <style>
    /* General Styles */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      color: #333;
      line-height: 1.6; /* protiti line er height hobe font*1.6 */
    }

    /* Navigation Bar */
    header {
      background: linear-gradient(90deg, #007BFF, #00C6FF);
      color: white;
      padding: 15px 20px; /* top-bottom left-right */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

    .btn {
      background: #FFC107;
      color: #333;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #e0a800;
    }

    /* Hero Section */
    .hero {
      background: linear-gradient(90deg, #007BFF, #00C6FF);
      color: white;
      text-align: center;
      padding: 100px 20px;
      clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
    }

    .hero-content h1 {
      font-size: 48px;
      margin-bottom: 20px;
      animation: fadeIn 1.5s ease-in-out;
    }

    .hero-content p {
      font-size: 20px;
      margin-bottom: 30px;
      animation: fadeIn 2s ease-in-out;
    }

    .hero-content .btn {
      animation: fadeIn 2.5s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Features Section */
    .features {
      padding: 80px 20px;
      text-align: center;
      background: #f9f9f9;
    }

    .features h2 {
      font-size: 36px;
      margin-bottom: 40px;
      color: #007BFF;
    }

    .feature-cards {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }

    .card {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      width: 300px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .card h3 {
      font-size: 24px;
      margin-bottom: 15px;
      color: #007BFF;
    }

    .card p {
      font-size: 16px;
      color: #666;
    }

    /* Footer */
    footer {
      background: #007BFF;
      color: white;
      text-align: center;
      padding: 20px;
      margin-top: 50px;
    }

    footer a {
      color: #FFC107;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    footer a:hover {
      color: #e0a800;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .nav-links {
        flex-direction: column;
        gap: 10px;
      }

      .hero-content h1 {
        font-size: 36px;
      }

      .hero-content p {
        font-size: 18px;
      }

      .feature-cards {
        flex-direction: column;
        align-items: center;
      }

      .card {
        width: 90%;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <header>
    <nav>
      <div class="logo">
        <h1>PulseScheduler</h1>
      </div>
      <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="doctor_list.php">Doctors</a></li>
        <li><a href="AboutUs.php">About Us</a></li>
        <li><a href="Contact.php">Contact</a></li>
        <li><a href="#features">Features</a></li>
        <li><a href="LoginorRegister.php" class="btn">Login/Register</a></li>
      </ul>
    </nav>
  </header>

  <!-- Hero Section -->
  <section id="home" class="hero">
    <div class="hero-content">
      <h1>The best doctor for you—just a tap away!</h1>
      <p>Tap to book now</p>
      <a href="LoginorRegister.php" class="btn">Get Started</a>
    </div>
  </section>

  <!-- Features Section -->
  <section id="features" class="features">
    <h2>Why Choose PulseScheduler?</h2>
    <div class="feature-cards">
      <div class="card">
        <h3>Easy Booking</h3>
        <p>Book appointments with your preferred doctors in just a few clicks.</p>
      </div>
      <div class="card">
        <h3>Top Doctors</h3>
        <p>Browse the best-rated doctors.</p>
      </div>
      <div class="card">
        <h3>24/7 Access</h3>
        <p>Access your appointments anytime, anywhere.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="contact">
    <p>&copy; <span id="year"></span> PulseScheduler. All rights reserved.</p>
  </footer>
  <script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>