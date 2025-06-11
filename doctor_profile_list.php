<?php
session_start();
include('db.php');
$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];

if (!isset($_GET['id'])) {
    header("Location: doctor_list.php");
    exit();
}

$user_ID = $_GET['id'];

// Fetch doctor details
$sql = "SELECT 
            systemuser.user_ID,
            systemuser.full_name,
            systemuser.user_name,
            systemuser.gender,
            systemuser.photo,
            doctor.bmdc_registration_number,
            doctor.specialization,
            doctor.hospital_affiliation,
            doctor.experience_years,
            doctor.bio,
            doctor.total_rating,
            doctor.rating_count,
            doctor.types_of_treatments,
            (doctor.total_rating / NULLIF(doctor.rating_count, 0)) AS average_rating,
            doctor.max_consultation_duration,
            doctor.qualifications
        FROM doctor
        JOIN systemuser ON doctor.user_ID = systemuser.user_ID
        WHERE systemuser.user_ID = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();

    // Fetch chamber information
    $chamber_sql = "SELECT 
                        chamber.chamber_name, 
                        CONCAT_WS(', ',
                            CONCAT('House No: ', IF(addr.house_no IS NULL OR addr.house_no = '', 'N/A', addr.house_no)),
                            CONCAT('Road No: ', IF(addr.road IS NULL OR addr.road = '', 'N/A', addr.road)),
                            CONCAT('Area: ', IF(addr.area IS NULL OR addr.area = '', 'N/A', addr.area)),
                            CONCAT('Thana: ', IF(addr.thana IS NULL OR addr.thana = '', 'N/A', addr.thana)),
                            CONCAT('District: ', IF(addr.district IS NULL OR addr.district = '', 'N/A', addr.district)),
                            CONCAT('Division: ', IF(addr.division IS NULL OR addr.division = '', 'N/A', addr.division)),
                            CONCAT('Postal Code: ', IF(addr.postal_code IS NULL OR addr.postal_code = '', 'N/A', addr.postal_code))
                        ) AS location,
                        chamber.opening_time,
                        chamber.working_days,
                        chamber.closing_time
                    FROM works_in
                    JOIN chamber ON works_in.chamber_ID = chamber.chamber_ID
                    JOIN chamber_address AS addr ON addr.chamber_ID = chamber.chamber_ID
                    WHERE works_in.doctor_ID = ?";
                    
    $chamber_stmt = $conn->prepare($chamber_sql);
    $chamber_stmt->bind_param("i", $user_ID);
    $chamber_stmt->execute();
    $chamber_result = $chamber_stmt->get_result();

    $chamberData = [];
    if ($chamber_result && $chamber_result->num_rows > 0) {
        while ($row = $chamber_result->fetch_assoc()) {
            $chamberData[] = $row;
        }
    }
}

// Fetch phone numbers
$phone_query = "SELECT GROUP_CONCAT(phone SEPARATOR ', ') AS contact_number FROM user_phone WHERE user_ID = ?";
$phone_stmt = $conn->prepare($phone_query);
$phone_stmt->bind_param("i", $user_ID);
$phone_stmt->execute();
$phone_result = $phone_stmt->get_result();
$phone = $phone_result->fetch_assoc();

// Fetch email addresses
$email_query = "SELECT GROUP_CONCAT(email SEPARATOR ', ') AS email FROM user_email WHERE user_ID = ?";
$email_stmt = $conn->prepare($email_query);
$email_stmt->bind_param("i", $user_ID);
$email_stmt->execute();
$email_result = $email_stmt->get_result();
$email = $email_result->fetch_assoc();

// Fetch address
$address_query = "SELECT 
                    CONCAT_WS(', ',
                        CONCAT('House No: ', IF(addr.house_no IS NULL OR addr.house_no = '', 'N/A', addr.house_no)),
                        CONCAT('Road No: ', IF(addr.road IS NULL OR addr.road = '', 'N/A', addr.road)),
                        CONCAT('Area: ', IF(addr.area IS NULL OR addr.area = '', 'N/A', addr.area)),
                        CONCAT('Thana: ', IF(addr.thana IS NULL OR addr.thana = '', 'N/A', addr.thana)),
                        CONCAT('District: ', IF(addr.district IS NULL OR addr.district = '', 'N/A', addr.district)),
                        CONCAT('Division: ', IF(addr.division IS NULL OR addr.division = '', 'N/A', addr.division)),
                        CONCAT('Postal Code: ', IF(addr.postal_code IS NULL OR addr.postal_code = '', 'N/A', addr.postal_code))
                    ) AS full_address
                 FROM user_address AS addr
                 WHERE user_ID = ?";

$address_stmt = $conn->prepare($address_query);
$address_stmt->bind_param("i", $user_ID);
$address_stmt->execute();
$address_result = $address_stmt->get_result();
$address = $address_result->fetch_assoc();

$treatments = [];
                if (!empty($doctor['types_of_treatments'])) {
                    $treatments = explode("\n", $doctor['types_of_treatments']);
                }
?>


