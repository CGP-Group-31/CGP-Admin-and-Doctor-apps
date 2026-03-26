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
        $pdo->beginTransaction();

        $pdo->prepare("DELETE FROM Appointments WHERE ElderID = ?")->execute([$id]);

        $pdo->prepare("DELETE FROM MedicationSchedules WHERE MedicationID IN (SELECT MedicationID FROM Medications WHERE ElderID = ?)")->execute([$id]);

        $pdo->prepare("DELETE FROM MedicationAdherence WHERE ElderID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM VitalRecords WHERE ElderID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM SOSLogs WHERE ElderID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM Medications WHERE ElderID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM UserDevices WHERE UserID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM UserLogins WHERE UserID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM CareRelationships WHERE ElderID = ?")->execute([$id]);


        $pdo->prepare("DELETE FROM Users WHERE UserID = ?")->execute([$id]);

        $pdo->commit();
        header("Location: Elders.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Delete Error: " . $e->getMessage());
    }
}

// Updates are disabled for sensitive profile details.


$caregiversList = $pdo->query("SELECT UserID, FullName FROM Users WHERE RoleID = 4")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT U.*, CR.CaregiverID FROM Users U LEFT JOIN CareRelationships CR ON U.UserID = CR.ElderID WHERE U.UserID = ?");
$stmt->execute([$id]);
$elder = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$elder) {
    die("<div class='card' style='max-width:520px; margin:40px auto; text-align:center;'>Elder not found in database.</div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Elder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">
    
    <script src="assets/app.js" defer></script>
</head>
<body class="app">
  <div class="sidebar">
    <h2>TRUSTCARE</h2>
    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
    <a class="nav-btn active" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
    <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
    <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
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
          <h2 class="view-title">Elder Profile</h2>
          <p class="view-subtitle">Personal details and caregiver assignment.</p>
        </div>
        <span class="badge">Profile Locked</span>
      </div>
    
    <form method="POST" class="form-section">
        <div class="input-group">
          <label>Full Name</label>
          <input type="text" name="full_name" value="<?= htmlspecialchars($elder['FullName']) ?>" class="readonly-field" readonly>
        </div>

        <div class="input-group">
          <label>Age</label>
          <input type="text" value="Hidden" class="readonly-field" readonly>
        </div>

        <div class="input-group">
          <label>Assigned Caregiver</label>
          <select name="caregiver_id" disabled>
              <option value="0">-- Select New Caregiver --</option>
              <?php foreach($caregiversList as $cg): ?>
                  <option value="<?= $cg['UserID'] ?>" <?= ($elder['CaregiverID'] == $cg['UserID']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($cg['FullName']) ?>
                  </option>
              <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group">
          <label>Phone Number</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($elder['Phone'] ?? '') ?>" class="readonly-field" readonly>
        </div>

        <div class="input-group">
          <label>Email Address</label>
          <input type="email" name="email" value="<?= htmlspecialchars($elder['Email'] ?? '') ?>" class="readonly-field" readonly>
        </div>

        <div class="view-actions">
            <a href="Elders.php" class="btn cancel-btn">Back</a>
            <button type="button" class="btn delete-btn" onclick="confirmDeletion()">Delete Forever</button>
        </div>
    </form>
    </div>
  </div>

<script>
function confirmDeletion() {
    if (confirm("Are you absolutely sure? This will delete the elder and all their history.")) {
        window.location.href = "EldersView.php?id=<?= $id ?>&action=delete";
    }
}
</script>

</body>
</html>
