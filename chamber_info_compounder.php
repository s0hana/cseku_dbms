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
$compounder_query = "SELECT * FROM compounder WHERE user_ID = '$user_ID'";
$compounder_result = mysqli_query($conn, $compounder_query);
$compounder_r = mysqli_fetch_assoc($compounder_result);

// Fetch compounder chamber details
$compounder_chamber_query = "
SELECT
  c.user_ID AS compounder_id,
  c.qualification,
  a.chamber_ID,
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

// Fetch chamber name
$chamber_name_query = "
SELECT c.chamber_ID, c.chamber_name
FROM chamber c
JOIN compounder comp ON comp.chamber_ID = c.chamber_ID
WHERE comp.user_ID = $user_ID
";
$chamber_name_result = mysqli_query($conn, $chamber_name_query);
$chamber_name_rt = mysqli_fetch_assoc($chamber_name_result);

// Prepare variables with safe defaults
$phone = $compounder_cr['phones'] ?? 'N/A';
$email = $compounder_cr['emails'] ?? 'N/A';
$house_no = $compounder_cr['house_no'] ?? 'N/A';
$road_no = $compounder_cr['road'] ?? 'N/A';
$area = $compounder_cr['area'] ?? 'N/A';
$thana = $compounder_cr['thana'] ?? 'N/A';
$district = $compounder_cr['district'] ?? 'N/A';
$division = $compounder_cr['division'] ?? 'N/A';
$postal_code = $compounder_cr['postal_code'] ?? 'N/A';
$chamber_name = $chamber_name_rt['chamber_name'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Compounder Profile</title>
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
        .logo h1, .logo {
            font-size: 1.8rem;
            margin: 0;
            color: white;
            text-decoration: none;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 24px;
            margin: 0;
            padding: 0;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.05em;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #FFC107;
        }
        .content {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 30px;
            flex: 1;
            text-align: center;
        }
        .content {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        flex: 1;
        padding: 50px 20px;
    }

    .profile-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 30px 50px;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
        text-align: left;
    }

    .profile-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .profile-item:last-child {
        border-bottom: none;
    }

    .profile-item label {
        font-weight: bold;
        color: #007bff;
    }

    .profile-item span {
        color: #333;
    }
        h2 {
            color: #007bff;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }
        .info {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        label {
            font-weight: bold;
            color: #34495e;
        }
        p {
            margin-top: 5px;
            margin-left: 10px;
        }
        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <a href="#" class="logo">PulseScheduler</a>
        <ul class="nav-links">
            <li><a href="add_my_work_place.php">Add Chamber</a></li>
            <li><a href="maanage_compounder_profile.php">Dashboard</a></li>
            <li><a href="AboutUs.php">About</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="content">
    <div class="profile-card">
        <div class="profile-item">
            <label>Name:</label>
            <span><?php echo htmlspecialchars($user['full_name']); ?></span>
        </div>
        <div class="profile-item">
            <label>Qualification:</label>
            <span><?php echo htmlspecialchars($compounder_r['qualification'] ?? 'N/A'); ?></span>
        </div>
        <div class="profile-item">
            <label>Chamber Name:</label>
            <span><?php echo htmlspecialchars($chamber_name); ?></span>
        </div>
        <div class="profile-item">
            <label>Chamber Address:</label>
            <span>
                <?php echo htmlspecialchars($house_no); ?>,
                <?php echo htmlspecialchars($road_no); ?>,
                <?php echo htmlspecialchars($area); ?>,
                <?php echo htmlspecialchars($thana); ?>,
                <?php echo htmlspecialchars($district); ?>,
                <?php echo htmlspecialchars($division); ?> - 
                <?php echo htmlspecialchars($postal_code); ?>
            </span>
        </div>
        <div class="profile-item">
            <label>Phone(s):</label>
            <span><?php echo htmlspecialchars($phone); ?></span>
        </div>
        <div class="profile-item">
            <label>Email(s):</label>
            <span><?php echo htmlspecialchars($email); ?></span>
        </div>
    </div>
</div>

<footer>
    &copy; 2025 PulseScheduler
</footer>

</body>
</html>
