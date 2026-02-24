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
            S.SOSID,
            U.FullName AS ElderName,
            CASE 
                WHEN S.TriggerTypeID = 1 THEN 'Panic Button'
                WHEN S.TriggerTypeID = 2 THEN 'Fall Detected'
                WHEN S.TriggerTypeID = 3 THEN 'Medical Alert'
                ELSE 'Unknown'
            END AS SOSType,
            S.TriggeredAt,
            'Active' as Status
        FROM SOSLogs S
        JOIN Users U ON S.ElderID = U.UserID
        ORDER BY S.TriggeredAt DESC";
        
    $stmt = $pdo->query($query);
    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SOS & Emergency | Admin</title>
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
      --reminder: #E6B450;
      --sos: #C62828;
      --btn-color: var(--checkins);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }

    body { display: flex; min-height: 100vh; background: var(--bg); }


    .sidebar { width: 240px; background: var(--sidebar); color: #fff; display: flex; flex-direction: column; position: fixed; height: 100vh; }
    .sidebar h2 { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); }
    .nav-btn { padding: 14px 20px; text-decoration: none; color: #fff; font-size: 14px; display: flex; align-items: center; transition: 0.3s; }
    .nav-btn i { margin-right: 10px; width: 20px; text-align: center; }
    .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.15); }
    .logout { margin-top: auto; background: var(--sos); text-align: center; font-weight: bold; }


    .content { flex: 1; margin-left: 240px; padding: 40px; }
   h1 { 
    color: var(--sidebar);
    margin-bottom: 20px; 
    font-size: 2rem; 
    font-weight: 700;
}
    .subtitle { color: var(--text-muted); margin-bottom: 25px; font-size: 14px; }


    .card { background: var(--card); padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
    th { background: #f8fafb; color: var(--text-muted); text-transform: uppercase; font-size: 12px; }


    .type-badge { font-weight: bold; color: var(--sos); display: flex; align-items: center; gap: 8px; }
    .status-active { color: #2e7d32; font-weight: bold; background: #e8f5e9; padding: 4px 8px; border-radius: 4px; font-size: 12px; }

    .action-btn { 
        padding: 8px 16px; 
        border: none; 
        border-radius: 6px; 
        cursor: pointer; 
        font-size: 12px; 
        background: var(--checkins); 
        color: var(--text-main); 
        text-decoration: none;
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
    <a class="nav-btn active" href="SOS.php"><i class="fas fa-ambulance"></i> SOS & Emergency</a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
    <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
    <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <h1>SOS & Emergency Logs</h1>
    <br>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Elder Name</th>
            <th>SOS Type</th>
            <th>Time Triggered</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($alerts)): ?>
            <tr><td colspan="6" style="text-align:center; padding: 30px;">No emergency logs found.</td></tr>
          <?php else: ?>
            <?php foreach ($alerts as $row): ?>
            <tr>
              <td><strong>#<?= $row['SOSID'] ?></strong></td>
              <td><?= htmlspecialchars($row['ElderName']) ?></td>
              <td class="type-badge">
                <i class="fas fa-exclamation-circle"></i> 
                <?= htmlspecialchars($row['SOSType']) ?>
              </td>
              <td><?= date('h:i A | M d', strtotime($row['TriggeredAt'])) ?></td>
              <td><span class="status-active"><?= $row['Status'] ?></span></td>
              <td>
                <a href="SOSView.php?id=<?= $row['SOSID'] ?>" class="action-btn">View Case</a>
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