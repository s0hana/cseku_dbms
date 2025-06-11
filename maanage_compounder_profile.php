<?php
session_start();
include('db.php');
$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

$user_ID = mysqli_real_escape_string($conn, $_SESSION['user_ID']);

// Fetch basic user info
$query = "SELECT * FROM systemuser WHERE user_ID='$user_ID'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) != 1) {
    echo "User not found!";
    exit();
}
$user = mysqli_fetch_assoc($result);

// Fetch compounder info
$compounder_query = "
    SELECT *
    FROM compounder
    WHERE user_ID = '$user_ID'
";
$compounder_result = mysqli_query($conn, $compounder_query);
$compounder_r = mysqli_fetch_assoc($compounder_result);
//fetch compounder chamber
$compounder_chamber_query = " SELECT
  c.user_ID AS compounder_id,
  c.qualification,a.chamber_ID,
  a.house_no,
  a.road,
  a.area,
  a.thana,
  a.district,
  a.division,
  a.postal_code,
  GROUP_CONCAT(DISTINCT ce.email) AS emails,
  GROUP_CONCAT(DISTINCT cp.phone) AS phones
FROM compounder c
LEFT JOIN chamber_address a ON c.chamber_ID = a.chamber_ID
LEFT JOIN chamber_email ce ON c.chamber_ID = ce.chamber_ID
LEFT JOIN chamber_phone cp ON c.chamber_ID = cp.chamber_ID
WHERE c.user_ID = $user_ID  
GROUP BY c.user_ID, a.chamber_ID;
";
$compounder_chamber_result = mysqli_query($conn, $compounder_chamber_query);
$compounder_cr = mysqli_fetch_assoc($compounder_chamber_result);

$chamber_name_result = "SELECT c.chamber_ID, c.chamber_name
FROM chamber c
JOIN compounder comp ON comp.chamber_ID = c.chamber_ID
WHERE comp.user_ID = $user_ID
";
$chamber_name_result = mysqli_query($conn, $chamber_name_result);
$chamber_name_rt = mysqli_fetch_assoc($chamber_name_result);
// Prepare variables with safe defaults
$user_name = $user['user_name'] ?? 'N/A';
$image_path = !empty($user['photo']) ? $user['photo'] : 'default.png';
$name = $user['full_name'] ?? 'N/A';
$qualification = $compounder_r['qualification'] ?? 'N/A';
$phone = $compounder_cr['phones']??'N/A';
$email = $compounder_cr['emails'] ?? 'N/A';
$house_no = $compounder_cr['house_no']??'N/A';
$road_no = $compounder_cr['road']??'N/A';
$area = $compounder_cr['area']??'N/A';
$thana = $compounder_cr['thana']??'N/A';
$district = $compounder_cr['district']??'N/A';
$division = $compounder_cr['division']??'N/A';
$postal_code = $compounder_cr['postal_code']??'N/A';
$chamber_name = $chamber_name_rt['chamber_name']??'N/A';


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Compounder Profile - PulseScheduler</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #38bdf8;
            --light-bg: #f9fafb;
            --white: #ffffff;
            --gray: #6b7280;
            --dark-text: #111827;
            --border-radius: 12px;
            --box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
        }

        .navbar {
            background: var(--primary-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
            box-shadow: var(--box-shadow);
        }

        .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.logout {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .container {
            max-width: 1100px;
            margin: 6.5rem auto 2rem;
            padding: 0 1rem;
        }

        .profile-card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .profile-card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
    display: block; 
}


        .profile-info h2 {
            font-size: 1.75rem;
            margin-bottom: 0.3rem;
        }

        .profile-info p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

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
            background: var(--light-bg);
            padding: 0.9rem 1rem;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--accent-color);
            font-size: 0.95rem;
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 2rem 2rem;
        }

        .btn {
            padding: 0.75rem 1.6rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-left: 0.5rem;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .profile-card-header {
                flex-direction: column;
                text-align: center;
            }

            .nav-links {
                flex-direction: column;
                align-items: flex-end;
            }

            .nav-links a {
                padding: 0.3rem 0.6rem;
            }

            .action-buttons {
                justify-content: center;
            }
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
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="#" class="logo">PulseScheduler</a>
        <div class="nav-links">
            <a href="compounder_profile.php">Back</a>
            <a href="chamber_list_compounder.php">Chambers</a>
            <a href="doctor_list.php">Doctors</a>
            <a href="AboutUs.php">About</a>
            <a href="Contact.php">Contact</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-photo">
                <img src="<?php echo $image_path; ?>" alt="Profile Photo">
                </div>
                <div class="profile-info">
                    <h1>Compounder Profile</h1>
                    <h2><?php echo $user_name; ?></h2>
                    <p><strong>Name:</strong> <?php echo $name; ?></p>
                </div>
            </div>
            <div class="profile-details">
                <div class="detail-group">
                    <span class="detail-label">My Qualifications</span>
                    <span class="detail-value"><?php echo $qualification; ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Chamber Name</span>
                    <span class="detail-value"><?php echo $chamber_name; ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Chamber Phone(s)</span>
                    <span class="detail-value"><?php echo $phone; ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Chamber Email(s)</span>
                    <span class="detail-value"><?php echo $email; ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Chamber Address</span>
                    <span class="detail-value">House no: <?php echo $house_no; ?> Road no: <?php echo $road_no; ?> Area: <?php echo $area; ?> Thana: <?php echo $thana; ?> District: <?php echo $district; ?> Division: <?php echo $division; ?> Postal Code: <?php echo $postal_code; ?></span>
                </div>
            </div>
            <div class="action-buttons">
                <a href="update_qualifications.php" class="btn">Update My Qualifications</a>
                <a href="my_doctors.php" class="btn">My Doctors</a>
                <a href="add_my_work_place.php" class="btn">Add/Remove Work Place</a>
                <a href="manage_appointments_by_compounder.php" class="btn">Manage Appointments</a>
                <a href="manage_billing_by_compounder.php" class="btn">Manage Billing</a>
                
            </div>
        </div>
    </div>
    <footer id="footer">
        <p>&copy; 2025 Pulsescheduler</p>
    </footer>
</body>
</html>
