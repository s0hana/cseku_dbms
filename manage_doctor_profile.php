<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

$user_ID = mysqli_real_escape_string($conn, $_SESSION['user_ID']);

// Fetch user details
$user_query = "SELECT * FROM systemuser WHERE user_ID='$user_ID'";
$user_result = mysqli_query($conn, $user_query);

if (!$user_result || mysqli_num_rows($user_result) != 1) {
    echo "User not found!";
    exit();
}
$user = mysqli_fetch_assoc($user_result);

//fetch doctor details
$doctor_query = "SELECT * FROM doctor WHERE user_ID='$user_ID'";
$doctor_result = mysqli_query($conn, $doctor_query);

if (!$doctor_result || mysqli_num_rows($doctor_result) != 1) {
    echo "User not found!";
    exit();
}
$doctor_info = mysqli_fetch_assoc($doctor_result);


// Prepare variables
$user_name     = $user['user_name'] ?? 'N/A';
$image_path    = !empty($user['photo']) ? $user['photo'] : 'default.png';
$name          = $user['full_name'] ?? 'N/A';
$specialization = $doctor_info['specialization']??'N/A';
$hospital_affiliation = $doctor_info['hospital_affiliation']??'N/A';
$experience_years = $doctor_info['experience_years']??'N/A';
$bio = $doctor_info['bio']??'N/A';
$total_rating = $doctor_info['total_rating']??'N/A';
$rating_count = $doctor_info['rating_count']??'N/A';
$max_consultation_duration = $doctor_info['max_consultation_duration']??'N/A';
$types_of_treatments = $doctor_info['types_of_treatments']??'N/A';
$qualifications = $doctor_info['qualifications']??'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Doctor Profile - PulseScheduler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #1e40af;
            --accent: #38bdf8;
            --light: #f9fafb;
            --gray: #6b7280;
            --dark: #111827;
            --white: #ffffff;
            --radius: 12px;
            --shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--dark);
        }
        .navbar {
            background: var(--primary);
            padding: 1rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }
        .logo {
            color: var(--white);
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 0.5rem;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .nav-links a:hover { background: rgba(255,255,255,0.15); }

        .container {
            max-width: 1100px;
            margin: 6.5rem auto 2rem;
            padding: 0 1rem;
        }
        .profile-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .profile-card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .profile-photo img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
        }
        .profile-info h2 { font-size: 1.75rem; margin-bottom: 0.3rem; }
        .profile-info p { font-size: 0.95rem; }

        .profile-details {
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .detail-group {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            font-weight: 600;
            color: var(--gray);
            margin-bottom: 0.3rem;
        }
        .detail-value {
            background: var(--light);
            padding: 0.9rem 1rem;
            border-left: 4px solid var(--accent);
            border-radius: var(--radius);
            font-size: 0.95rem;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: flex-end;
            padding: 1rem 2rem 2rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            border-radius: var(--radius);
            font-weight: 600;
            text-decoration: none;
            transition: background 0.3s, transform 0.2s;
        }
        .btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .profile-card-header { flex-direction: column; text-align: center; }
            .action-buttons { justify-content: center; }
        }
        footer {
            background: var(--primary);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        footer a {
            color: #ffc107;
            text-decoration: none;
            font-weight: 600;
        }
        footer a:hover { color: #e0a800; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="#" class="logo">PulseScheduler</a>
        <div class="nav-links">
            <a href="<?php echo $_SESSION['previous_page']; ?>">Back</a>
            <a href="works_in_chamber_list_doctor.php?user_ID=<?php echo $user_ID; ?>">My Chambers</a>
            <a href="works_in_schedule_list_doctor.php?user_ID=<?php echo $user_ID; ?>">My Schedules</a>
            <a href="compounder_list_doctor.php?user_ID=<?php echo $user_ID; ?>">My Compounders</a>
            <a href="add_compounder.php">Add Compounders</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-photo">
                    <img src="<?php echo $image_path; ?>" alt="Profile Photo">
                </div>
                <div class="profile-info">
                    <h1>Doctor Profile</h1>
                    
                    <h2><?php echo htmlspecialchars($user_name); ?></h2>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                    <h1><a href="today_schedule.php" style="color: white">Today's Schedule</a></h1>
                
                </div>
            </div>
            <div class="profile-details">
                <div class="detail-group">
                    <span class="detail-label">Specialization</span>
                    <span class="detail-value"><?php echo htmlspecialchars($specialization); ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Hospital Affiliation</span>
                    <span class="detail-value"><?php echo htmlspecialchars($hospital_affiliation); ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Experience Years</span>
                    <span class="detail-value"><?php echo htmlspecialchars($experience_years); ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Bio</span>
                    <span class="detail-value"><?php echo htmlspecialchars($bio); ?></span>
                </div>

                <div class="detail-group">
                    <span class="detail-label">Total Rating</span>
                    <span class="detail-value"><?php echo htmlspecialchars($total_rating); ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Number of Ratings</span>
                    <span class="detail-value"><?php echo htmlspecialchars($rating_count); ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Max Consultation Duration</span>
                    <span class="detail-value"><?php echo htmlspecialchars($max_consultation_duration); ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Qualifications</span>
                    <span class="detail-value"><?php echo htmlspecialchars($qualifications); ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Types of Treatments</span>
                    <span class="detail-value"><?php echo htmlspecialchars($types_of_treatments); ?></span>
                </div>
            </div>
            <div class="action-buttons">
                <a href="update_doctor_profile.php" class="btn">Update Information</a>
                <a href="add_chamber.php" class="btn">Add Chamber</a>
                <a href="add_schedule.php" class="btn">Add Schedule</a>
                <a href="add_unavailability.php" class="btn">Add Unavailability</a>
                <a href="manage_appointment.php?user_ID=<?php echo $user_ID; ?>"class="btn">Manage Appointments</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 PulseScheduler</a></p>
    </footer>
</body>
</html>
 