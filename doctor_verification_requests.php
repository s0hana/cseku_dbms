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
$bmdc_search = '';
$search_sql = '';
$params = [];
$types = '';

// Handle search input
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET['bmdc'])) {
    $bmdc_search = trim($_GET['bmdc']);
    $search_sql = "AND dvr.bmdc_number = ?";
    $params[] = $bmdc_search;
    $types .= 's';
}

// SQL query to get Approved/Rejected requests verified by this admin
$sql = "SELECT dvr.request_ID, dvr.status, dvr.bmdc_number, su.full_name, su.photo, dvr.request_date
        FROM doctor_bmdc_verification dvr
        JOIN systemuser su ON dvr.user_ID = su.user_ID
        WHERE dvr.admin_ID = ? AND dvr.status IN ('Verified', 'Rejected') 
        $search_sql
        ORDER BY dvr.request_date DESC";

$params = array_merge([$admin_id], $params);
$types = 'i' . $types;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PulseScheduler - Doctor Verification</title>
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
        }
        
        .search-box {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .search-input {
            flex: 1;
            max-width: 250px;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .search-input:focus {
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
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .table-responsive {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eaeaea;
        }
        
        th {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .verified {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }
        
        .rejected {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        .no-results {
            text-align: center;
            padding: 30px;
            color: var(--gray);
            font-style: italic;
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
            
            .search-box {
                flex-direction: column;
            }
            
            .search-input {
                max-width: 100%;
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
                <li><a href="logout_admin.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-container">
        <div class="page-header">
            <h1 class="page-title">Doctor Verification Requests</h1>
        </div>
        
        <div class="card">
            <form method="GET" class="search-box">
                <input type="text" name="bmdc" class="search-input" placeholder="Search by BMDC Number" value="<?php echo htmlspecialchars($bmdc_search); ?>">
                <button type="submit" class="btn">Search</button>
            </form>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Doctor Name</th>
                            <th>Photo</th>
                            <th>BMDC Number</th>
                            <th>Status</th>
                            <th>Request Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td>
              <img src="<?= htmlspecialchars($row['photo'] ?? '') ?>" alt="Photo" class="admin-table-img" style=" width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 1px solid #007bff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);">
            </td>
                                <td><?php echo htmlspecialchars($row['bmdc_number']); ?></td>
                                <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                                <td><?php echo date('F j, Y', strtotime($row['request_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($result->num_rows === 0): ?>
                            <tr>
                                <td colspan="4" class="no-results">
                                    No verified/rejected requests found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> PulseScheduler. All rights reserved.
    </footer>
</body>
</html>