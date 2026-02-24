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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    try {
        $type = $_POST['relationship_type'];
        $isPrimary = isset($_POST['is_primary']) ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE careRelationships SET RelationshipType = ?, IsPrimary = ? WHERE RelationshipID = ?");
        $stmt->execute([$type, $isPrimary, $id]);

        echo "<script>alert('Link updated successfully'); window.location.href='CaregiverLinks.php';</script>";
        exit;
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}


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
        die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h2>Assignment Not Found</h2>
                <p>The Relationship ID #$id does not exist.</p>
                <a href='CaregiverLinks.php'>Back to List</a>
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
    <title>View Assignment Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #1F6F78; --bg: #F6F7F3; --danger: #C62828; }
        body { background: var(--bg); font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; padding: 40px; }
        .card { background: white; width: 100%; max-width: 500px; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .header i { font-size: 3rem; color: var(--sidebar); }
        
        .detail-group { background: #f9f9f9; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .label { font-weight: bold; color: #666; font-size: 14px; }
        .val { font-weight: 600; color: #333; }

        label { display: block; margin-top: 15px; font-weight: bold; font-size: 14px; }
        input[type="text"] { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; }
        .check-group { display: flex; align-items: center; gap: 10px; margin-top: 20px; }

        .btn-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; }
        .save { background: #D6EFE6; color: #1E2A2A; }
        .delete { background: var(--danger); color: white; }
        .back { background: #eee; color: #333; }
    </style>
</head>
<body>

<div class="card">
    <div class="header">
        <i class="fas fa-link"></i>
        <h2>Assignment Details</h2>
    </div>

    <div class="detail-group">
        <div class="row"><span class="label">Elder:</span> <span class="val"><?= htmlspecialchars($link['ElderName']) ?></span></div>
        <div class="row"><span class="label">Caregiver:</span> <span class="val"><?= htmlspecialchars($link['CaregiverName']) ?></span></div>
        <div class="row"><span class="label">Phone:</span> <span class="val"><?= htmlspecialchars($link['CaregiverPhone']) ?></span></div>
    </div>

    <form method="POST">
        <label>Relationship Type</label>
        <input type="text" name="relationship_type" value="<?= htmlspecialchars($link['RelationshipType']) ?>">

        <div class="check-group">
            <input type="checkbox" name="is_primary" id="pri" <?= $link['IsPrimary'] ? 'checked' : '' ?>>
            <label for="pri" style="margin:0;">Set as Primary Caregiver</label>
        </div>

        <div class="btn-group">
            <button type="submit" name="update" class="btn save">Save Changes</button>
            <button type="button" class="btn delete" onclick="confirmDelete()">Remove Link</button>
            <a href="CaregiverLinks.php" class="btn back">Back</a>
        </div>
    </form>
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