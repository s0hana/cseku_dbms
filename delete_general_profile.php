<?php
session_start();
require 'db.php';

$user_ID = $_SESSION['user_ID'] ?? null;

if (!$user_ID) {
    header("Location: login.php");
    exit();
}

$deleted = false;
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    $user_ID = mysqli_real_escape_string($conn, $user_ID);

    // Start transaction
    mysqli_begin_transaction($conn);
    try {
        // Step 1: Get all address_IDs linked to the user
        $address_IDs = [];
        $ad_query = "SELECT address_ID FROM user_address WHERE user_ID = '$user_ID'";
        $ad_result = mysqli_query($conn, $ad_query);

        if ($ad_result) {
            while ($row = mysqli_fetch_assoc($ad_result)) {
                $address_IDs[] = $row['address_ID'];
            }
        }

        // Step 2: Run all deletion queries
        $queries = [
            "DELETE FROM user_phone WHERE user_ID = '$user_ID'",
            "DELETE FROM user_email WHERE user_ID = '$user_ID'",
            "DELETE FROM user_address WHERE user_ID = '$user_ID'",
            "DELETE FROM appointment WHERE user_ID = '$user_ID'",
            "DELETE FROM password_reset WHERE matched_user_ID = '$user_ID'",
            "DELETE FROM doctor_bmdc_verification WHERE user_ID = '$user_ID'",
            "DELETE FROM compounder WHERE user_ID = '$user_ID'",
            "DELETE FROM doctor WHERE user_ID = '$user_ID'",
            "DELETE FROM unavailability WHERE doctor_id = '$user_ID'",
            "DELETE FROM works_for WHERE doctor_ID = '$user_ID' OR compounder_ID = '$user_ID'",
            "DELETE FROM works_in WHERE doctor_ID = '$user_ID'",
            "DELETE FROM systemuser WHERE user_ID = '$user_ID'"
        ];

        foreach ($queries as $query) {
            if (!mysqli_query($conn, $query)) {
                throw new Exception("Query failed: " . mysqli_error($conn));
            }
        }

        // Step 3: Delete orphan addresses
        foreach ($address_IDs as $address_ID) {
            $check_query = "SELECT 1 FROM user_address WHERE address_ID = '$address_ID' LIMIT 1";
            $check_result = mysqli_query($conn, $check_query);
            if (mysqli_num_rows($check_result) == 0) {
                mysqli_query($conn, "DELETE FROM address WHERE address_ID = '$address_ID'");
            }
        }

        // Commit transaction
        mysqli_commit($conn);
        $deleted = true;
        session_destroy(); // log the user out
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Error deleting user: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 40px;
            text-align: center;
        }
        .box {
            background: white;
            max-width: 500px;
            margin: 60px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-cancel {
            background: #9ca3af;
            color: white;
        }
        .success {
            color: #16a34a;
            font-weight: bold;
        }
        .error {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="box">
    <?php if ($deleted): ?>
        <h2 class="success">✅ Your profile has been deleted successfully.</h2>
        <p><a href="LoginorRegister.php" class="btn btn-cancel">Return to Home</a></p>
    <?php elseif ($error): ?>
        <h2 class="error">❌ Something went wrong.</h2>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <p><a href="login.php" class="btn btn-cancel">Back to Profile</a></p>
    <?php else: ?>
        <h2>⚠️ Are you sure you want to delete your profile?</h2>
        <p>This action <strong>cannot be undone</strong>.</p>
        <form method="POST">
            <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete</button>
            <a href="login.php" class="btn btn-cancel">Cancel</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
