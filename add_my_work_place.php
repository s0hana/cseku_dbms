<?php
session_start();
require_once 'db.php';

$user_ID = $_SESSION['user_ID'];

// Handle AJAX: Search Chamber
if (isset($_GET['action']) && $_GET['action'] === 'search') {
    $q = "%" . ($_GET['q'] ?? '') . "%";

    $sql = "SELECT c.chamber_ID, c.chamber_name, 
            CONCAT_WS(', ', a.house_no, a.road, a.area, a.thana, a.district, a.division, a.postal_code) AS full_address
            FROM chamber c
            JOIN chamber_address a ON a.chamber_ID = c.chamber_ID
            WHERE c.chamber_name LIKE ? OR a.area LIKE ? OR a.district LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $q, $q, $q);
    $stmt->execute();
    $result = $stmt->get_result();

    $chambers = [];
    while ($row = $result->fetch_assoc()) {
        $chambers[] = [
            "id" => $row['chamber_ID'],
            "display" => $row['chamber_name'] . ", " . $row['full_address']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($chambers);
    exit;
}

// Handle AJAX: Add Chamber
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $chamber_ID = $_POST['chamber_ID'] ?? null;
    if (!$user_ID || !$chamber_ID) {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE compounder SET chamber_ID = ? WHERE user_ID = ?");
    $stmt->bind_param("ii", $chamber_ID, $user_ID);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Chamber added successfully."]);
    exit;
}

// Handle AJAX: Remove Chamber
if (isset($_POST['action']) && $_POST['action'] === 'remove') {
    $stmt = $conn->prepare("UPDATE compounder SET chamber_ID = NULL WHERE user_ID = ?");
    $stmt->bind_param("i", $user_ID);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Chamber removed successfully."]);
    exit;
}

// Get current chamber_ID
$stmt = $conn->prepare("SELECT chamber_ID FROM compounder WHERE user_ID = ?");
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$stmt->bind_result($currentChamber);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add My Work Place</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f2f5;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
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
      padding: 15px 15px;
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
    main {
      flex: 1;
      max-width: 800px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #007bff;
      margin-bottom: 25px;
    }
    input[type="text"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 10px;
    }
    #message {
      font-weight: bold;
      margin-top: 10px;
    }
    .success { color: green; }
    .error { color: red; }
    .chamber-result {
      cursor: pointer;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 5px;
      background: #f9f9f9;
    }
    .chamber-result:hover {
      background-color: #e6f0ff;
    }
    #results {
      margin-top: 5px;
    }
    button.remove {
      margin-top: 15px;
      background-color:red;
      color: white;
      border: none;
      padding: 12px 20px;
      cursor: pointer;
      border-radius: 6px;
      font-weight: bold;
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
  <!-- Header -->
  <header>
    <nav>
      <div class="logo">
        <h1>PulseScheduler</h1>
      </div>
      <ul class="nav-links">
      <li><a href="chamber_info_compounder.php">My Chamber</a></li>
      <li><a href="maanage_compounder_profile.php">Dashboard</a></li>
        <li><a href="AboutUs.php">About Us</a></li>
        <li><a href="Contact.php">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- Main Section -->
  <main>
    <h2>Search and Add Chamber</h2>

    <input type="text" id="searchBox" placeholder="Type chamber name or address..." autocomplete="off">
    <div id="results"></div>

    <?php if ($currentChamber): ?>
      <button class="remove" onclick="removeChamber()">Remove Current Chamber</button>
    <?php endif; ?>

    <div id="message"></div>
  </main>

  <!-- Footer -->
  <footer>© 2025 PulseScheduler</footer>

  <!-- JS -->
  <script>
    const searchBox = document.getElementById("searchBox");
    const resultsDiv = document.getElementById("results");
    const messageDiv = document.getElementById("message");

    searchBox.addEventListener("input", function () {
      const query = this.value.trim();
      if (query.length < 2) {
        resultsDiv.innerHTML = "";
        return;
      }

      fetch("?action=search&q=" + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
          resultsDiv.innerHTML = "";
          data.forEach(item => {
            const div = document.createElement("div");
            div.className = "chamber-result";
            div.textContent = item.display;
            div.onclick = () => selectChamber(item.id);
            resultsDiv.appendChild(div);
          });
        });
    });

    function selectChamber(chamberId) {
      fetch("", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=add&chamber_ID=" + encodeURIComponent(chamberId)
      })
      .then(res => res.json())
      .then(data => {
        messageDiv.textContent = data.message;
        messageDiv.className = data.success ? "success" : "error";
        if (data.success) setTimeout(() => location.reload(), 1000);
      });
    }

    function removeChamber() {
      fetch("", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=remove"
      })
      .then(res => res.json())
      .then(data => {
        messageDiv.textContent = data.message;
        messageDiv.className = data.success ? "success" : "error";
        if (data.success) setTimeout(() => location.reload(), 1000);
      });
    }
  </script>
</body>
</html>