<!-- HTML starts here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Doctor Profile</title>
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
        .container_1 {
        max-width: 1100px;
        margin: 2rem auto 2rem;
        padding: 0 1rem;
    }

    .profile-card_1 {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        padding: 2rem;
    }

    h2 {
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
        color: var(--primary-color);
    }

    .chamber-info-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .chamber-info-table th, .chamber-info-table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .chamber-info-table th {
        background-color: #4CAF50;
        color: white;
    }

    .chamber-info-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .chamber-info-table tr:hover {
        background-color: #ddd;
    }

    .chamber-info-table td {
        font-size: 14px;
    }

    .no-chamber-info {
        color: #ff0000;
        font-weight: bold;
        margin-top: 20px;
    }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="#" class="logo">PulseScheduler</a>
        <div class="nav-links">
        <a href="doctor_list.php">Doctors</a>
        <a href="javascript:history.back()">Back</a>
        <a href="AboutUs.php">About Us</a>
        <a href="Contact.php">Contact</a>
        </div>
    </nav>

    <div class="container">
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-photo">
                    <img src="<?php echo htmlspecialchars($doctor['photo'] ?? ''); ?>" alt="Doctor Photo" class="profile-photo">
                </div>
                <div class="profile-info">
                    <h2 style="color: white;"><strong>Name:</strong> <?php echo htmlspecialchars($doctor['full_name'] ?? 'N/A'); ?></h2>
                    <p><strong>BMDC Registration Number:</strong> <?php echo htmlspecialchars($doctor['bmdc_registration_number'] ?? 'N/A'); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($doctor['gender'] ?? 'N/A'); ?></p>
                    <p><strong>Average Rating:</strong>⭐  <?php echo htmlspecialchars($doctor['average_rating'] ?? 'N/A'); ?></p>
                </div>
            </div>
            <div class="profile-details">
                <div class="detail-group"><span class="detail-label">Bio</span><span class="detail-value"><?php echo htmlspecialchars($doctor['bio'] ?? 'N/A'); ?></span></div>
                <div class="detail-group"><span class="detail-label">Address</span><span class="detail-value"><?php echo htmlspecialchars($address['full_address'] ?? 'N/A'); ?></span></div>
                <div class="detail-group"><span class="detail-label">Phone</span><span class="detail-value"><?php echo htmlspecialchars($phone['contact_number'] ?? 'N/A'); ?></span></div>
                <div class="detail-group"><span class="detail-label">Email</span><span class="detail-value"><?php echo htmlspecialchars($email['email'] ?? 'N/A'); ?></span></div>
                <div class="detail-group"><span class="detail-label">Specialization</span><span class="detail-value"><?php echo htmlspecialchars($doctor['specialization'] ?? 'N/A'); ?></span></div>
                <div class="detail-group"><span class="detail-label">Qualifications</span><span class="detail-value"><?php echo htmlspecialchars($doctor['qualifications'] ?? 'N/A'); ?></span></div>
                <div class="detail-group"><span class="detail-label">Hospital Affiliation</span><span class="detail-value"><?php echo htmlspecialchars($doctor['hospital_affiliation'] ?? 'N/A'); ?></span></div>
                <div class="detail-group">
                <span class="detail-label">Types of treatments</span>
                <span class="detail-value">
                    <?php 
                    if (!empty($treatments)) {
                        foreach ($treatments as $treatment) {
                            echo htmlspecialchars($treatment) . '<br>';
                        }
                    }
                    ?>
                </span>
            </div>
                <div class="detail-group"><span class="detail-label">Experience Years</span><span class="detail-value"><?php echo htmlspecialchars($doctor['experience_years'] ?? 'N/A'); ?></span></div>
                <div class="detail-group"><span class="detail-label">Max Consultation Time</span><span class="detail-value"><?php echo htmlspecialchars($doctor['max_consultation_duration'] ?? 'N/A'); ?></span></div>
            </div>
        </div>
    </div>

    <!-- Chamber Info -->
    <div class="container_1">
        <div class="profile-card_1">
            <h2>Chamber Information</h2>
            <?php if (!empty($chamberData)): ?>
                <table class="chamber-info-table">
                    <thead>
                        <tr>
                            <th>Chamber Name</th>
                            <th>Location</th>
                            <th>Schedule Day(s)</th>
                            <th>Opening Time</th>
                            <th>Closing Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chamberData as $chamber): ?>
                            <tr>
                                <td><?= htmlspecialchars($chamber['chamber_name']) ?></td>
                                <td><?= htmlspecialchars($chamber['location']) ?></td>
                                <td>
                                    <?php
                                    $working_days = json_decode($chamber['working_days'], true);
                                    if (!empty($working_days)) {
                                        echo htmlspecialchars(implode(', ', $working_days)); 
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($chamber['opening_time']) ?></td>
                                <td><?= htmlspecialchars($chamber['closing_time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-chamber-info">No chamber information available.</p>
            <?php endif; ?>
        </div>
    </div>

   

    <footer id="footer">
        <p>&copy; 2025 PulseScheduler | <a href="Contact.php">Contact Us</a></p>
    </footer>
</body>
</html>
