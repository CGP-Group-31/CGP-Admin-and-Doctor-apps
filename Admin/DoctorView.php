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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    try {
        $pdo->beginTransaction();

 
        $stmt1 = $pdo->prepare("UPDATE Users SET FullName = ?, Phone = ? WHERE UserID = ?");
        $stmt1->execute([$_POST['full_name'], $_POST['phone'], $id]);

 
        $stmt2 = $pdo->prepare("UPDATE Doctor SET LicenseNumber = ?, Specialization = ?, Hospital = ? WHERE DoctorID = ?");
        $stmt2->execute([$_POST['license'], $_POST['specialization'], $_POST['hospital'], $id]);

        $pdo->commit();
        echo "<script>alert('Changes saved successfully'); window.location.href='Doctors.php';</script>";
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Update Error: " . $e->getMessage();
    }
}


$query = "
    SELECT U.FullName, U.Phone, D.* FROM Users U 
    JOIN Doctor D ON U.UserID = D.DoctorID 
    WHERE U.UserID = ? AND U.RoleID = 2";

$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2>Doctor Not Found</h2>
            <p>Could not find a record for ID #$id.</p>
            <a href='Doctors.php'>Return to Directory</a>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Doctor Profile</title>
    <style>
        :root { --primary: #1F6F78; --bg: #F6F7F3; --danger: #C62828; }
        body { background: var(--bg); font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; padding: 40px; }
        .card { background: white; width: 100%; max-width: 500px; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h2 { color: var(--primary); text-align: center; margin-bottom: 25px; }
        label { display: block; margin-top: 15px; font-weight: bold; font-size: 13px; color: #555; }
        input { width: 100%; padding: 12px; margin-top: 5px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; }
        .btn-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn { flex: 1; padding: 13px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; text-align: center; }
        .save { background: #D6EFE6; color: #1E2A2A; }
        .delete { background: var(--danger); color: white; }
        .back { background: #eee; color: #333; }
    </style>
</head>
<body>

<div class="card">
    <h2>Doctor Profile #<?= $id ?></h2>
    
    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($doctor['FullName']) ?>" required>

        <label>Medical License Number</label>
        <input type="text" name="license" value="<?= htmlspecialchars($doctor['LicenseNumber']) ?>">

        <label>Specialization</label>
        <input type="text" name="specialization" value="<?= htmlspecialchars($doctor['Specialization']) ?>">

        <label>Hospital</label>
        <input type="text" name="hospital" value="<?= htmlspecialchars($doctor['Hospital']) ?>">

        <label>Contact Number</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($doctor['Phone']) ?>">

        <div class="btn-group">
            <button type="submit" name="update" class="btn save">Save Changes</button>
            <button type="button" class="btn delete" onclick="deleteDoc()">Delete</button>
            <a href="Doctors.php" class="btn back">Back</a>
        </div>
    </form>
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