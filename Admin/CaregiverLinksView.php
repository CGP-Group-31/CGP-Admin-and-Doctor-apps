<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (isset($_GET['action']) && $_GET['action'] == 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM careRelationships WHERE RelationshipID = ?");
        $stmt->execute([$id]);
        
        echo "<script>alert('Assignment removed successfully'); window.location.href='CaregiverLinks.php';</script>";
        exit;
    } catch (PDOException $e) {
        die("Delete Error: " . $e->getMessage());
    }
}

// Updates are disabled for relationship details.


try {
    $query = "
        SELECT 
            CR.*, 
            U1.FullName AS ElderName, 
            U2.FullName AS CaregiverName,
            U2.Phone AS CaregiverPhone
        FROM careRelationships CR
        JOIN Users U1 ON CR.ElderID = U1.UserID
        JOIN Users U2 ON CR.CaregiverID = U2.UserID
        WHERE CR.RelationshipID = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $link = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$link) {
        die("<div class='card' style='max-width:520px; margin:40px auto; text-align:center;'>
                <h2>Assignment Not Found</h2>
                <p>The Relationship ID #$id does not exist.</p>
                <a href='CaregiverLinks.php' class='text-primary'>Back to List</a>
             </div>");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignment Details</title>
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
    <a class="nav-btn active" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
    <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> <span>Health & AI</span></a>
    <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> <span>SOS & Emergency</span></a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
  </div>

  <div class="content">
    <div class="card view-card">
      <div class="view-header">
        <div>
          <h2 class="view-title">Assignment Details</h2>
          <p class="view-subtitle">Caregiver pairing overview.</p>
        </div>
        <span class="badge"><?= $link['IsPrimary'] ? 'Primary Caregiver' : 'Secondary Caregiver' ?></span>
      </div>

      <div class="detail-grid">
        <div class="detail-card">
          <span class="label">Elder</span>
          <span class="val"><?= htmlspecialchars($link['ElderName']) ?></span>
        </div>
        <div class="detail-card">
          <span class="label">Caregiver</span>
          <span class="val"><?= htmlspecialchars($link['CaregiverName']) ?></span>
        </div>
        <div class="detail-card">
          <span class="label">Phone</span>
          <span class="val"><?= htmlspecialchars($link['CaregiverPhone']) ?></span>
        </div>
      </div>

      <form method="POST" class="form-section">
        <div class="input-group">
          <label>Relationship Type</label>
          <input type="text" name="relationship_type" value="<?= htmlspecialchars($link['RelationshipType']) ?>" class="readonly-field" readonly>
        </div>

        <label class="check-pill">
          <input type="checkbox" name="is_primary" <?= $link['IsPrimary'] ? 'checked' : '' ?> disabled>
          <span>Set as Primary Caregiver</span>
        </label>

        <div class="view-actions">
          <a href="CaregiverLinks.php" class="btn back">Back</a>
          <button type="button" class="btn delete-btn" onclick="confirmDelete()">Remove Link</button>
        </div>
      </form>
    </div>
  </div>

<script>
function confirmDelete() {
    if (confirm("Are you sure you want to remove this assignment?")) {
        window.location.href = "CaregiverLinksView.php?id=<?= $id ?>&action=delete";
    }
}
</script>

</body>
</html>
