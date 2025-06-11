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

$admin_id = $_SESSION['user_id'];

$birth_cert = '';
$phone = '';
$username = '';
$request_id = null;
$status = '';
$search_done = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search'])) {
        // Handle the search
        $birth_cert = trim($_POST['birth_certificate_number']);
        $phone = trim($_POST['phone']);

        $stmt = $conn->prepare("SELECT pr.request_id, su.user_name, pr.status 
                                FROM password_reset pr 
                                JOIN systemuser su ON pr.matched_user_ID  = su.user_ID 
                                WHERE pr.birth_certificate_number = ? AND pr.status = 'Pending'");
        $stmt->bind_param("s", $birth_cert);
        $stmt->execute();
        $result = $stmt->get_result();
        $search_done = true;

        if ($row = $result->fetch_assoc()) {
            $username = $row['user_name'];
            $request_id = $row['request_id'];
            $status = $row['status'];
        } else {
            $error = "No matching pending request found.";
        }
    }

    if (isset($_POST['update']) && isset($_POST['request_id'])) {
        $request_id = (int)$_POST['request_id'];
        $new_status = $_POST['status'];

        $update = $conn->prepare("UPDATE password_reset SET status = ?, approved_by = ? WHERE request_id= ?");
        $update->bind_param("sii", $new_status, $admin_id, $request_id);
        $update->execute();

        header("Location: admin_dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PulseScheduler - Update Password Reset</title>
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
            max-width: 500px;
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
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
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
        
        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }
        
        .btn {
            padding: 12px 20px;
            background: linear-gradient(90deg, var(--primary));
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
            border-left: 4px solid var(--primary);
        }
        
        .error {
            color: var(--danger);
            background-color: rgba(220, 53, 69, 0.1);
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
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
        
        .sent {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }
        
        .rejected {
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
            
            .form-container {
                padding: 0 15px;
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
            <h1 class="page-title">Update Password Reset Request</h1>
        </div>
        
        <div class="card">
            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="birth_certificate_number">Birth Certificate Number:</label>
                        <input type="text" name="birth_certificate_number" id="birth_certificate_number" required 
                               value="<?php echo htmlspecialchars($birth_cert); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="text" name="phone" id="phone" required 
                               value="<?php echo htmlspecialchars($phone); ?>">
                    </div>
                    
                    <button type="submit" name="search" class="btn">Search Request</button>
                </form>

                <?php if ($search_done): ?>
                    <?php if ($username): ?>
                        <div class="info-box">
                            <h3>Request Details</h3>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                            <p><strong>Current Status:</strong> 
                                <span class="status-badge <?php echo strtolower($status); ?>"><?php echo $status; ?></span>
                            </p>
                            
                            <form method="POST" style="margin-top: 20px;">
                                <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                                
                                <div class="form-group">
                                    <label for="status">Update Status:</label>
                                    <select name="status" id="status" required>
                                        <option value="Pending" <?php if ($status == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Sent">Sent</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
                                </div>
                                
                                <button type="submit" name="update" class="btn">Update Status</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="error"><?php echo $error; ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> PulseScheduler. All rights reserved.
    </footer>
</body>
</html>