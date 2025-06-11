<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Our Specialist Doctors</title>
  <style>
    :root {
      --primary: #0066cc;
      --primary-dark: #003d80;
      --secondary: #6c757d;
      --background: #f4f6f8;
      --card-bg: #ffffff;
      --shadow: rgba(0, 0, 0, 0.1);
      --text-dark: #212529;
      --accent: #20c997;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: var(--background);
      color: var(--text-dark);
    }

    header {
      background: linear-gradient(to right, var(--primary), var(--primary-dark));
      padding: 1rem 2rem;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px var(--shadow);
    }

    header h1 {
      font-size: 1.8rem;
      font-weight: bold;
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 1.5rem;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }

    .nav-links a:hover {
      color: var(--accent);
    }

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .search-bar {
      display: flex;
      justify-content: center;
      margin-bottom: 2rem;
    }

    .search-bar input {
      width: 100%;
      max-width: 400px;
      padding: 0.75rem 1rem;
      border: 1px solid var(--secondary);
      border-radius: 50px;
      outline: none;
      font-size: 1rem;
      transition: 0.3s;
    }

    .search-bar input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 8px var(--primary);
    }

    .doctor-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
    }

    .doctor-card {
      background: var(--card-bg);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 4px 12px var(--shadow);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 2rem 1.5rem;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .doctor-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 20px var(--shadow);
    }

    .doctor-photo {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid var(--accent);
      margin-bottom: 1rem;
    }

    .doctor-name {
      font-size: 1.5rem;
      color: var(--primary-dark);
      font-weight: bold;
      margin-bottom: 0.5rem;
    }

    .specialization {
      font-size: 1rem;
      color: var(--accent);
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .hospital-affiliation {
      font-size: 0.9rem;
      color: var(--secondary);
      margin-bottom: 1rem;
    }

    .doctor-details {
      font-size: 0.9rem;
      line-height: 1.5;
    }

    .rating {
      margin-top: 0.5rem;
      color: #f39c12;
      font-weight: 600;
    }

    footer {
      margin-top: 3rem;
      background: var(--primary-dark);
      color: white;
      text-align: center;
      padding: 1rem;
      font-size: 0.9rem;
    }
    .view-profile-btn {
    margin-top: 0.5rem;
    display: inline-block;
    padding: 0.5rem 1rem;
    background-color: var(--primary);
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-size: 0.7rem;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.view-profile-btn:hover {
    background-color: var(--primary-dark);
}

  </style>
</head>

<body>

<header>
  <h1>PulseScheduler</h1>
  <ul class="nav-links">
    <li><a href="Home.php">Home</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="contact.php">Contact</a></li>
    <li><a href="javascript:history.back()">Back</a></li>
  </ul>
</header>

<div class="container">

  <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 1.5rem;">Our Specialist Doctors</h2>

  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search doctor by name  or specialization...">
  </div>

  <div class="doctor-grid">
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pulsescheduler";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("
            SELECT 
                su.user_ID, 
                su.full_name, 
                su.photo, 
                d.specialization, 
                d.hospital_affiliation, 
                d.experience_years, 
                d.bio, 
                (d.total_rating / NULLIF(d.rating_count, 0)) AS average_rating
            FROM 
                doctor d
            JOIN 
                systemuser su ON d.user_ID = su.user_ID
        ");
        $stmt->execute();
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($doctors as $doctor) {
            $photo = !empty($doctor['photo']) ? htmlspecialchars($doctor['photo']) : 'default-doctor.jpg';
            echo '<div class="doctor-card">';
            echo '<img src="' . $photo . '" alt="' . htmlspecialchars($doctor['full_name']) . '" class="doctor-photo">';
            echo '<h2 class="doctor-name">' . htmlspecialchars($doctor['full_name']) . '</h2>';
            echo '<p class="specialization">' . htmlspecialchars($doctor['specialization'] ?? 'N/A') . '</p>';
            echo '<p class="hospital-affiliation">' . htmlspecialchars($doctor['hospital_affiliation'] ?? 'N/A') . '</p>';
            echo '<div class="doctor-details">';
            echo '<p>Experience: ' . htmlspecialchars($doctor['experience_years'] ?? 'N/A') . ' years</p>';
            echo '<p>' . htmlspecialchars($doctor['bio'] ?? 'N/A') . '</p>';
            if (!empty($doctor['average_rating'])) {
                echo '<p class="rating">⭐ ' . number_format($doctor['average_rating'], 1) . '/5</p>';
            }
            echo '<a href="doctor_profile_list.php?id=' . $doctor['user_ID'] . '" 
            class="view-profile-btn">View Profile</a>';

            echo '</div>';
            echo '</div>';
        }

    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
    $conn = null;
    ?>
  </div>

</div>

<footer>
    <p>&copy; 2025 PulseScheduler | <a href="Contact.php" style="color: white;">Contact Us</a></p>
</footer>

<script>
  document.getElementById("searchInput").addEventListener("keyup", function () {
  const filter = this.value.toLowerCase();
  const cards = document.querySelectorAll(".doctor-card");

  cards.forEach(card => {
    const name = card.querySelector(".doctor-name").textContent.toLowerCase();
    const specialization = card.querySelector(".specialization").textContent.toLowerCase();

    if (name.includes(filter) || specialization.includes(filter)) {
      card.style.display = "flex";
    } else {
      card.style.display = "none";
    }
  });
});

</script>

</body>
</html>
