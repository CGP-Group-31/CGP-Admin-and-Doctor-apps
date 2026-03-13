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
    <link rel="stylesheet" href="assets/theme.css">
  
    <script src="assets/app.js" defer></script>
</head>

<body class="app">

  <div class="sidebar">
    <h2>TRUSTCARE</h2>
    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> Caregivers</a>
    <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> Elders</a>
    <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> Doctors</a>
    <a class="nav-btn active" href="CaregiverLinks.php"><i class="fas fa-link"></i> Caregiver Links</a>
    <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> Health & AI</a>
    <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> SOS & Emergency</a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
    <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <h1>Caregiver Assignments</h1>
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
              <td><span class="text-muted">#<?= htmlspecialchars($row['RelationshipID']) ?></span></td>
              <td><strong><?= htmlspecialchars($row['ElderName']) ?></strong></td>
              <td><?= htmlspecialchars($row['CaregiverName']) ?></td>
              <td class="relationship-text"><?= htmlspecialchars($row['RelationshipType']) ?></td>
              <td>
                <?php if ($row['IsPrimary'] == 1): ?>
                    <span class="badge primary"><i class="fas fa-star" style="font-size: 8px;"></i> PRIMARY</span>
                <?php else: ?>
                    <span class="badge secondary">SECONDARY</span>
                <?php endif; ?>
              </td>
              <td style="color: var(--text-muted);"><?= date('M d, Y', strtotime($row['CreatedAt'])) ?></td>
              <td>
                <a href="CaregiverLinksView.php?id=<?= $row['RelationshipID'] ?>" class="btn-view">View Details</a>
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
