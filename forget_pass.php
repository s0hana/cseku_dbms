<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f2f7ff;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color:rgb(13, 122, 231);
            color: white;
            padding: 15px 30px;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            margin: 0;
            font-size: 24px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #aee1ff;
        }

        .container {
            flex: 1;
            max-width: 500px;
            margin: 60px auto;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 25px;
            color:rgb(7, 129, 250);
        }

        form label {
            display: block;
            margin-bottom: 15px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color:rgb(10, 130, 250);
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color:rgb(13, 125, 236);
        }

        #message {
            margin-top: 20px;
            padding: 12px;
            text-align: center;
            border-radius: 6px;
            display: none;
        }

        #message.success {
            display: block;
            background-color: #e6ffe6;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        #message.error {
            display: block;
            background-color: #ffe6e6;
            color: #d32f2f;
            border: 1px solid #ef9a9a;
        }

        footer {
            text-align: center;
            background-color:rgb(6, 127, 247);
            color: white;
            padding: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <div class="logo"><h1>PulseScheduler</h1></div>
        <ul class="nav-links">
            <li><a href="Home.php">Home</a></li>
            <li><a href="AboutUs.php">About Us</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2>Reset Password Request</h2>
    <form id="resetForm">
        <label>Birth Certificate Number:
            <input type="text" name="bcn" required>
        </label>
        <label>Phone Number:
            <input type="text" name="phone" required>
        </label>
        <input type="submit" value="Submit">
    </form>
    <div id="message"></div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> PulseScheduler. All rights reserved.</p>
</footer>
 
<script>
$(document).ready(function(){
    $("#resetForm").on("submit", function(e){
        e.preventDefault();
        const bcn = $("input[name='bcn']").val();
        const phone = $("input[name='phone']").val();

        $.post("ajax_verify_reset_info.php", { bcn, phone }, function(response) {
            const msgDiv = $("#message").removeClass("error success");
            response = response.trim();

            if (response === "matched") {
                msgDiv.addClass("success").html("✔️ Password reset request submitted successfully.");
                setTimeout(() => window.location.href = "login.php", 2000);
            } else if (response === "already_sent") {
                msgDiv.addClass("error").html("⚠️ A reset request already exists. Redirecting to reset page...");
                setTimeout(() => {
                    window.open("reset_password_user.php?bcn=" + encodeURIComponent(bcn) + "&phone=" + encodeURIComponent(phone), "_blank");
                }, 2000);
            } else {
                msgDiv.addClass("error").html("❌ " + response);
                setTimeout(() => window.location.href = "login.php", 3000);
            }
        });
    });
});
</script>

</body>
</html>
