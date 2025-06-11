<?php
// login.php
// Include database connection
    include('db.php'); // Make sure you have a connection to your database.
session_start();
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input values
    $username = $_POST['username'];
    $password = $_POST['password'];

    

    // Query to check if the user exists in the systemuser table
    $query = "SELECT * FROM systemuser WHERE user_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if the password is correct
        if (password_verify($password, $user['user_password'])) {
            $_SESSION['user_ID'] = $user['user_ID'];
            // Password is correct, check if the user is a doctor or compounder
            $doctorQuery = "SELECT * FROM doctor WHERE user_ID = ?";
            $compounderQuery = "SELECT * FROM compounder WHERE user_ID = ?";
            
            $stmtDoctor = $conn->prepare($doctorQuery);
            $stmtDoctor->bind_param('i', $user['user_ID']);
            $stmtDoctor->execute();
            $doctorResult = $stmtDoctor->get_result();

            $stmtCompounder = $conn->prepare($compounderQuery);
            $stmtCompounder->bind_param('i', $user['user_ID']);
            $stmtCompounder->execute();
            $compounderResult = $stmtCompounder->get_result();

            // Determine where to redirect
            if ($doctorResult->num_rows > 0) {
                $response = [
                    'success' => true,
                    'message' => 'Login successful. Redirecting to your profile...',
                    'redirect_url' => 'doctor_profile.php'
                ];
            } elseif ($compounderResult->num_rows > 0) {
                $response = [
                    'success' => true,
                    'message' => 'Login successful. Redirecting to your profile...',
                    'redirect_url' => 'compounder_profile.php'
                ];
            } else {
                $response = [
                    'success' => true,
                    'message' => 'Login successful. Redirecting to your profile...',
                    'redirect_url' => 'general.php'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Incorrect password. Please try again.'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'User not found. Please check your credentials.'
        ];
    }

    // Return the response in JSON format
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #333;
            line-height: 1.6;
            background: #f4f4f4;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #007BFF;
            color: white;
        }
        header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: bold;
            padding-left: 40%;
        }
        nav {
            display: flex;
            gap: 20px;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        nav a:hover {
            color: #FFC107;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007BFF;
        }
        input, select {
            width: 100%;
            padding: 7px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            background: #007BFF;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        #message {
            text-align: center;
            margin-top: 15px;
            font-size: 16px;
        }
        #message.success {
            color: green;
            }

        #message.error {
            color: red;
        }

        footer {
            background: #007BFF;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }
        footer p {
            font-size: 16px;
        }
        footer a {
            color: #FFC107;
            text-decoration: none;
            font-weight: 600;
        }
        footer a:hover {
            color: #e0a800;
        }
        @media (max-width: 500px) {
            .container {
                width: 90%;
                padding: 20px;
            }
            header nav {
                flex-direction: column;
                gap: 10px;
            }
            header nav a {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>PulseScheduler</h1>
        <nav>
            <a href="Home.php">Home</a>
            <a href="AboutUs.php">About</a>
            <a href="Contact.php">Contact</a>
        <a href="registration.php" class="btn">Register</a>
        
        </nav>
    </header>

    <div class="container">
        <h2>Login</h2>
        <form id="loginForm">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <div id="message"></div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                var username = $('#username').val();
                var password = $('#password').val();
                
                // AJAX request to login.php
                $.ajax({
                    url: 'login.php',
                    type: 'POST',
                    data: { username: username, password: password },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if(data.success) {
                            $('#message').removeClass('error').addClass('success').text(data.message);
                            window.location.href = data.redirect_url;
                        } else {
                            $('#message').removeClass('success').addClass('error').text(data.message);
                        }
                    }
                });
            });
        });
    </script>

<footer id="footer">
<a href="forget_pass.php">Forget Password/Username</a>
        <p>&copy; 2025 Pulsescheduler</p>
    </footer>
</body>
</html>
