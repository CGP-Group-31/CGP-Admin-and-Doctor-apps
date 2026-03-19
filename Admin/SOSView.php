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
            S.TriggeredAt,
            U.FullName AS ElderName,
            U.Phone AS ElderPhone,
            LT.Latitude,
            LT.Longitude
        FROM SOSLogs S
        JOIN Users U ON S.ElderID = U.UserID
        LEFT JOIN (
            SELECT ElderID, Latitude, Longitude, 
                   ROW_NUMBER() OVER (PARTITION BY ElderID ORDER BY RecordedAt DESC) as rn
            FROM LocationTrack
        ) LT ON S.ElderID = LT.ElderID AND LT.rn = 1
        WHERE S.SOSID = ?";
        
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $alert = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alert) {
        die("<div style='text-align:center; padding:100px; font-family:sans-serif;'>
                <i class='fas fa-search text-muted' style='font-size:3rem;'></i>
                <h2>Case #$id Not Found</h2>
                <a href='SOS.php' class='text-primary'>Return to Logs</a>
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
    <title>Incident Report | #<?= $id ?></title>
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
    <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
    <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> <span>Health & AI</span></a>
    <a class="nav-btn active" href="SOS.php"><i class="fas fa-ambulance"></i> <span>SOS & Emergency</span></a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
  </div>

  <div class="content">
    <div class="report-card view-card">
    <div class="view-header">
        <div>
            <h2 class="view-title">Incident Report</h2>
            <p class="view-subtitle">Reference Log: #<?= $alert['SOSID'] ?></p>
        </div>
        <div class="status-badge">EMERGENCY ACTIVE</div>
    </div>

    <div class="report-body">
        <div class="section">
            <div class="section-title">Elder Subject Details</div>
            <div class="data-grid">
                <div class="data-item">
                    <label>Full Name</label>
                    <p><?= htmlspecialchars($alert['ElderName']) ?></p>
                </div>
                <div class="data-item">
                    <label>Contact Number</label>
                    <p><?= htmlspecialchars($alert['ElderPhone'] ?? 'Not Provided') ?></p>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Incident Timestamp</div>
            <div class="data-grid">
                <div class="data-item">
                    <label>Date Received</label>
                    <p><?= date('F d, Y', strtotime($alert['TriggeredAt'])) ?></p>
                </div>
                <div class="data-item">
                    <label>Exact Time (Local)</label>
                    <p><?= date('h:i:s A', strtotime($alert['TriggeredAt'])) ?></p>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Last Known Geolocation</div>
            <div class="gps-container">
                <div class="gps-info">
                    <i class="fas fa-location-crosshairs"></i>
                    <?php if ($alert['Latitude']): ?>
                        <strong><?= $alert['Latitude'] ?>, <?= $alert['Longitude'] ?></strong>
                    <?php else: ?>
                        <span class="text-muted">No GPS coordinates available for this log.</span>
                    <?php endif; ?>
                </div>
                <?php if ($alert['Latitude']): ?>
                    <a href="https://www.google.com/maps?q=<?= $alert['Latitude'] ?>,<?= $alert['Longitude'] ?>" target="_blank" class="btn-map">
                        <i class="fas fa-map"></i> Open Maps
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="view-actions">
        <a href="SOS.php" class="btn btn-secondary">Back to Emergency Center</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Download PDF Report</button>
    </div>
    </div>
  </div>

</body>
</html>
