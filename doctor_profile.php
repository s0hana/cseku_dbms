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

// Fetch phone numbers
$phone_query = "
    SELECT GROUP_CONCAT(user_phone.phone SEPARATOR ', ') AS contact_number
    FROM user_phone
    WHERE user_ID = '$user_ID'
";
$phone_result = mysqli_query($conn, $phone_query);
$phone = mysqli_fetch_assoc($phone_result);

// Fetch email addresses
$email_query = "
    SELECT GROUP_CONCAT(user_email.email SEPARATOR ', ') AS email
    FROM user_email
    WHERE user_ID = '$user_ID'
";
$email_result = mysqli_query($conn, $email_query);
$email = mysqli_fetch_assoc($email_result);
// address
$address_query = "
    SELECT 
        CONCAT_WS(', ',
            CONCAT('House No: ', IF(a.house_no IS NULL OR a.house_no = '', 'N/A', a.house_no)),
            CONCAT('Road No: ', IF(a.road IS NULL OR a.road = '', 'N/A', a.road)),
            CONCAT('Area: ', IF(a.area IS NULL OR a.area = '', 'N/A', a.area)),
            CONCAT('Thana: ', IF(a.thana IS NULL OR a.thana = '', 'N/A', a.thana)),
            CONCAT('District: ', IF(a.district IS NULL OR a.district = '', 'N/A', a.district)),
            CONCAT('Division: ', IF(a.division IS NULL OR a.division = '', 'N/A', a.division)),
            CONCAT('Postal Code: ', IF(a.postal_code IS NULL OR a.postal_code = '', 'N/A', a.postal_code))
        ) AS full_address
    FROM user_address as a
    WHERE user_ID = $user_ID;
";

$address_result = mysqli_query($conn, $address_query);
$address = mysqli_fetch_assoc($address_result);


// Prepare variables with safe defaults
$user_name = $user['user_name'] ?? 'N/A';
$image_path = !empty($user['photo']) ? $user['photo'] : 'default.png';
$name = $user['full_name'] ?? 'N/A';
$emails = $email['email'] ?? 'N/A';
$phones = $phone['contact_number'] ?? 'N/A';
$address1 = $address['full_address'] ?? 'N/A';
$role = $user['user_role'] ?? 'N/A';
$age = $user['birth_day'] ?? 'N/A';
$gender = $user['gender'] ?? 'N/A';
$medical_history = $user['medical_history'] ?? 'N/A';
$blood_group = $user['blood_group'] ?? 'N/A';
$bc_number = $user['birth_certificate_number'] ?? 'N/A';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>General Profile - PulseScheduler</title>
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
            gap: 1.5rem;
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
            margin-left: 1.5rem;
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
            <a href="doctor_list.php">Doctors</a>
            <a href="appointments.php">Appointments</a>
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
                    <h2><?php echo $user_name; ?></h2>
                    <p><strong>Name:</strong> <?php echo $name; ?></p>
                    <p><strong>Birth Certificate Number:</strong> <?php echo $bc_number; ?></p>
                    <p><strong>Birth day:</strong> <?php echo $age; ?></p>
                    <p><strong>Blood Group:</strong> <?php echo $blood_group; ?></p>
                    <p><strong>Gender:</strong> <?php echo $gender; ?></p>
                </div>
            </div>
            <div class="profile-details">
                <div class="detail-group">
                    <span class="detail-label">Address</span>
                    <span class="detail-value"><?php echo $address1; ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Medical History</span>
                    <span class="detail-value"><?php echo $medical_history; ?></span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Phones</span>
                    <span class="detail-value">
                    <?php
                $phone_query = "SELECT phone_ID, phone FROM user_phone WHERE user_ID = '$user_ID'";
                $phone_result = mysqli_query($conn, $phone_query);
                while ($row = mysqli_fetch_assoc($phone_result)) {
                echo "<div>{$row['phone']} 
                <a href='delete_phone.php?id={$row['phone_ID']}' class='delete-link' data-type='phone' data-value=\"{$row['phone']}\" data-url='delete_phone.php?id={$row['phone_ID']}'>🗑️</a>
                </div>";
                }
                ?>
</span>

                </div>
                <div class="detail-group">
                    <span class="detail-label">Emails</span>
                    <span class="detail-value">
                    <?php
$email_query = "SELECT email_ID, email FROM user_email WHERE user_ID = '$user_ID'";
$email_result = mysqli_query($conn, $email_query);
while ($row = mysqli_fetch_assoc($email_result)) {
    echo "<div>{$row['email']} 
        <a href='delete_email.php?id={$row['email_ID']}'
           class='delete-link'
           data-type='email'
           data-value=\"" . htmlspecialchars($row['email']) . "\"
           data-url='delete_email.php?id={$row['email_ID']}'>🗑️</a>
    </div>";
}
?>

</span>
                </div>
            </div>
            <div class="action-buttons">
                <a href="update_general_profile.php" class="btn">Update Profile</a>
                <a href="manage_doctor_profile.php" class="btn">Manage Job Profile</a>
                <a href="book_appointment.php" class="btn">Book Appointment</a>
                <a href="delete_general_profile.php" class="btn">Delete Profile</a>
                
            </div>
        </div>
    </div>

    <div id="confirmBox" style="display:none; position:fixed; top:40%; left:50%; transform:translate(-50%, -50%);
background:white; border:1px solid #ccc; padding:20px; z-index:9999; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">
    <p id="confirmMessage" style="color:red; margin-bottom:20px;"></p>
    <button id="confirmYes" style="margin-right:10px; background-color:red; color:white; padding:5px 10px; border:none;">Yes</button>
    <button id="confirmNo" style="background-color:gray; color:white; padding:5px 10px; border:none;">No</button>
</div>
<script>
document.querySelectorAll('.delete-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.dataset.url;
        const type = this.dataset.type;
        const value = this.dataset.value;

        const confirmBox = document.getElementById('confirmBox');
        const message = document.getElementById('confirmMessage');
        const yesBtn = document.getElementById('confirmYes');
        const noBtn = document.getElementById('confirmNo');

        message.textContent = `Are you sure you want to delete this ${type}: ${value}?`;

        confirmBox.style.display = 'block';

        yesBtn.onclick = () => {
            window.location.href = url;
        };
        noBtn.onclick = () => {
            confirmBox.style.display = 'none';
        };
    });
});
</script>
    <footer id="footer">
        <p>&copy; 2025 Pulsescheduler</p>
    </footer>
</body>
</html>
