<?php
// Include database connection
include('db.php');

// Fetch all chambers and related details
$query = "
    SELECT c.chamber_name, c.opening_time, c.closing_time, c.working_days, 
           a.house_no, a.road, a.area, a.thana, a.district, a.division, a.postal_code, 
           GROUP_CONCAT(DISTINCT cp.phone ORDER BY cp.phone SEPARATOR ', ') AS phones, 
           GROUP_CONCAT(DISTINCT ce.email ORDER BY ce.email SEPARATOR ', ') AS emails
    FROM chamber c
    LEFT JOIN chamber_address a ON c.chamber_ID = a.chamber_ID
    LEFT JOIN chamber_phone cp ON c.chamber_ID = cp.chamber_ID
    LEFT JOIN chamber_email ce ON c.chamber_ID = ce.chamber_ID
    GROUP BY c.chamber_ID, a.house_no, a.road, a.area, a.thana, a.district, a.division, a.postal_code
";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Function to format working days
function format_working_days($working_days) {
    $days = json_decode($working_days, true);
    return implode(', ', $days);
}

// Display the chamber list in a table
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamber List</title>
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fbff;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(90deg, #007BFF, #00C6FF);
            color: white;
            padding: 10px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 50px;
        }

        .logo h1 {
            font-size: 1.5rem;
            margin: 0;
            color: white;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            display: inline;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 1em;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #FFC107;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }

        th, td {
            padding: 10px 14px;
            border: 1px solid #ccc;
            text-align: left;
            vertical-align: top;
        }

        th {
    background-color: #007BFF; /* Blue color */
    color: white; /* White text */
    padding: 10px 14px;
    border: 1px solid #ccc;
    text-align: left;
    vertical-align: top;
}


        .delete-btn {
            background-color: transparent;
            border: none;
            color: red;
            font-weight: bold;
            cursor: pointer;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }

        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 25px 0;
            margin-top: auto;
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
                <li><a href="chamber_info_compounder.php">My Chamber</a></li>
                <li><a href="AboutUs.php">About Us</a></li>
                <li><a href="Contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
     
    <div class="container">
        <h2 class="page-title">Chamber List</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Chamber Name</th>
                        <th>Full Address</th>
                        <th>Phones</th>
                        <th>Emails</th>
                        <th>Opening Time</th>
                        <th>Closing Time</th>
                        <th>Working Days</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['chamber_name']; ?></td>
                        <td>
                            <?php 
                            echo $row['house_no'] . ', ' . $row['road'] . ', ' . $row['area'] . ', ' . $row['thana'] . ', ' . 
                                $row['district'] . ', ' . $row['division'] . ', ' . $row['postal_code']; 
                            ?>
                        </td>
                        <td><?php echo $row['phones']; ?></td>
                        <td><?php echo $row['emails']; ?></td>
                        <td><?php echo $row['opening_time']; ?></td>
                        <td><?php echo $row['closing_time']; ?></td>
                        <td><?php echo format_working_days($row['working_days']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> PulseScheduler. All rights reserved.
    </footer>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
