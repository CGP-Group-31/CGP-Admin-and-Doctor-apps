<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

try {
    // We join careRelationships with the Users table twice:
    // 1. Once to get the Elder's FullName (U1)
    // 2. Once to get the Caregiver's FullName (U2)
    $query = "
        SELECT 
            CR.RelationshipID,
            U1.FullName AS ElderName,
            U2.FullName AS CaregiverName,
            CR.RelationshipType,
            CR.IsPrimary,
            CR.CreatedAt
        FROM careRelationships CR
        JOIN Users U1 ON CR.ElderID = U1.UserID
        JOIN Users U2 ON CR.CaregiverID = U2.UserID
        ORDER BY CR.CreatedAt DESC";
        
    $stmt = $pdo->query($query);
    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Caregiver Links | Admin</title>
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
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
    
    body { display: flex; height: 100vh; background: var(--bg); }

    /* ===== SIDEBAR ===== */
    .sidebar { width: 240px; background: var(--sidebar); color: #fff; display: flex; flex-direction: column; position: fixed; height: 100vh; }
    .sidebar h2 { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); }
    .nav-btn { padding: 14px 20px; text-decoration: none; color: #fff; font-size: 14px; display: flex; align-items: center; }
    .nav-btn i { margin-right: 10px; width: 20px; text-align: center; }
    .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.15); }
    .logout { margin-top: auto; background: var(--sos); text-align: center; font-weight: bold; }

    /* ===== CONTENT ===== */
    .content { flex: 1; margin-left: 240px; padding: 40px; overflow-y: auto; }
    h1 { color: var(--text-main); margin-bottom: 20px; font-size: 2rem; }

    /* ===== TABLE ===== */
    .card { background: var(--card); padding: 20px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
    th { background: #f8fafb; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-size: 12px; }

    /* STATUS BADGES */
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
    .primary { background: #E3F2FD; color: #1976D2; }
    .secondary { background: #F5F5F5; color: #757575; }

    .view-btn { 
        padding: 6px 12px; 
        background: var(--sidebar); 
        color: white; 
        text-decoration: none; 
        border-radius: 4px; 
        font-size: 12px; 
    }
  </style>
</head>

<body>

  <div class="sidebar">
    <h2>ELDERCARE</h2>
    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> Caregivers</a>
    <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> Elders</a>
    <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> Doctors</a>
    <a class="nav-btn active" href="CaregiverLinks.php"><i class="fas fa-link"></i> Caregiver Links</a>
    <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> Health & AI</a>
    <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> SOS & Emergency</a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
    <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
    <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <h1>Caregiver Assignments</h1>
    <p style="color: var(--text-muted); margin-bottom: 25px;">Managing relationships between Elders and their assigned Caregivers.</p>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Elder Name</th>
            <th>Assigned Caregiver</th>
            <th>Relationship</th>
            <th>Status</th>
            <th>Date Linked</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          <?php if (empty($links)): ?>
            <tr><td colspan="7" style="text-align:center;">No links found.</td></tr>
          <?php else: ?>
            <?php foreach ($links as $row): ?>
            <tr>
              <td><strong>#<?= $row['RelationshipID'] ?></strong></td>
              <td><?= htmlspecialchars($row['ElderName']) ?></td>
              <td><?= htmlspecialchars($row['CaregiverName']) ?></td>
              <td><?= htmlspecialchars($row['RelationshipType']) ?></td>
              <td>
                <?php if ($row['IsPrimary'] == 1): ?>
                    <span class="badge primary">PRIMARY</span>
                <?php else: ?>
                    <span class="badge secondary">SECONDARY</span>
                <?php endif; ?>
              </td>
              <td style="color: var(--text-muted);"><?= date('M d, Y', strtotime($row['CreatedAt'])) ?></td>
              <td>
                <a href="ViewLink.php?id=<?= $row['RelationshipID'] ?>" class="view-btn">View Details</a>
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