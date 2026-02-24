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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    try {
        $pdo->beginTransaction();

        $stmt1 = $pdo->prepare("UPDATE Users SET FullName = ?, Email = ?, Phone = ? WHERE UserID = ?");
        $stmt1->execute([$_POST['full_name'], $_POST['email'], $_POST['phone'], $id]);

        $newCG = intval($_POST['caregiver_id']);
        if ($newCG > 0) {

            $pdo->prepare("DELETE FROM CareRelationships WHERE ElderID = ?")->execute([$id]);

            $pdo->prepare("INSERT INTO CareRelationships (ElderID, CaregiverID) VALUES (?, ?)")->execute([$id, $newCG]);
        }

        $pdo->commit();
        header("Location: Elders.php?msg=updated");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Update Error: " . $e->getMessage();
    }
}


$caregiversList = $pdo->query("SELECT UserID, FullName FROM Users WHERE RoleID = 4")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT U.*, CR.CaregiverID FROM Users U LEFT JOIN CareRelationships CR ON U.UserID = CR.ElderID WHERE U.UserID = ?");
$stmt->execute([$id]);
$elder = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$elder) {
    die("Elder not found in database.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Elder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #1F6F78; --bg: #F6F7F3; --card: #FFFFFF; --sos: #C62828; --checkins: #D6EFE6; }
        body { background: var(--bg); display: flex; justify-content: center; padding: 40px; font-family: 'Segoe UI', sans-serif; }
        .card { background: var(--card); width: 600px; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: var(--sidebar); margin-bottom: 20px; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #444; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; margin-top: 5px; box-sizing: border-box; }
        .buttons { margin-top: 30px; display: flex; gap: 10px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; }
        .update-btn { background: var(--checkins); color: #1E2A2A; }
        .delete-btn { background: var(--sos); color: #fff; }
        .cancel-btn { background: #eee; color: #333; }
    </style>
</head>
<body>

<div class="card">
    <h2>Elder Profile</h2>
    
    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($elder['FullName']) ?>" required>

        <label>Age (Visible)</label>
        <input type="number" name="age" value="72">

        <label>Assigned Caregiver</label>
        <select name="caregiver_id">
            <option value="0">-- Select New Caregiver --</option>
            <?php foreach($caregiversList as $cg): ?>
                <option value="<?= $cg['UserID'] ?>" <?= ($elder['CaregiverID'] == $cg['UserID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cg['FullName']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Phone Number</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($elder['Phone'] ?? '') ?>">

        <label>Email Address</label>
        <input type="email" name="email" value="<?= htmlspecialchars($elder['Email'] ?? '') ?>">

        <div class="buttons">
            <button type="submit" name="update" class="btn update-btn">Save Changes</button>
            <button type="button" class="btn delete-btn" onclick="confirmDeletion()">Delete Forever</button>
            <a href="Elders.php" class="btn cancel-btn">Cancel</a>
        </div>
    </form>
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