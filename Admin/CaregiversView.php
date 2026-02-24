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
        die("<div style='text-align:center; padding:50px;'><h2>Caregiver not found!</h2><a href='Caregivers.php'>Go Back</a></div>");
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
  <style>
    :root { --sidebar: #1F6F78; --bg: #F6F7F3; --card: #FFFFFF; --text-main: #1E2A2A; --text-muted: #6F7F7D; --sos: #C62828; }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
    body { background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
    .card { background: var(--card); width: 100%; max-width: 550px; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    h1 { color: var(--sidebar); text-align: center; margin-bottom: 10px; font-size: 24px; }
    .subtitle { text-align: center; color: var(--text-muted); margin-bottom: 30px; font-size: 14px; }
    label { display: block; margin: 15px 0 5px; color: var(--text-main); font-weight: 600; font-size: 14px; }
    input, select { width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #ddd; font-size: 15px; margin-bottom: 10px; }
    .readonly-field { background-color: #f9f9f9; color: #777; cursor: not-allowed; }
    .buttons { margin-top: 35px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .btn { padding: 12px; border: none; border-radius: 8px; font-size: 14px; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; }
    .update-btn { background: var(--sidebar); color: #fff; grid-column: span 2; }
    .delete-btn { background: #fee2e2; color: var(--sos); }
    .cancel-btn { background: #eee; color: #555; }
    .error-msg { background: #fee2e2; color: var(--sos); padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
  </style>
</head>
<body>

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

  <script>
    function confirmDelete(id) {
        if (confirm("WARNING: This is a HARD DELETE. It will remove this caregiver and ALL medication records they created. Continue?")) {
            window.location.href = "CaregiversView.php?id=" + id + "&action=delete";
        }
    }
  </script>

</body>
</html>