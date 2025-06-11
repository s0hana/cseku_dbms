<?php
include 'db.php'; // DB connection

$doctorID = $_GET['doctor_ID'] ?? null;
$successMsg = $errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorID = $_POST['doctor_ID'];
    $rating = intval($_POST['rating']);

    if ($rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("UPDATE doctor SET total_rating = total_rating + ?, rating_count = rating_count + 1 WHERE user_ID = ?");
        $stmt->bind_param("ii", $rating, $doctorID);
        if ($stmt->execute()) {
            $successMsg = "Thank you for rating!";
        } else {
            $errorMsg = "Failed to submit rating.";
        }
    } else {
        $errorMsg = "Invalid rating. Please rate between 1 and 5.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rate Doctor</title>
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
            color: #FFC107;
        }

        .container {
            width: 350px;
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin: 60px auto 80px;
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        .rating-stars input {
            display: none;
        }

        .rating-stars label {
            font-size: 40px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .rating-stars input:checked ~ label,
        .rating-stars label:hover,
        .rating-stars label:hover ~ label {
            color: gold;
        }

        .submit-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }

        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Header + Navigation -->
    <header>
        <nav>
            <div class="logo">
                <h1>PulseScheduler</h1>
            </div>
            <ul class="nav-links">
                <li><a href="Home.php">Home</a></li>
                <li><a href="AboutUs.php">About Us</a></li>
                <li><a href="Contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Rating Form -->
    <div class="container">
        <h2>Rate This Doctor</h2>

        <?php if ($successMsg): ?>
            <p class="message"><?= $successMsg ?></p>
        <?php elseif ($errorMsg): ?>
            <p class="error"><?= $errorMsg ?></p>
        <?php endif; ?>

        <?php if (!$successMsg): ?>
        <form method="POST" action="rate_doctor.php?doctor_ID=<?= htmlspecialchars($doctorID) ?>">
            <input type="hidden" name="doctor_ID" value="<?= htmlspecialchars($doctorID) ?>">
            <div class="rating-stars">
                <input type="radio" name="rating" id="rate-5" value="5"><label for="rate-5">&#9733;</label>
                <input type="radio" name="rating" id="rate-4" value="4"><label for="rate-4">&#9733;</label>
                <input type="radio" name="rating" id="rate-3" value="3"><label for="rate-3">&#9733;</label>
                <input type="radio" name="rating" id="rate-2" value="2"><label for="rate-2">&#9733;</label>
                <input type="radio" name="rating" id="rate-1" value="1"><label for="rate-1">&#9733;</label>
            </div>
            <button type="submit" class="submit-btn">Submit Rating</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?= date("Y") ?> PulseScheduler. All rights reserved.</p>
    </footer>

</body>
</html>
