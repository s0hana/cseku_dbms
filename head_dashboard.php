<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: head_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$query = "SELECT full_name, photo as h_photo FROM systemuser WHERE user_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$head_admin = $result->fetch_assoc();

function fetchAdmins($conn) {
    $query = "
        SELECT 
            su.full_name,
            su.photo,
            su.birth_certificate_number,
            GROUP_CONCAT(DISTINCT up.phone SEPARATOR ', ') AS phones,
            GROUP_CONCAT(DISTINCT ue.email SEPARATOR ', ') AS emails,
            GROUP_CONCAT(DISTINCT 
                CONCAT_WS(', ',
                    NULLIF(a.house_no, ''),
                    NULLIF(a.road, ''),
                    NULLIF(a.area, ''),
                    NULLIF(a.thana, ''),
                    NULLIF(a.district, ''),
                    NULLIF(a.division, ''),
                     NULLIF(a.postal_code, '')
                )
                SEPARATOR ' | '
            ) AS full_address
        FROM systemuser su
        LEFT JOIN user_phone up ON su.user_ID = up.user_ID
        LEFT JOIN user_email ue ON su.user_ID = ue.user_ID
        LEFT JOIN user_address a ON su.user_ID = a.user_ID
        WHERE su.role = 'Admin'
        GROUP BY su.user_ID
    ";

    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $response = ['status' => '', 'message' => ''];
    $birth_certificate_number = $_POST['birth_certificate_number'];

    $checkQuery = "SELECT * FROM systemuser WHERE birth_certificate_number = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('s', $birth_certificate_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['status'] = 'error';
        $response['message'] = 'No user found with this Birth Certificate Number.';
    } else {
        $updateRole = $_POST['action'] == 'add_admin' ? 'Admin' : 'User';
        $updateQuery = "UPDATE systemuser SET role = ? WHERE birth_certificate_number = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('ss', $updateRole, $birth_certificate_number);
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = $_POST['action'] == 'add_admin' 
                ? 'Admin added successfully.' 
                : 'Admin removed successfully.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to update admin status.';
        }
    }

    echo json_encode($response);
    exit;
}

$admins = fetchAdmins($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PulseScheduler | Head Admin Dashboard</title>
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

    main {
      padding: 40px 20px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h2 {
      color: #007bff;
      text-align: center;
    }

    form input {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    .button-group {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
    }

    button {
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      color: white;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .btn-add {
      background-color: #28a745;
    }

    .btn-add:hover {
      background-color: #218838;
    }

    .btn-remove {
      background-color: #dc3545;
    }

    .btn-remove:hover {
      background-color: #c82333;
    }

    table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ccc;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    #responseMessage {
      margin-top: 20px;
      text-align: center;
      font-weight: bold;
    }

    .text-success {
      color: green;
    }

    .text-error {
      color: red;
    }

    .profile-img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #007bff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .admin-table-img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #007bff;
    }

    @media (max-width: 768px) {
      .container {
        margin: 10px;
        padding: 15px;
      }

      nav {
        flex-direction: column;
        align-items: flex-start;
      }

      .nav-links {
        flex-direction: column;
        gap: 10px;
      }
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
      <li><a href="logout_head.php">Logout</a></li>
    </ul>
  </nav>
</header>

<main>
  <div class="container">
    <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
    <div class="profile-section" style="text-align:center; margin-top: 20px;">
      <img src="<?= htmlspecialchars($head_admin['h_photo'] ?? '') ?>" alt="Head Admin Photo" class="profile-img">
      <p style="margin-top:10px;"><?= htmlspecialchars($head_admin['full_name']) ?></p>
    </div>

    <form id="adminForm">
      <input type="text" name="birth_certificate_number" id="birth_certificate_number" placeholder="Enter Birth Certificate Number" required>
      <div class="button-group">
        <button type="button" class="btn-add" id="addAdmin">Add Admin</button>
        <button type="button" class="btn-remove" id="removeAdmin">Remove Admin</button>
      </div>
    </form>

    <div id="responseMessage"></div>

    <h2 style="margin-top: 40px;">Admin List</h2>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Photo</th>
          <th>Birth Certificate</th>
          <th>Phone</th>
          <th>Address</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($admins as $admin): ?>
          <tr>
            <td><?= htmlspecialchars($admin['full_name']) ?></td>
            <td>
              <img src="<?= htmlspecialchars($admin['photo'] ?? '') ?>" alt="Admin Photo" class="admin-table-img">
            </td>
            <td><?= htmlspecialchars($admin['birth_certificate_number']) ?></td>
            <td><?= htmlspecialchars($admin['phones']) ?></td>
            <td><?= htmlspecialchars($admin['full_address']) ?></td>
            <td><?= htmlspecialchars($admin['emails'] ?: 'N/A') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<footer>
  <p>&copy; 2025 PulseScheduler. All rights reserved.</p>
</footer>

<script>
$(document).ready(function() {
  function sendRequest(action) {
    var birthCertificateNumber = $('#birth_certificate_number').val();
    $.ajax({
      type: 'POST',
      url: '',
      data: { action: action, birth_certificate_number: birthCertificateNumber },
      success: function(response) {
        var data = JSON.parse(response);
        $('#responseMessage')
          .text(data.message)
          .removeClass('text-success text-error')
          .addClass(data.status === 'success' ? 'text-success' : 'text-error');
        if (data.status === 'success') {
          setTimeout(() => location.reload(), 1000);
        }
      }
    });
  }

  $('#addAdmin').on('click', function() {
    sendRequest('add_admin');
  });

  $('#removeAdmin').on('click', function() {
    sendRequest('remove_admin');
  });
});
</script>

</body>
</html>
