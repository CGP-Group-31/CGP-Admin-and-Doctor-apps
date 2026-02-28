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
            C.ComplaintID,
            U.FullName AS ComplainantName,
            C.Subject,
            C.Description,
            C.Status,
            C.CreatedAt
        FROM Complaints C
        JOIN Users U ON C.ComplainantID = U.UserID
        ORDER BY C.CreatedAt DESC";
        
    $stmt = $pdo->query($query);
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | Complaints Management</title>
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
      --warning: #E6B450;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }

    body { display: flex; min-height: 100vh; background: var(--bg); }


    .sidebar { width: 240px; background: var(--sidebar); color: #fff; display: flex; flex-direction: column; position: fixed; height: 100vh; }
    .sidebar h2 { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); font-size: 1.2rem; }
    .nav-btn { padding: 14px 20px; text-decoration: none; color: #fff; font-size: 14px; display: flex; align-items: center; transition: 0.3s; }
    .nav-btn i { margin-right: 10px; width: 20px; text-align: center; }
    .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.15); border-left: 4px solid var(--warning); }
    .logout { margin-top: auto; background: var(--sos); text-align: center; font-weight: bold; }
    .content { flex: 1; margin-left: 240px; padding: 40px; }
h1 { 
    color: var(--sidebar); 
    margin-bottom: 20px; 
    font-size: 2rem; 
    font-weight: 700;
}
    .subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 25px; }

 
    .card { background: var(--card); padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
    th { background: #f8fafb; color: var(--text-muted); text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }


    .status-badge { padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    .status-pending { background: #fff3e0; color: #ef6c00; }
    .status-resolved { background: #e8f5e9; color: #2e7d32; }
    .status-review { background: #e3f2fd; color: #1976d2; }

    .action-btn { 
        padding: 8px 14px; 
        background: var(--checkins); 
        color: var(--text-main); 
        text-decoration: none; 
        border-radius: 6px; 
        font-size: 12px; 
        font-weight: 600;
        transition: 0.2s;
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
    <a class="nav-btn active" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
    <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <h1>Complaints & Feedback</h1>
    <br>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Complainant</th>
            <th>Subject</th>
            <th>Date Reported</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($complaints)): ?>
            <tr><td colspan="6" style="text-align:center; padding: 30px;">No complaints found.</td></tr>
          <?php else: ?>
            <?php foreach ($complaints as $row): ?>
            <tr>
              <td><strong>#<?= $row['ComplaintID'] ?></strong></td>
              <td><?= htmlspecialchars($row['ComplainantName']) ?></td>
              <td><?= htmlspecialchars($row['Subject']) ?></td>
              <td style="color: var(--text-muted);"><?= date('M d, Y | h:i A', strtotime($row['CreatedAt'])) ?></td>
              <td>
                <?php 
                  $statusClass = 'status-pending';
                  $statusText = $row['Status'];
                  if(strtolower($statusText) == 'resolved') $statusClass = 'status-resolved';
                  if(strtolower($statusText) == 'under review') $statusClass = 'status-review';
                ?>
                <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
              </td>
              <td>
                  <a href="ComplainRespond.php?id=<?= $row['ComplaintID'] ?>" class="action-btn">Respond</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>