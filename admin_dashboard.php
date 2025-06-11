<?php
session_start();

// Redirect to login page if not logged in as Admin
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "pulsescheduler");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin's Information
$admin_id = $_SESSION['user_id'];
$admin_query = "SELECT full_name, photo FROM systemuser WHERE user_ID = ?";
$stmt = $conn->prepare($admin_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_result = $stmt->get_result();
$admin_info = $admin_result->fetch_assoc();

// Doctor BMDC Verification Request
$doctor_verification_query = "
    SELECT d.request_ID, d.bmdc_number, d.request_date, d.status,
           s.full_name, s.photo
    FROM doctor_bmdc_verification d
    JOIN systemuser s ON d.user_ID = s.user_ID
    WHERE d.status = 'Pending'
    ORDER BY d.request_date DESC
";
$doctor_verification_result = $conn->query($doctor_verification_query);

// Password Reset Requests
$password_reset_query = "SELECT * FROM password_reset WHERE status = 'Pending' ORDER BY request_date DESC";
$password_reset_result = $conn->query($password_reset_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PulseScheduler - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #1e40af;
        --accent-color: #38bdf8;
        --light-bg: #f3f4f6;
        --white: #ffffff;
        --gray: #6b7280;
        --dark-text: #111827;
        --border-radius: 12px;
        --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
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
        line-height: 1.6;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .navbar {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
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
        font-size: 1.6rem;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo i {
        font-size: 1.8rem;
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
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .nav-links a:hover {
        background-color: rgba(255, 255, 255, 0.15);
    }

    .container {
        flex: 1;
        margin: 7rem auto 2rem;
        padding: 0 1rem;
        width: 95%;
        max-width: 1400px;
    }

    .header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        padding: 2rem;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .header::before {
        content: "";
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
    }

    .header img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        z-index: 1;
    }

    .header h2 {
        font-size: 1.7rem;
        font-weight: 600;
        z-index: 1;
    }

    .request-section {
        background-color: var(--white);
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: var(--transition);
    }

    .request-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }

    .request-section h3 {
        margin-bottom: 1rem;
        font-size: 1.4rem;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .request-section h3 i {
        font-size: 1.2rem;
    }

    .request-section .link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 1rem;
        color: var(--secondary-color);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
    }

    .request-section .link:hover {
        text-decoration: underline;
        color: var(--accent-color);
    }

    .request-table {
        width: 100%;
        border-collapse: collapse;
        overflow: hidden;
        border-radius: var(--border-radius);
    }

    .request-table th,
    .request-table td {
        padding: 0.9rem 1rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }

    .request-table th {
        background-color: #f1f5f9;
        color: #374151;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .request-table tr:hover {
        background-color: #f9fafb;
    }

    .request-table img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--accent-color);
    }

    .btn-update {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius);
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
        border: none;
        cursor: pointer;
    }

    .btn-update:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
    }

    .status-pending {
        color: #f59e0b;
        font-weight: 600;
    }

    footer {
        background: linear-gradient( var(--primary-color), var(--secondary-color));
        color: white;
        text-align: center;
        padding: 2rem 0;
        width: 100%;
        margin-top: auto;
    }

    .footer-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .footer-links {
        display: flex;
        gap: 1.5rem;
    }

    .footer-links a {
        color: white;
        text-decoration: none;
        transition: var(--transition);
    }

    .footer-links a:hover {
        color: var(--accent-color);
    }

    .social-icons {
        display: flex;
        gap: 1rem;
    }

    .social-icons a {
        color: white;
        font-size: 1.2rem;
        transition: var(--transition);
    }

    .social-icons a:hover {
        color: var(--accent-color);
        transform: translateY(-3px);
    }

    .copyright {
        margin-top: 1rem;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            text-align: center;
        }

        .nav-links {
            gap: 0.8rem;
        }

        .request-table th,
        .request-table td {
            padding: 0.7rem;
            font-size: 0.85rem;
        }

        .request-table {
            display: block;
            overflow-x: auto;
        }
    }
    </style>
</head>
<body>
<div class="navbar">
    <a href="admin_dashboard.php" class="logo">
        
        <span>PulseScheduler</span>
    </a>
    <div class="nav-links">
        <a href="doctor_verification_requests.php">
            
            <span>Doctor Verification</span>
        </a>
        <a href="password_reset_requests.php">
           
            <span>Password Reset</span>
        </a>
        <a href="logout_admin.php">
            
            <span>Logout</span>
        </a>
    </div>
</div>

<div class="container">
    <!-- Admin Profile -->
    <div class="header">
        <img src="<?php echo htmlspecialchars($admin_info['photo']??'default-admin.jpg'); ?>" alt="Admin Image">
        <h2>Welcome, <?php echo htmlspecialchars($admin_info['full_name']); ?></h2>
    </div>
    
    <!-- Doctor Verification Requests -->
    <div class="request-section">
        <h3> Doctor's BMDC Verification</h3>
        <a href="doctor_verification_requests.php" class="link">
            View all doctor verification requests
        </a>
        <table class="request-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Photo</th>
                    <th>BMDC Number</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $doctor_verification_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($row['photo']??'default-user.jpg'); ?>" alt="Doctor Photo"></td>
                        <td><?php echo htmlspecialchars($row['bmdc_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                        <td class="status-pending"><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="update_verification.php?id=<?php echo $row['request_ID']; ?>" class="btn-update">
                                
                                Update
                            </a>
                        </td>
                    </tr> 
                <?php endwhile; ?>
                <?php if ($doctor_verification_result->num_rows == 0): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No pending verification requests</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Password Reset Requests -->
    <div class="request-section">
        <h3><i class="fas fa-key"></i> Password Reset Requests</h3>
        <a href="password_reset_requests.php" class="link">
            View all password reset requests
        </a>
        <table class="request-table">
            <thead>
                <tr>
                    <th>Birth Certificate Number</th>
                    <th>Phone Number</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $password_reset_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['birth_certificate_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                        <td class="status-pending"><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="update_password_reset.php?id=<?php echo $row['request_id']; ?>" class="btn-update">
                                <i class="fas fa-edit"></i>
                                Update
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($password_reset_result->num_rows == 0): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No pending password reset requests</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
        <div class="copyright">
            &copy; 2025 PulseScheduler. All rights reserved.
        </div>
    </div>
</footer>
</body>
</html>