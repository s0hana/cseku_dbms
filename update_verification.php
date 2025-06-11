<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "pulsescheduler");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$admin_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];

    // Step 1: Get the verification request info
    $stmt = $conn->prepare("SELECT user_ID, bmdc_number FROM doctor_bmdc_verification WHERE request_ID = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $verification = $result->fetch_assoc();

    if ($verification) {
        $doctor_user_id = $verification['user_ID'];
        $bmdc_number = $verification['bmdc_number'];

        // Step 2: Update the verification status and admin_ID
        $update_stmt = $conn->prepare("UPDATE doctor_bmdc_verification SET status = ?, admin_ID = ? WHERE request_ID = ?");
        $update_stmt->bind_param("sii", $new_status, $admin_id, $request_id);
        $update_stmt->execute();

        // Step 3: If Verified, insert or update doctor table
        if ($new_status === 'Verified') {
            $check_doctor = $conn->prepare("SELECT * FROM doctor WHERE user_ID = ?");
            $check_doctor->bind_param("i", $doctor_user_id);
            $check_doctor->execute();
            $doctor_result = $check_doctor->get_result();

            if ($doctor_result->num_rows === 0) {
                $insert_doc = $conn->prepare("INSERT INTO doctor (user_ID, bmdc_registration_number) VALUES (?, ?)");
                $insert_doc->bind_param("is", $doctor_user_id, $bmdc_number);
                $insert_doc->execute();
            } else {
                $doc_update = $conn->prepare("UPDATE doctor SET bmdc_registration_number = ? WHERE user_ID = ?");
                $doc_update->bind_param("si", $bmdc_number, $doctor_user_id);
                $doc_update->execute();
            }
        }

        // Redirect to dashboard after update
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid request ID.";
    }
} else {
    // Fetch the request for display
    $stmt = $conn->prepare("SELECT dvr.*, su.full_name 
                           FROM doctor_bmdc_verification dvr
                           JOIN systemuser su ON dvr.user_ID = su.user_ID
                           WHERE dvr.request_ID = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PulseScheduler - Update Doctor Verification</title>
    <style>
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e3f2fd;
            color: var(--dark);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        header {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
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
            padding: 15px 50px;
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
            color: var(--warning);
        }
        
        .main-container {
            max-width: 1200px;
            width: 100%;
            margin: 30px auto;
            padding: 0 20px;
            flex: 1;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(0, 123, 255, 0.2);
        }
        
        .page-title {
            color: var(--secondary);
            font-size: 1.8rem;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--secondary);
        }
        
        .form-value {
            padding: 10px;
            background-color: var(--light);
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }
        
        .btn {
            padding: 12px 20px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            width: 100%;
            margin-top: 15px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-verify {
            background: linear-gradient(90deg, var(--success), #5cb85c);
            margin-bottom: 20px;
        }
        
        .error {
            color: var(--danger);
            background-color: rgba(220, 53, 69, 0.1);
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .verified {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }
        
        .canceled {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        footer {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }
        
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                padding: 15px;
            }
            
            .nav-links {
                margin-top: 15px;
            }
            
            .card {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>PulseScheduler</h1>
            </div>
            <ul class="nav-links">
                <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-container">
        <div class="page-header">
            <h1 class="page-title">Update Doctor Verification</h1>
        </div>
        
        <div class="card">
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php elseif (isset($request)): ?>
                <div class="form-group">
                    <label>Doctor Name:</label>
                    <div class="form-value"><?php echo htmlspecialchars($request['full_name']); ?></div>
                </div>
                
                <div class="form-group">
                    <label>BMDC Number:</label>
                    <div class="form-value"><?php echo htmlspecialchars($request['bmdc_number']); ?></div>
                </div>
                
                <div class="form-group">
                    <label>Current Status:</label>
                    <div class="form-value">
                        <span class="status-badge <?php echo strtolower($request['status']); ?>">
                            <?php echo $request['status']; ?>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                <!-- External verify button -->
                <a href="https://verify.bmdc.org.bd/" target="_blank" class="btn btn-verify">
                    Verify on BMDC Website
                </a>
                </div>
                <form method="POST">
                    <div class="form-group">
                        <label for="status">Update Status:</label>
                        <select name="status" id="status" required>
                            <option value="Pending" <?php if ($request['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="Verified" <?php if ($request['status'] == 'Verified') echo 'selected'; ?>>Verified</option>
                            <option value="Canceled" <?php if ($request['status'] == 'Canceled') echo 'selected'; ?>>Canceled</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Update Status</button>
                </form>
            <?php else: ?>
                <div class="error">Request not found.</div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> PulseScheduler. All rights reserved.
    </footer>
</body>
</html>