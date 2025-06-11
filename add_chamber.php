<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}
$user_ID = $_SESSION['user_ID'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $chamber_name =  mysqli_real_escape_string($conn, $_POST['chamber_name']);
    $opening_time = $_POST['opening_time'];
    $closing_time = $_POST['closing_time'];
    $working_days = isset($_POST['working_days']) ? json_encode($_POST['working_days']) : "[]";
    $phones = $_POST['phone'];
    $emails = $_POST['email'];
    
    $house_no = $_POST['house_no'];
    $road_no = $_POST['road_no'];
    $area = $_POST['area'];
    $thana = $_POST['thana'];
    $district = $_POST['district'];
    $division = $_POST['division'];
    $postal_code = $_POST['postal_code'];

    $conn->begin_transaction();
    try {
        $conn->query("INSERT INTO chamber (chamber_name, opening_time, closing_time, working_days)
                      VALUES ('$chamber_name', '$opening_time', '$closing_time', '$working_days')");
        $chamber_ID = $conn->insert_id;

        $conn->query("INSERT INTO chamber_address (chamber_ID, house_no, road, area, thana, district, division, postal_code)
                      VALUES ('$chamber_ID', '$house_no', '$road_no', '$area', '$thana', '$district', '$division', '$postal_code')");
        $address_ID = $conn->insert_id;

        foreach ($phones as $phone) {
            if (!empty($phone)) {
                $conn->query("INSERT INTO chamber_phone (chamber_ID, phone) VALUES ($chamber_ID, '$phone')");
            }
        }
        foreach ($emails as $email) {
            if (!empty($email)) {
                $conn->query("INSERT INTO chamber_email (chamber_ID, email) VALUES ($chamber_ID, '$email')");
            }
        }

        $conn->commit();
        $_SESSION['message'] = "Chamber added successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: chamber_list_doctor.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = "Failed to add chamber: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        echo "<div style='padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; font-weight: bold;'>
        {$_SESSION['message']}
      </div>";

// Optionally delay redirect so user sees the message
echo "<script>
        setTimeout(function() {
            window.location.href = 'chamber_list_doctor.php';
        }, 30000); // 3 seconds delay
      </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Chamber | PulseScheduler</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #007BFF;
            --primary-light: #00C6FF;
            --secondary: #6c757d;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --white: #ffffff;
            --hover-yellow: #fff3cd;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        header {
            background-color: var(--primary);
            color: var(--white);
            padding: 1rem 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 1rem;
        }
        
        nav ul li a {
            color: var(--white);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        nav ul li a:hover {
            background-color: var(--hover-yellow);
            color: var(--dark);
        }
        
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .profile-card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--primary);
            color: var(--white);
            padding: 1.5rem;
            text-align: center;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
        }
        
        select.form-control {
            height: 2.8rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .btn:hover {
            background-color: #0069d9;
            transform: translateY(-1px);
        }
        
        .section-title {
            color: var(--primary);
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-light);
            font-size: 1.1rem;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .response-message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
            text-align: center;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .add-more-btn {
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            margin: 0.5rem 0;
            width: auto;
        }
        
        .add-more-btn:hover {
            background-color: var(--primary-light);
            transform: none;
        }
        
        .add-more-btn i {
            margin-right: 5px;
        }
        
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }
        
        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: normal;
            cursor: pointer;
        }
        
        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .input-group .form-control {
            flex: 1;
            margin-bottom: 0;
        }
        
        footer {
            background-color: var(--primary);
            color: var(--light);
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
        
        @media (max-width: 1024px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            header {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 1rem;
                justify-content: center;
            }
            
            nav ul li {
                margin: 0 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">PulseScheduler</div>
        <nav>
            <ul>
                <li><a href="chamber_list_doctor.php?user_ID=<?php echo $user_ID; ?>">Chambers</a></li>
                <li><a href="works_in_chamber_list_doctor.php?user_ID=<?php echo $user_ID; ?>">My Chambers</a></li>
                <li><a href="manage_doctor_profile.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="profile-card">
            <div class="card-header">
                <h2>Add New Chamber</h2>
            </div>
            
            <div class="card-body">
                <?= $message ?>
                <form method="post" action="">
                    <!-- Chamber Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Chamber Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="chamber_name">Chamber Name</label>
                                <input type="text" id="chamber_name" name="chamber_name" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="opening_time">Opening Time</label>
                                <input type="time" id="opening_time" name="opening_time" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="closing_time">Closing Time</label>
                                <input type="time" id="closing_time" name="closing_time" class="form-control" required>
                            </div>
                            
                            <div class="form-group full-width">
                                <label>Working Days</label>
                                <div class="checkbox-group">
                                    <?php
                                    $days = ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
                                    foreach ($days as $day) {
                                        echo "<label><input type='checkbox' name='working_days[]' value='$day'> $day</label>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Contact Information</h3>
                        <div class="form-grid">
                            <div class="form-group" id="phone-fields">
                                <label>Phone Numbers</label>
                                <div class="input-group">
                                    <input type="text" name="phone[]" class="form-control phone-input" maxlength="11">
                                    <button type="button" class="add-more-btn" onclick="addPhone()">
                                        <i class="fas fa-plus-circle"></i> Add
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-group" id="email-fields">
                                <label>Email Addresses</label>
                                <div class="input-group">
                                    <input type="email" name="email[]" class="form-control email-input">
                                    <button type="button" class="add-more-btn" onclick="addEmail()">
                                        <i class="fas fa-plus-circle"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Address Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Address Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="house_no">House No</label>
                                <input type="text" id="house_no" name="house_no" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="road_no">Road No</label>
                                <input type="text" id="road_no" name="road_no" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="area">Area</label>
                                <input type="text" id="area" name="area" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="thana">Thana</label>
                                <input type="text" id="thana" name="thana" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="district">District</label>
                                <input type="text" id="district" name="district" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="division">Division</label>
                                <input type="text" id="division" name="division" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button type="submit" class="btn" style="padding: 0.8rem 2rem; font-size: 1rem;">
                            Add Chamber
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <script>
        function addPhone() {
            let div = document.createElement('div');
            div.classList.add('input-group');
            div.innerHTML = `<input type="text" name="phone[]" class="form-control phone-input" maxlength="11">
                            <button type="button" class="add-more-btn" onclick="this.parentNode.remove()">
                                <i class="fas fa-minus-circle"></i> Remove
                            </button>`;
            document.getElementById("phone-fields").appendChild(div);
        }

        function addEmail() {
            let div = document.createElement('div');
            div.classList.add('input-group');
            div.innerHTML = `<input type="email" name="email[]" class="form-control email-input">
                            <button type="button" class="add-more-btn" onclick="this.parentNode.remove()">
                                <i class="fas fa-minus-circle"></i> Remove
                            </button>`;
            document.getElementById("email-fields").appendChild(div);
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('email-input')) {
                let input = e.target;
                let msg = input.nextElementSibling;
                fetch(`check_duplicate.php?type=email&value=${input.value}`)
                    .then(res => res.text())
                    .then(data => {
                        msg.textContent = data;
                        msg.className = data.includes('available') ? 'success-msg' : 'error-msg';
                    });
            } else if (e.target.classList.contains('phone-input')) {
                let input = e.target;
                let msg = input.nextElementSibling;
                fetch(`check_duplicate.php?type=phone&value=${input.value}`)
                    .then(res => res.text())
                    .then(data => {
                        msg.textContent = data;
                        msg.className = data.includes('available') ? 'success-msg' : 'error-msg';
                    });
            }
        });
    </script>
    <br><br>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> PulseScheduler. All rights reserved.</p>
    </footer>

</body>
</html>