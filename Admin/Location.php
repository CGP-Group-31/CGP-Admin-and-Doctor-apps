<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

try {

    $query = "
        SELECT 
            UserID, 
            FullName, 
            Address, 
            RoleID,
            CASE 
                WHEN RoleID = 1 THEN 'Admin'
                WHEN RoleID = 2 THEN 'Doctor'
                WHEN RoleID = 4 THEN 'Caregiver'
                ELSE 'Elder'
            END AS RoleName
        FROM Users 
        WHERE RoleID != 1 -- Usually, you don't need to track the admin's location
        ORDER BY RoleID ASC, FullName ASC";
        
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Locations | Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    :root {
      --sidebar: #1F6F78;
      --bg: #F6F7F3;
      --card: #FFFFFF;
      --text-main: #1E2A2A;
      --text-muted: #6F7F7D;
      --checkins: #D6EFE6;
      --sos: #C62828;
      --accent: #E6B450;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
    body { display: flex; min-height: 100vh; background: var(--bg); }


    .sidebar { width: 240px; background: var(--sidebar); color: #fff; display: flex; flex-direction: column; position: fixed; height: 100vh; }
    .sidebar h2 { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); }
    .nav-btn { padding: 14px 20px; text-decoration: none; color: #fff; font-size: 14px; display: flex; align-items: center; }
    .nav-btn i { margin-right: 10px; width: 20px; text-align: center; }
    .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.15); border-left: 4px solid var(--accent); }
    .logout { margin-top: auto; background: var(--sos); text-align: center; font-weight: bold; }

    .content { flex: 1; margin-left: 240px; padding: 40px; }
h1 { 
    color: var(--sidebar); 
    margin-bottom: 20px; 
    font-size: 2rem; 
    font-weight: 700;
}
    .subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 25px; }

    .search-box {
      width: 100%; max-width: 400px; padding: 12px; border-radius: 8px;
      border: 1px solid #ddd; margin-bottom: 20px; font-size: 14px; outline: none;
    }

    .card { background: var(--card); padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
    th { background: #f8fafb; color: var(--text-muted); text-transform: uppercase; font-size: 11px; }

    .role-badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
    .role-Elder { background: #E3F2FD; color: #1976D2; }
    .role-Caregiver { background: #F3E5F5; color: #7B1FA2; }
    .role-Doctor { background: #E8F5E9; color: #2E7D32; }

    .action-btn { 
      padding: 8px 16px; background: var(--checkins); color: var(--text-main); 
      text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: bold;
    }
    .action-btn:hover { background: #bfe5d7; }
  </style>
</head>

<body>

  <div class="sidebar">
    <h2>ELDERCARE</h2>
    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> Caregivers</a>
    <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> Elders</a>
    <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> Doctors</a>
    <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> Caregiver Links</a>
    <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> Health & AI</a>
    <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> SOS & Emergency</a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
    <a class="nav-btn active" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
    <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <h1>Location Directory</h1>
    <br>

    <input type="text" id="searchInput" class="search-box" placeholder="Search by name, role, or address..." onkeyup="searchTable()">

    <div class="card">
      <table id="locationTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Registered Address</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr><td colspan="5" style="text-align:center;">No user records found.</td></tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><strong>#<?= $u['UserID'] ?></strong></td>
              <td><?= htmlspecialchars($u['FullName']) ?></td>
              <td><span class="role-badge role-<?= $u['RoleName'] ?>"><?= $u['RoleName'] ?></span></td>
              <td>
                <i class="fas fa-home" style="color: var(--sidebar); margin-right: 5px;"></i>
                <?= htmlspecialchars($u['Address'] ?? 'Address not set') ?>
              </td>
              <td>
                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($u['Address']) ?>" target="_blank" class="action-btn">
                  <i class="fas fa-map"></i> Open Map
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function searchTable() {
      const filter = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.querySelectorAll("#locationTable tbody tr");
      rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
      });
    }
  </script>
</body>
</html>