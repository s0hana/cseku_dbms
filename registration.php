<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PulseScheduler | Register</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #e6f0ff, #ffffff);
      margin: 0;
      padding: 0;
    }

    header, footer {
      background: #007bff;
      color: white;
      padding: 15px 0;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      height: 35px;
    }

    header h1 {
      margin: 0;
      font-size: 26px;
    }

    footer p {
      margin: 0;
      font-size: 14px;
    }

    main {
      padding: 20px 20px;
    }

    form, #userTypeSection {
      max-width: 400px;
      margin: auto;
      padding: 25px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    form h2 {
      margin-bottom: 20px;
      text-align: center;
      color: #007bff;
    }

    input {
      width: 100%;
      padding: 7.5px;
      margin-top: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background: #007bff;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #0056b3;
    }

    #consoleOutput {
      text-align: center;
      margin-top: 20px;
    }

    .console-message {
      font-weight: bold;
      padding: 10px 15px;
    }

    .success-msg {
      color: green;
    }

    .error-msg {
      color: red;
    }

    #userTypeSection {
      display: none;
      text-align: center;
    }

    #userTypeSection h3 {
      margin-bottom: 15px;
      color: #007bff;
    }

    #userTypeSection button {
      width: auto;
      margin: 8px;
      padding: 10px 16px;
    }

    #bmdcModal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }

    #bmdcModal > div {
      background: white;
      padding: 20px;
      border-radius: 12px;
      max-width: 350px;
      margin: auto;
    }

    @media (max-width: 480px) {
      form, #userTypeSection {
        margin: 10px;
        padding: 20px;
      }
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
    }

    .logo h1 {
      margin: 0;
      font-size: 28px;
      font-weight: 700;
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 25px;
      margin: 0;
      padding: 0;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .nav-links a:hover {
      color: #FFC107;
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
      <li><a href="Home.php">Home</a></li>
      <li><a href="AboutUs.php">About Us</a></li>
      <li><a href="Contact.php">Contact</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
  </nav>
</header>

<main>
  <form id="registrationForm">
    <h2>Create an Account</h2>
    <input type="text" name="full_name" placeholder="Full Name" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="text" name="birth_certificate" placeholder="Birth Certificate Number" required>
    <input type="text" name="phone" placeholder="Phone (e.g. 01XXXXXXXXX)" required>
    <input type="email" name="email" placeholder="Email (optional)">
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
  </form>

  <div id="consoleOutput"></div>

  <div id="userTypeSection">
    <h3>Are you a doctor?</h3>
    <button onclick="submitUserType('doctor')">Yes</button>
    <button onclick="submitUserType('general')">No</button>
  </div>

  <div id="bmdcModal">
    <div>
      <h3>Enter BMDC Number</h3>
      <input type="text" id="bmdcNumberInput" placeholder="BMDC Number">
      <button onclick="submitBMDCNumber()">Submit</button>
    </div>
  </div>
</main>
<br><br>
<footer>
  <p>&copy; 2025 PulseScheduler. All rights reserved.</p>
</footer>

<script>
  let registeredUserId = null;

  function showMessage(message, type = 'success') {
    const output = $("#consoleOutput");
    const cssClass = type === 'success' ? 'success-msg' : 'error-msg';
    output.html(`<div class="console-message ${cssClass}">${message}</div>`);
  }

  function isValidBirthCertificate(bc) {
    return /^\d{17}$/.test(bc);
  }

  function isValidBDPhone(phone) {
    return /^01[3-9]\d{8}$/.test(phone);
  }

  $("#registrationForm").on("submit", function(e) {
    e.preventDefault();

    const formData = $(this).serializeArray();
    const birth_certificate = formData.find(x => x.name === 'birth_certificate').value;
    const phone = formData.find(x => x.name === 'phone').value;

    if (!isValidBirthCertificate(birth_certificate)) {
      showMessage("❌ Birth Certificate must be exactly 17 digits.", "error");
      return;
    }

    if (!isValidBDPhone(phone)) {
      showMessage("❌ Phone number must be 11 digits and valid in Bangladesh format (e.g., 01XXXXXXXXX).", "error");
      return;
    }

    $.ajax({
      url: "register.php",
      method: "POST",
      data: $(this).serialize(),
      success: function(response) {
        const res = JSON.parse(response);
        if (res.status === "success") {
          registeredUserId = res.user_ID;
          $("#registrationForm").hide();
          $("#userTypeSection").show();
          showMessage("✅ Registration successful!", "success");
        } else {
          showMessage(`❌ ${res.message}`, "error");
        }
      }
    });
  });

  function submitUserType(type) {
    if (type === 'doctor') {
      $("#bmdcModal").css('display', 'flex');
    } else {
      $.post("user_type_handler.php", { user_ID: registeredUserId, type: type }, function() {
        showMessage("✅ User type saved. Redirecting to login...", "success");
        setTimeout(() => {
          window.location.href = "login.php";
        }, 2000);
      });
    }
  }

  function submitBMDCNumber() {
  const bmdcNumber = $("#bmdcNumberInput").val().trim();
  if (!bmdcNumber) {
    alert("BMDC Number is required.");
    return;
  }

  $.post("bmdc_verification_handler.php", {
    user_ID: registeredUserId,
    bmdc_number: bmdcNumber
  }, function(response) {
    const res = JSON.parse(response);
    
    if (res.status === "success") {
      $("#bmdcModal").hide();
      showMessage(res.message, "success");
      setTimeout(() => {
        window.location.href = "login.php";
      }, 3000);
    } else {
      showMessage(res.message, "error");
    }
  });
}

</script>

</body>
</html>
