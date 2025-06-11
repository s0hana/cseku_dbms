<?php
session_start();
require_once "db.php"; // db connection

$user_ID = $_SESSION['user_ID'] ?? null;
if (!$user_ID) {
    die("Access denied. Please log in.");
}

// old info
$stmt = $conn->prepare("SELECT * FROM doctor WHERE user_ID = ?");
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Show message from session (if any)
$update_message = $_SESSION['update_message'] ?? null;
$update_type = $_SESSION['update_type'] ?? null;
unset($_SESSION['update_message'], $_SESSION['update_type']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $specialization = !empty($_POST["specialization"]) ? $_POST["specialization"] : $doctor["specialization"];
    $hospital_affiliation = !empty($_POST["hospital_affiliation"]) ? $_POST["hospital_affiliation"] : $doctor["hospital_affiliation"];
    $experience_years = isset($_POST["experience_years"]) && $_POST["experience_years"] !== '' ? $_POST["experience_years"] : $doctor["experience_years"];
    $bio = !empty($_POST["bio"]) ? $_POST["bio"] : $doctor["bio"];
    $qualifications = !empty($_POST["qualifications"]) ? $_POST["qualifications"] : $doctor["qualifications"];
    $types_of_treatments = !empty($_POST["types_of_treatments"]) ? $_POST["types_of_treatments"] : $doctor["types_of_treatments"];
    $max_duration = isset($_POST["max_consultation_duration"]) && $_POST["max_consultation_duration"] !== '' ? $_POST["max_consultation_duration"] : $doctor["max_consultation_duration"];

    $update_stmt = $conn->prepare("UPDATE doctor SET specialization=?, hospital_affiliation=?, experience_years=?, bio=?, qualifications=?, types_of_treatments=?, max_consultation_duration=? WHERE user_ID=?");
    $update_stmt->bind_param("ssisssii", $specialization, $hospital_affiliation, $experience_years, $bio, $qualifications, $types_of_treatments, $max_duration, $user_ID);

    if ($update_stmt->execute()) {
        $_SESSION['update_message'] = "✅ Profile updated successfully!";
        $_SESSION['update_type'] = "success";
    } else {
        $_SESSION['update_message'] = "❌ Update failed. Try again.";
        $_SESSION['update_type'] = "error";
    }

    header("Location: update_doctor_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Doctor Profile | PulseScheduler</title>
    <style>
        /* CSS Variables */
        :root {
            --primary: #007BFF;
            --primary-light: #00C6FF;
            --secondary: #2c3e50;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
            --transition: all 0.3s ease;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f8fafc;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header Styles */
        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            width: 30px;
            height: 30px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0;
            position: relative;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .nav-links a:hover {
            color: var(--warning);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--warning);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-icon {
            width: 18px;
            height: 18px;
        }

        /* Main Content Styles */
        main {
            flex: 1;
            padding: 2rem 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .form-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            padding: 2.5rem;
            margin-top: 1rem;
            border: 1px solid #e2e8f0;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 1.75rem;
            color: var(--secondary);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .form-header p {
            color: var(--gray);
            font-size: 0.95rem;
        }

        /* Back Link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: #0056b3;
            transform: translateX(-3px);
        }

        .back-icon {
            width: 16px;
            height: 16px;
        }

        /* Messages */
        .message {
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .error {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .message-icon {
            width: 18px;
            height: 18px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition);
            background-color: #f8fafc;
        }

        .form-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            background-color: white;
        }

        textarea.form-input {
            min-height: 80px;
            resize: vertical;
        }

        /* Form Grid Layout */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            width: 20%;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            background-color: #0069d9;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .form-actions {
    margin-top: 0;
    grid-column: 1 / -1;
    display: flex;
    justify-content: flex-end;
    padding-right: 1rem; 
}

        /* Footer Styles */
        footer {
            background-color: var(--primary);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            text-align: center;
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--warning);
        }

        .copyright {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            .nav-links {
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .form-container {
                padding: 1.5rem;
            }
            
            .form-header h2 {
                font-size: 1.5rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 1rem;
            }
            
            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }
            
            .form-container {
                padding: 1rem;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>PulseScheduler</h1>
            </div>
            <ul class="nav-links">
                <li><a href="doctor_profile.php">My Profile</a></li>
                <li><a href="today_schedule.php">Today's Schedule</a></li>
                <li><a href="manage_doctor_profile.php">Dashboard</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2>Update Your Profile</h2>
                    <p>Keep your professional information up to date</p>
                </div>

                <!-- Back Button -->
                <a href="manage_doctor_profile.php" class="back-link">
                    <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back to Dashboard
                </a>

                <?php if ($update_message): ?>
                    <div class="message <?= $update_type ?>">
                        <?= $update_message ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="form-grid">
                    <!-- Column 1 -->
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <input type="text" id="specialization" name="specialization" class="form-input" 
                               value="<?= htmlspecialchars($doctor["specialization"] ?? '') ?>" 
                               placeholder="Cardiology, Neurology, etc.">
                    </div>

                    <div class="form-group">
                        <label for="hospital_affiliation">Hospital Affiliation</label>
                        <input type="text" id="hospital_affiliation" name="hospital_affiliation" class="form-input" 
                               value="<?= htmlspecialchars($doctor["hospital_affiliation"] ?? '') ?>" 
                               placeholder="Hospital or Clinic name">
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-input" 
                                  placeholder="Tell patients about you"><?= htmlspecialchars($doctor["bio"] ?? '') ?></textarea>
                    </div>

                    <!-- Column 2 -->
                    <div class="form-group">
                        <label for="experience_years">Years of Experience</label>
                        <input type="number" id="experience_years" name="experience_years" class="form-input" 
                               value="<?= htmlspecialchars($doctor["experience_years"] ?? '') ?>" 
                               placeholder="5">
                    </div>

                    <div class="form-group">
                        <label for="max_consultation_duration">Max Consultation Duration (Minutes)</label>
                        <input type="number" id="max_consultation_duration" name="max_consultation_duration" class="form-input" 
                               value="<?= htmlspecialchars($doctor["max_consultation_duration"] ?? '') ?>" 
                               placeholder="30">
                    </div>

                    <!-- Column 3 -->
                    <div class="form-group">
                        <label for="qualifications">Qualifications</label>
                        <textarea id="qualifications" name="qualifications" class="form-input" 
                                  placeholder="MBBS, FCPS, MD\nSpecialized Training in..."><?= htmlspecialchars($doctor["qualifications"] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="types_of_treatments">Types of Treatments</label>
                        <textarea id="types_of_treatments" name="types_of_treatments" class="form-input" 
                                  placeholder="Cardiac Surgery\nNeurological Consultation\n..."><?= htmlspecialchars($doctor["types_of_treatments"] ?? '') ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <p class="copyright">© 2023 PulseScheduler. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>