<?php
// update_profile.php
include 'db.php';
session_start();

if (!isset($_SESSION['user_ID'])) {
    die("Unauthorized access");
}

$user_ID = $_SESSION['user_ID'];

$stmt = $conn->prepare("SELECT full_name, gender, birth_day, blood_group, medical_history FROM systemuser WHERE user_ID=?");
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$stmt->bind_result($db_name, $db_gender, $db_birth_day, $db_blood, $db_history);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = !empty($_POST['full_name']) ? $_POST['full_name'] : $db_name;
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : $db_gender;
    $birth_day = !empty($_POST['birth_day']) ? $_POST['birth_day'] : $db_birth_day;
    $blood_group = !empty($_POST['blood_group']) ? $_POST['blood_group'] : $db_blood;
    $medical_history = !empty($_POST['medical_history']) ? $_POST['medical_history'] : $db_history;

    // Profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $targetDir = "uploads/profile_pics/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileTmp = $_FILES['profile_picture']['tmp_name'];
        $fileName = basename($_FILES['profile_picture']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newName = "user_" . $user_ID . "_" . time() . "." . $fileExt;
        $targetFile = $targetDir . $newName;
        if (move_uploaded_file($fileTmp, $targetFile)) {
            $stmt = $conn->prepare("UPDATE systemuser SET photo=? WHERE user_ID=?");
            $stmt->bind_param("si", $targetFile, $user_ID);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Basic info update
    $stmt = $conn->prepare("UPDATE systemuser SET full_name=?, gender=?, birth_day=?, blood_group=?, medical_history=? WHERE user_ID=?");
    $stmt->bind_param("sssssi", $full_name, $gender, $birth_day, $blood_group, $medical_history, $user_ID);
    $stmt->execute();
    $stmt->close();

    // Emails insert
    if (!empty($_POST['emails'])) {
        foreach ($_POST['emails'] as $email) {
            $email = trim($email);
            if (!empty($email)) {
                $check = $conn->prepare("SELECT * FROM user_email WHERE user_ID=? AND email=?");
                $check->bind_param("is", $user_ID, $email);
                $check->execute();
                if ($check->get_result()->num_rows == 0) {
                    $insert = $conn->prepare("INSERT INTO user_email (user_ID, email) VALUES (?, ?)");
                    $insert->bind_param("is", $user_ID, $email);
                    $insert->execute();
                    $insert->close();
                }
                $check->close();
            }
        }
    }

    // Phones insert
    if (!empty($_POST['phones'])) {
        foreach ($_POST['phones'] as $phone) {
            $phone = trim($phone);
            if (!empty($phone)) {
                $check = $conn->prepare("SELECT * FROM user_phone WHERE user_ID=? AND phone=?");
                $check->bind_param("is", $user_ID, $phone);
                $check->execute();
                if ($check->get_result()->num_rows == 0) {
                    $insert = $conn->prepare("INSERT INTO user_phone (user_ID, phone) VALUES (?, ?)");
                    $insert->bind_param("is", $user_ID, $phone);
                    $insert->execute();
                    $insert->close();
                }
                $check->close();
            }
        }
    }
    // Check if address exists for the user
    $stmt = $conn->prepare("SELECT address_ID FROM user_address WHERE user_ID = ?");
    $stmt->bind_param("i", $user_ID);
    $stmt->execute();
    $stmt->store_result();
    $has_address = $stmt->num_rows > 0;
    $stmt->close();
    
    if ($has_address) {
        // Get existing values
        $stmt = $conn->prepare("SELECT house_no, road, area, thana, district, division, postal_code FROM user_address WHERE user_ID = ?");
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $stmt->bind_result($db_house, $db_road, $db_area, $db_thana, $db_district, $db_division, $db_postal);
        $stmt->fetch();
        $stmt->close();
    
        // Use new POST values if available, else keep existing ones
        $house_no = !empty($_POST['house_no']) ? $_POST['house_no'] : $db_house;
        $road_no = !empty($_POST['road']) ? $_POST['road'] : $db_road;
        $area = !empty($_POST['area']) ? $_POST['area'] : $db_area;
        $thana = !empty($_POST['thana']) ? $_POST['thana'] : $db_thana;
        $district = !empty($_POST['district']) ? $_POST['district'] : $db_district;
        $division = !empty($_POST['division']) ? $_POST['division'] : $db_division;
        $postal_code = !empty($_POST['postal_code']) ? $_POST['postal_code'] : $db_postal;
    
        // Update existing address
        $stmt = $conn->prepare("UPDATE user_address SET house_no = ?, road = ?, area = ?, thana = ?, district = ?, division = ?, postal_code = ? WHERE user_ID = ?");
        $stmt->bind_param("sssssssi", $house_no, $road_no, $area, $thana, $district, $division, $postal_code, $user_ID);
        $stmt->execute();
        $stmt->close();
    } else {
        // Insert new address with user_ID
        $stmt = $conn->prepare("INSERT INTO user_address (user_ID, house_no, road, area, thana, district, division, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "isssssss",
            $user_ID,
            $_POST['house_no'], $_POST['road'], $_POST['area'],
            $_POST['thana'], $_POST['district'], $_POST['division'], $_POST['postal_code']
        );
        $stmt->execute();
        $stmt->close();
    }
    

    echo "✅ Profile updated successfully!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile | PulseScheduler</title>
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
            background-color: var(--primary, var(--primary-light));
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
        .removable-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    background: #f9f9f9;
    padding: 5px 10px;
    border-radius: 5px;
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
            width: 100px;
            padding: 0.6rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: border-color 0.3s;
        }
        #medical_history {
            height: 50px;
            resize: none;
}
.form-control {
    width: 100%;
    max-width: 250px;
}
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
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
        
        .img-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--primary-light);
            display: block;
            margin: 0 auto 1rem;
        }
        
        .hidden {
            display: none;
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
        
        footer {
            background-color: var(--primary, var(--primary-light));
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
                <li><a href="javascript:history.back()">Back</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="profile-card">
            <div class="card-header">
                <h2>Update Your Profile</h2>
            </div>
            
            <div class="card-body">
                <form id="updateProfileForm" enctype="multipart/form-data" method="post">
                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Basic Information</h3>
                        <div class="form-grid">
                            <!-- Column 1 -->
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Your full name">
                            </div>
                            
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="birth_day">Date of Birth</label>
                                <input type="text" id="birth_day" name="birth_day" class="form-control" placeholder="dd/mm/yyyy">
                            </div>
                            
                            <!-- Column 2 -->
                            <div class="form-group">
                                <label for="blood_group">Blood Group</label>
                                <select id="blood_group" name="blood_group" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Profile Picture</label>
                                <img id="preview" class="img-preview hidden" src="#" alt="Profile Preview">
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="form-control" onchange="previewImage(event)">
                            </div>
                            
                            <div class="form-group">
                                <label>Medical History</label>
                                <textarea id="medical_history" name="medical_history" class="form-control" placeholder="Any medical conditions or allergies"></textarea>
                            </div>
                            
                            <!-- Column 3 -->
                            <div class="form-group" id="phone-group">
    <label>Phone Numbers</label>
    <div class="phone-input">
        <input type="text" name="phones[]" class="form-control" placeholder="01XXXXXXXXX">
        <button type="button" class="remove-btn" onclick="removeField(this)">❌</button>
    </div>
    <button type="button" class="add-more-btn" onclick="addPhone()">
        <i class="fas fa-plus-circle"></i> Add Phone
    </button>
</div>

<div class="form-group" id="email-group">
    <label>Email Addresses</label>
    <div class="email-input">
        <input type="email" name="emails[]" class="form-control" placeholder="your@email.com">
        <button type="button" class="remove-btn" onclick="removeField(this)">❌</button>
    </div>
    <button type="button" class="add-more-btn" onclick="addEmail()">
        <i class="fas fa-plus-circle"></i> Add Email
    </button>
</div>

                        </div>
                    </div>
                    
                    <!-- Address Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Address Information</h3>
                        <div class="form-grid">
                            <!-- Column 1 -->
                            <div class="form-group">
                                <label for="house_no">House No</label>
                                <input type="text" id="house_no" name="house_no" class="form-control" placeholder="House/Apartment number">
                            </div>
                            
                            <div class="form-group">
                                <label for="road">Road No</label>
                                <input type="text" id="road" name="road" class="form-control" placeholder="Road/Street number">
                            </div>
                            
                            <div class="form-group">
                                <label for="area">Area</label>
                                <input type="text" id="area" name="area" class="form-control" placeholder="Area/Village">
                            </div>
                            
                            <!-- Column 2 -->
                            <div class="form-group">
                                <label for="thana">Thana</label>
                                <input type="text" id="thana" name="thana" class="form-control" placeholder="Thana/Upazila">
                            </div>
                            
                            <div class="form-group">
                                <label for="district">District</label>
                                <input type="text" id="district" name="district" class="form-control" placeholder="District">
                            </div>
                            
                            <div class="form-group">
                                <label for="division">Division</label>
                                <input type="text" id="division" name="division" class="form-control" placeholder="Division">
                            </div>
                            
                            <!-- Column 3 -->
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="Postal/Zip code">
                            </div>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button type="submit" class="btn" style="padding: 0.8rem 2rem; font-size: 1rem;">
                            Update Profile
                        </button>
                    </div>
                </form>
                
                <div id="response" class="response-message hidden"></div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> PulseScheduler. All rights reserved.</p>
    </footer>

    <script>
        function addPhone() {
    const container = document.createElement('div');
    container.className = 'phone-input';
    container.innerHTML = `
        <input type="text" name="phones[]" class="form-control" placeholder="01XXXXXXXXX">
        <button type="button" class="remove-btn" onclick="removeField(this)">❌</button>
    `;
    document.getElementById('phone-group').insertBefore(container, document.querySelector('#phone-group .add-more-btn'));
}

function addEmail() {
    const container = document.createElement('div');
    container.className = 'email-input';
    container.innerHTML = `
        <input type="email" name="emails[]" class="form-control" placeholder="your@email.com">
        <button type="button" class="remove-btn" onclick="removeField(this)">❌</button>
    `;
    document.getElementById('email-group').insertBefore(container, document.querySelector('#email-group .add-more-btn'));
}

function removeField(button) {
    button.parentElement.remove();
}

        function previewImage(event) {
            const reader = new FileReader();
            const preview = document.getElementById('preview');
            
            reader.onload = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
            };
            
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const responseDiv = document.getElementById('response');
            
            // Format birth day
            const birthDayInput = document.getElementById('birth_day').value;
            const parts = birthDayInput.split('/');
            if (parts.length === 3) {
                const formatted = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                formData.set('birth_day', formatted);
            }

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                responseDiv.textContent = data;
                responseDiv.classList.remove('hidden', 'error');
                responseDiv.classList.add('success');
                
                // Scroll to response message
                responseDiv.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                responseDiv.textContent = "❌ Something went wrong! Please try again.";
                responseDiv.classList.remove('hidden', 'success');
                responseDiv.classList.add('error');
                console.error(error);
            });
        });
    </script>
</body>
</html>