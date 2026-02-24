<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_response'])) {
    try {
        $newStatus = $_POST['status'];
        // Update the status in the database
        $stmt = $pdo->prepare("UPDATE Complaints SET Status = ? WHERE ComplaintID = ?");
        $stmt->execute([$newStatus, $id]);
        
        echo "<script>alert('Complaint status updated to " . $newStatus . "'); window.location.href='Complains.php';</script>";
        exit;
    } catch (PDOException $e) {
        $error = "Error updating record: " . $e->getMessage();
    }
}
try {
    $query = "
        SELECT 
            C.*, 
            U.FullName AS ComplainantName, 
            U.Phone AS ComplainantPhone,
            U.Email AS ComplainantEmail
        FROM Complaints C
        JOIN Users U ON C.ComplainantID = U.UserID
        WHERE C.ComplaintID = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $complain = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$complain) {
        die("<div style='text-align:center; padding:500px; font-family:sans-serif;'><h2>Complaint Not Found</h2><a href='Complains.php'>Return to List</a></div>");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Respond to Complaint #<?= $id ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #1F6F78; --bg: #F6F7F3; --white: #FFFFFF; --text: #1E2A2A; --muted: #6F7F7D; --warning: #E6B450; }
        body { background: var(--bg); font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; padding: 40px; margin: 0; }
        
        .respond-card { background: var(--white); width: 100%; max-width: 700px; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); overflow: hidden; }
        
        .top-bar { background: var(--sidebar); color: white; padding: 30px; position: relative; }
        .top-bar h2 { margin: 0; font-size: 1.5rem; }
        .case-id { position: absolute; right: 30px; top: 35px; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }

        .content-body { padding: 40px; }
        .section-header { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; color: var(--sidebar); font-weight: bold; font-size: 14px; text-transform: uppercase; }
        
        .original-message { background: #f0f4f4; border-radius: 12px; padding: 25px; margin-bottom: 30px; border-left: 5px solid var(--warning); }
        .subject-line { font-weight: 800; font-size: 18px; margin-bottom: 10px; color: var(--text); }
        .message-body { color: #444; line-height: 1.7; font-size: 15px; }

        .meta-info { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; background: #fafafa; padding: 15px; border-radius: 8px; }
        .meta-item label { display: block; font-size: 11px; color: var(--muted); text-transform: uppercase; margin-bottom: 4px; }
        .meta-item span { font-weight: 600; font-size: 14px; }

        form label { display: block; margin-top: 20px; font-weight: bold; font-size: 14px; color: var(--text); }
        select, textarea { width: 100%; padding: 12px; margin-top: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        textarea { height: 120px; }

        .actions { display: flex; gap: 15px; margin-top: 30px; }
        .btn { flex: 1; padding: 15px; border: none; border-radius: 10px; font-weight: bold; cursor: pointer; text-decoration: none; text-align: center; transition: 0.3s; }
        .btn-submit { background: var(--sidebar); color: white; }
        .btn-cancel { background: #eee; color: #333; }
        .btn:hover { filter: brightness(1.1); transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="respond-card">
    <div class="top-bar">
        <h2>Process Complaint</h2>
        <div class="case-id">TICKET #<?= $id ?></div>
    </div>

    <div class="content-body">
        <div class="section-header"><i class="fas fa-user"></i> Complainant Information</div>
        <div class="meta-info">
            <div class="meta-item">
                <label>Name</label>
                <span><?= htmlspecialchars($complain['ComplainantName']) ?></span>
            </div>
            <div class="meta-item">
                <label>Date Filed</label>
                <span><?= date('M d, Y', strtotime($complain['CreatedAt'])) ?></span>
            </div>
            <div class="meta-item">
                <label>Phone</label>
                <span><?= htmlspecialchars($complain['ComplainantPhone']) ?></span>
            </div>
            <div class="meta-item">
                <label>Email</label>
                <span><?= htmlspecialchars($complain['ComplainantEmail'] ?? 'N/A') ?></span>
            </div>
        </div>

        <div class="section-header"><i class="fas fa-envelope-open-text"></i> Complaint Details</div>
        <div class="original-message">
            <div class="subject-line"><?= htmlspecialchars($complain['Subject']) ?></div>
            <div class="message-body"><?= nl2br(htmlspecialchars($complain['Description'])) ?></div>
        </div>

        <form method="POST">
            <div class="section-header"><i class="fas fa-clipboard-check"></i> Resolution Action</div>
            
            <label>Set Current Status</label>
            <select name="status">
                <option value="Pending" <?= $complain['Status'] == 'Pending' ? 'selected' : '' ?>>Pending (New)</option>
                <option value="Under Review" <?= $complain['Status'] == 'Under Review' ? 'selected' : '' ?>>Under Review (In Progress)</option>
                <option value="Resolved" <?= $complain['Status'] == 'Resolved' ? 'selected' : '' ?>>Resolved (Completed)</option>
            </select>

            <label>Admin Internal Notes</label>
            <textarea placeholder="Describe the outcome or steps taken to address this complaint..."></textarea>

            <div class="actions">
                <a href="Complains.php" class="btn btn-cancel">Discard Changes</a>
                <button type="submit" name="submit_response" class="btn btn-submit">Update & Save Ticket</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>