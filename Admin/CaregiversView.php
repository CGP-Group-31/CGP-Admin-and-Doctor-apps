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

        $pdo->prepare("DELETE FROM MedicationSchedules WHERE MedicationID IN (SELECT MedicationID FROM Medications WHERE CreatedBy = ?)")->execute([$id]);

        $pdo->prepare("DELETE FROM Medications WHERE CreatedBy = ?")->execute([$id]);

        $pdo->prepare("DELETE FROM UserDevices WHERE UserID = ?")->execute([$id]);

        $pdo->prepare("DELETE FROM UserLogins WHERE UserID = ?")->execute([$id]);

        $pdo->prepare("DELETE FROM SOSLogs WHERE ElderID = ?")->execute([$id]);


        $pdo->prepare("DELETE FROM CareRelationships WHERE CaregiverID = ? OR ElderID = ?")->execute([$id, $id]);

        $pdo->prepare("DELETE FROM Users WHERE UserID = ? AND RoleID = 4")->execute([$id]);

        $pdo->commit();
        header("Location: Caregivers.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();

        $error = "Hard Delete failed at step: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    try {
        $status = ($_POST['status'] == 'Active') ? 1 : 0;
        $updateQuery = "UPDATE Users SET FullName = ?, Phone = ?, Email = ?, IsActive = ? WHERE UserID = ? AND RoleID = 4";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([$_POST['full_name'], $_POST['phone'], $_POST['email'], $status, $id]);
        header("Location: Caregivers.php?status=success");
        exit;
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}


try {
    $query = "SELECT *, (SELECT COUNT(*) FROM CareRelationships WHERE CaregiverID = U.UserID) as assigned_elders 
              FROM Users U WHERE U.UserID = ? AND U.RoleID = 4";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $caregiver = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$caregiver) {
        die("<div class='card' style='max-width:520px; margin:40px auto; text-align:center;'><h2>Caregiver not found!</h2><a href='Caregivers.php' class='text-primary'>Go Back</a></div>");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | View Caregiver</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">
  
    <script src="assets/app.js" defer></script>
</head>
<body class="app">
  <div class="sidebar">
    <h2>TRUSTCARE</h2>
    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
    <a class="nav-btn active" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
    <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
    <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
    <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
    <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> <span>Health & AI</span></a>
    <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> <span>SOS & Emergency</span></a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
    <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> <span>Location</span></a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
  </div>

  <div class="content">
    <div class="card">
      <h1>Caregiver Profile</h1>


    <?php if(isset($error)): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <label><i class="fas fa-user"></i> Full Name</label>
      <input type="text" name="full_name" value="<?= htmlspecialchars($caregiver['FullName']) ?>" required>

      <label><i class="fas fa-phone"></i> Phone Number</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($caregiver['Phone']) ?>">

      <label><i class="fas fa-envelope"></i> Email Address</label>
      <input type="email" name="email" value="<?= htmlspecialchars($caregiver['Email']) ?>">

      <label><i class="fas fa-toggle-on"></i> Account Status</label>
      <select name="status">
        <option value="Active" <?= $caregiver['IsActive'] == 1 ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= $caregiver['IsActive'] == 0 ? 'selected' : '' ?>>Inactive</option>
      </select>

      <label><i class="fas fa-users"></i> Assigned Elders (Count)</label>
      <input type="text" class="readonly-field" value="<?= $caregiver['assigned_elders'] ?>" readonly>

      <div class="buttons">
        <button type="submit" name="update" class="btn update-btn">Save All Changes</button>
        <button type="button" class="btn delete-btn" onclick="confirmDelete(<?= $caregiver['UserID'] ?>)">
            <i class="fas fa-trash"></i> Delete
        </button>
        <a href="Caregivers.php" class="btn cancel-btn">Cancel</a>
      </div>
    </form>
    </div>
  </div>

  <script>
    function confirmDelete(id) {
        if (confirm("WARNING: This is a HARD DELETE. It will remove this caregiver and ALL medication records they created. Continue?")) {
            window.location.href = "CaregiversView.php?id=" + id + "&action=delete";
        }
    }
  </script>

</body>
</html>
