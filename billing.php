<?php
session_start();
// Include database connection
require_once('db.php'); // Update with your actual DB connection file

// Get appointment_ID (can be passed as a query parameter)
$appointment_ID = $_GET['appointment_ID']; // Get appointment ID from the URL or set it via other means

// Query to fetch billing details for a specific appointment from the billing_view
$query = "SELECT  *
            FROM view_billing_summary
            WHERE appointment_ID = ?"; // Filter by appointment_ID

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $appointment_ID); // Bind the appointment ID to the query
$stmt->execute();
$result = $stmt->get_result();

// Check if billing record is found for the specific appointment
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Details</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
        }

        header h1 {
            margin: 0;
        }

        header .buttons {
            display: flex;
            gap: 10px;
        }

        header .buttons button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        header .buttons button:hover {
            background-color: #218838;
        }

        footer {
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
            font-size: 14px;
        }

        .container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            max-width: 800px;
            width: 100%;
            margin: 40px auto;
        }

        h2, h3 {
            text-align: center;
            color: #333;
        }

        .billing-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .billing-details div {
            font-size: 16px;
        }

        .billing-details .heading {
            font-weight: bold;
            color: #007BFF;
        }

        .billing-details .data {
            color: #333;
        }

        .no-record {
            text-align: center;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <h1>Billing Information</h1>
    <div class="buttons">
        <button onclick="window.location.href='appointments.php'">Appointments</button>
    </div>
</header>

<div class="container">
    <?php
    if ($result->num_rows > 0) {
        // Fetch the row and display it
        $row = $result->fetch_assoc();
        
        echo '<div class="billing-details">';
        
        echo '<div class="heading">Appointment Date</div>';
        echo '<div class="data">' . htmlspecialchars($row['appointment_date']) . '</div>';
        
        echo '<div class="heading">Payment Status</div>';
        echo '<div class="data">' . htmlspecialchars($row['payment_status']) . '</div>';
        
        echo '<div class="heading">Consultation Fee</div>';
        echo '<div class="data">' . htmlspecialchars($row['consultation_fee']) . '</div>';
        
        echo '<div class="heading">Additional Fees</div>';
        echo '<div class="data">' . htmlspecialchars($row['additional_fees']) . '</div>';
        
        echo '<div class="heading">Discount</div>';
        echo '<div class="data">' . htmlspecialchars($row['discount']) . '</div>';
        
        echo '<div class="heading">Final Amount</div>';
        echo '<div class="data">' . htmlspecialchars($row['final_amount']) . '</div>';
        
        echo '</div>';
    } else {
        echo '<p class="no-record">No billing record found for this appointment.</p>';
    }

    $stmt->close();
    $conn->close();
    ?>
</div>
<br><br><br>
<footer>
    PulseScheduler &copy; 2025
</footer>

</body>
</html>
