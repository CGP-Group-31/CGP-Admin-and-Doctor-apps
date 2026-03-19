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


        try { $pdo->prepare("DELETE FROM Appointments WHERE DoctorID = ?")->execute([$id]); } catch (Exception $e) {}
        try { $pdo->prepare("DELETE FROM Appointments WHERE CreatedBy = ?")->execute([$id]); } catch (Exception $e) {}

        $pdo->prepare("DELETE FROM Doctor WHERE DoctorID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM UserLogins WHERE UserID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM Users WHERE UserID = ? AND RoleID = 2")->execute([$id]);

        $pdo->commit();

        echo "<script>alert('Doctor record removed successfully'); window.location.href='Doctors.php';</script>";
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Delete Error: " . $e->getMessage());
    }
}

// Updates are disabled for sensitive profile details.


$query = "
    SELECT U.FullName, U.Phone, D.* FROM Users U 
    JOIN Doctor D ON U.UserID = D.DoctorID 
    WHERE U.UserID = ? AND U.RoleID = 2";

$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    die("<div class='card' style='max-width:520px; margin:40px auto; text-align:center;'>
            <h2>Doctor Not Found</h2>
            <p>Could not find a record for ID #$id.</p>
            <a href='Doctors.php' class='text-primary'>Return to Directory</a>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor Profile</title>
    
    <script src="assets/app.js" defer></script>
</head>
<body class="app">
  <div class="sidebar">
    <h2>TRUSTCARE</h2>
    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
    <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
    <a class="nav-btn active" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
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
          <h2 class="view-title">Doctor Profile</h2>
          <p class="view-subtitle">Professional details and credentials.</p>
        </div>
        <span class="badge">Profile Locked</span>
      </div>
    
    <form method="POST" class="form-section">
        <div class="input-group">
          <label>Full Name</label>
          <input type="text" name="full_name" value="<?= htmlspecialchars($doctor['FullName']) ?>" class="readonly-field" readonly>
        </div>

        <div class="input-group">
          <label>Medical License Number</label>
          <input type="text" name="license" value="<?= htmlspecialchars($doctor['LicenseNumber']) ?>" class="readonly-field" readonly>
        </div>

        <div class="input-group">
          <label>Specialization</label>
          <input type="text" name="specialization" value="<?= htmlspecialchars($doctor['Specialization']) ?>" class="readonly-field" readonly>
        </div>

        <div class="input-group">
          <label>Hospital</label>
          <input type="text" name="hospital" value="<?= htmlspecialchars($doctor['Hospital']) ?>" class="readonly-field" readonly>
        </div>

        <div class="input-group">
          <label>Contact Number</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($doctor['Phone']) ?>" class="readonly-field" readonly>
        </div>

        <div class="view-actions">
            <a href="Doctors.php" class="btn back">Back</a>
            <button type="button" class="btn delete-btn" onclick="deleteDoc()">Delete</button>
        </div>
    </form>
    </div>
  </div>

<script>
function deleteDoc() {
    if (confirm("Permanently delete this doctor?")) {
        window.location.href = "DoctorView.php?id=<?= $id ?>&action=delete";
    }
}
</script>

</body>
</html>
