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
            S.TriggeredAt,
            U.FullName AS ElderName,
            LT.Latitude,
            LT.Longitude
        FROM SOSLogs S
        JOIN Users U ON S.ElderID = U.UserID
        LEFT JOIN (
            SELECT ElderID, Latitude, Longitude, 
                   ROW_NUMBER() OVER (PARTITION BY ElderID ORDER BY RecordedAt DESC) as rn
            FROM LocationTrack
        ) LT ON S.ElderID = LT.ElderID AND LT.rn = 1
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SOS & Emergency | Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">
  
    <script src="assets/app.js" defer></script>
</head>
<body class="app">

  <div class="sidebar">
    <h2>TRUSTCARE</h2>
    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
    <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
    <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
    <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
    <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> <span>Health & AI</span></a>
    <a class="nav-btn active" href="SOS.php"><i class="fas fa-ambulance"></i> <span>SOS & Emergency</span></a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>    
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
  </div>

  <div class="content">
    <h1>SOS</h1>
    
    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Elder Name</th>
            <th>Last Known Location</th>
            <th>Triggered Time</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($alerts)): ?>
            <tr><td colspan="5" style="text-align:center; padding: 30px;">No emergency records found.</td></tr>
          <?php else: ?>
            <?php foreach ($alerts as $row): ?>
            <tr>
              <td><span class="id-badge">#<?= $row['SOSID'] ?></span></td>
              <td><strong><?= htmlspecialchars($row['ElderName']) ?></strong></td>
              <td>
                <?php if ($row['Latitude']): ?>
                    <a href="https://www.google.com/maps?q=<?= $row['Latitude'] ?>,<?= $row['Longitude'] ?>" target="_blank" class="location-box">
                        <i class="fas fa-map-marker-alt"></i> <?= $row['Latitude'] ?>, <?= $row['Longitude'] ?>
                    </a>
                <?php else: ?>
                    <span class="text-muted">No GPS Data</span>
                <?php endif; ?>
              </td>
              <td class="time-text">
                <i class="far fa-clock"></i> <?= date('h:i A | M d', strtotime($row['TriggeredAt'])) ?>
              </td>
              <td>
                <a href="SOSView.php?id=<?= $row['SOSID'] ?>" class="text-primary link-strong">View Details</a>
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
