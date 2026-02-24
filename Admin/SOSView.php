<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $query = "
        SELECT 
            S.SOSID,
            U.FullName AS ElderName,
            U.Phone AS ElderPhone,
            CASE 
                WHEN S.TriggerTypeID = 1 THEN 'Panic Button'
                WHEN S.TriggerTypeID = 2 THEN 'Fall Detected'
                WHEN S.TriggerTypeID = 3 THEN 'Medical Alert'
                ELSE 'Unknown'
            END AS SOSType,
            S.TriggeredAt
        FROM SOSLogs S
        JOIN Users U ON S.ElderID = U.UserID
        WHERE S.SOSID = ?";
        
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $alert = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alert) {
        die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h2>SOS Case Not Found</h2>
                <a href='SOS.php'>Return to Logs</a>
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
    <title>SOS Incident Report | #<?= $id ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #1F6F78; --bg: #F6F7F3; --sos: #C62828; --text: #1E2A2A; }
        body { background: var(--bg); font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; padding: 40px; margin: 0; }
        .report-container { background: white; width: 100%; max-width: 600px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .report-header { background: var(--sos); color: white; padding: 30px; text-align: center; }
        .report-header i { font-size: 3rem; margin-bottom: 10px; }
        .report-header h2 { margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        .report-body { padding: 30px; }
        .section-title { font-size: 12px; text-transform: uppercase; color: #888; letter-spacing: 1px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-item label { display: block; font-size: 13px; color: #666; margin-bottom: 4px; }
        .info-item span { font-size: 16px; font-weight: 600; color: var(--text); }
        .description-box { background: #fff9f9; border-left: 4px solid var(--sos); padding: 20px; border-radius: 4px; margin-bottom: 30px; }
        .description-box p { margin: 0; line-height: 1.6; color: #444; }
        .footer-btns { display: flex; gap: 10px; }
        .btn { flex: 1; padding: 14px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; }
        .btn-back { background: #eee; color: #333; }
        .btn-print { background: var(--sidebar); color: white; }
    </style>
</head>
<body>

<div class="report-container">
    <div class="report-header">
        <i class="fas fa-exclamation-triangle"></i>
        <h2>Emergency Incident Report</h2>
        <p>Case ID: #<?= $alert['SOSID'] ?></p>
    </div>

    <div class="report-body">
        <div class="section-title">Elder Information</div>
        <div class="info-grid">
            <div class="info-item">
                <label>Name</label>
                <span><?= htmlspecialchars($alert['ElderName']) ?></span>
            </div>
            <div class="info-item">
                <label>Contact Number</label>
                <span><?= htmlspecialchars($alert['ElderPhone']) ?></span>
            </div>
        </div>

        <div class="section-title">Incident Details</div>
        <div class="info-grid">
            <div class="info-item">
                <label>Trigger Type</label>
                <span style="color: var(--sos);"><?= htmlspecialchars($alert['SOSType']) ?></span>
            </div>
            <div class="info-item">
                <label>Time of Trigger</label>
                <span><?= date('h:i:s A | M d, Y', strtotime($alert['TriggeredAt'])) ?></span>
            </div>
        </div>

        <div class="section-title">Narrative Summary</div>
        <div class="description-box">
            <p>
                At <strong><?= date('H:i', strtotime($alert['TriggeredAt'])) ?></strong>, an emergency signal was received 
                for <strong><?= htmlspecialchars($alert['ElderName']) ?></strong>. The alert was classified as 
                a <strong><?= htmlspecialchars($alert['SOSType']) ?></strong>. 
                This report serves as a formal log of the emergency trigger event.
            </p>
        </div>

        <div class="footer-btns">
            <a href="SOS.php" class="btn btn-back">Return to Logs</a>
            <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Print Report</button>
        </div>
    </div>
</div>

</body>
</html>