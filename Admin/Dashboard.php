<?php
session_start();
require 'include/db.php';


if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}


$caregivers = 0; $elders = 0; $activeToday = 0;
$sosToday = 0; $missedMeds = 0; $highRisk = 0;

try {
    $caregivers = $pdo->query("SELECT COUNT(*) FROM Users WHERE RoleID = 4 AND IsActive = 1")->fetchColumn();
    
    $elders = $pdo->query("SELECT COUNT(*) FROM Users WHERE RoleID NOT IN (1, 2, 4) AND IsActive = 1")->fetchColumn();
    
    $activeToday = $pdo->query("SELECT COUNT(DISTINCT UserID) FROM UserLogins WHERE CAST(LoginTime AS DATE) = CAST(GETDATE() AS DATE)")->fetchColumn();
    $sosToday = $pdo->query("SELECT COUNT(*) FROM SOSLogs WHERE CAST(TriggeredAt AS DATE) = CAST(GETDATE() AS DATE)")->fetchColumn();
    $missedMeds = $pdo->query("SELECT COUNT(*) FROM MedicationAdherence WHERE StatusID = 3 AND CAST(ScheduledFor AS DATE) = CAST(GETDATE() AS DATE)")->fetchColumn();
    

    $highRisk = $pdo->query("
        SELECT COUNT(DISTINCT VR.ElderID) 
        FROM VitalRecords VR
        JOIN Users U ON VR.ElderID = U.UserID
        WHERE U.RoleID NOT IN (1, 2, 4) 
        AND ((VitalTypeID = 1 AND Value > 100) OR (VitalTypeID = 2 AND Value > 140))
    ")->fetchColumn();

} catch (PDOException $e) {
    error_log("Dashboard Query Error: " . $e->getMessage());
}

$sosLabels = [];
$sosData = [];
try {
    $graphQuery = "SELECT CAST(TriggeredAt AS DATE) as ReportDate, COUNT(*) as Total FROM SOSLogs WHERE TriggeredAt >= DATEADD(day, -6, CAST(GETDATE() AS DATE)) GROUP BY CAST(TriggeredAt AS DATE) ORDER BY ReportDate ASC";
    $results = $pdo->query($graphQuery)->fetchAll(PDO::FETCH_ASSOC);
    for ($i = 6; $i >= 0; $i--) {
        $targetDate = date('Y-m-d', strtotime("-$i days"));
        $sosLabels[] = date('D', strtotime($targetDate));
        $count = 0;
        foreach ($results as $row) {
            $dbDate = ($row['ReportDate'] instanceof DateTime) ? $row['ReportDate']->format('Y-m-d') : substr($row['ReportDate'], 0, 10);
            if ($dbDate === $targetDate) { $count = (int)$row['Total']; break; }
        }
        $sosData[] = $count;
    }
} catch (Exception $e) { $sosLabels = ['Error']; $sosData = [0]; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Dashboard Overview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">
    
    <script src="assets/app.js" defer></script>
</head>
<body class="app">

<div class="sidebar">
    <h2>TRUSTCARE</h2>
    <a class="nav-btn active" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
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
        <div class="header-box">
            <h1>Dashboard Overview</h1>
            <p style="color: var(--text-muted);">System status and critical alerts for <?= date('F j, Y') ?></p>
        </div>

        <div class="card-grid">
            <div class="card">
                <a href="Caregivers.php" class="card-link">
                    <h3><i class="fas fa-users-cog"></i> Caregivers</h3>
                    <p data-count="<?= $caregivers ?>"><?= $caregivers ?></p>
                </a>
            </div>
            <div class="card">
                <a href="Elders.php" class="card-link">
                    <h3><i class="fas fa-blind"></i> Elders</h3>
                    <p data-count="<?= $elders ?>"><?= $elders ?></p>
                </a>
            </div>
            <div class="card active-users">
                <h3><i class="fas fa-signal"></i> Active Today</h3>
                <p data-count="<?= $activeToday ?>"><?= $activeToday ?></p>
            </div>
            <div class="card sos-card">
                <a href="SOS.php" class="card-link">
                    <h3><i class="fas fa-bell"></i> SOS Alerts</h3>
                    <p data-count="<?= $sosToday ?>"><?= $sosToday ?></p>
                </a>
            </div>
            <div class="card">
                <h3><i class="fas fa-pills"></i> Missed Meds</h3>
                <p data-count="<?= $missedMeds ?>"><?= $missedMeds ?></p>
            </div>
            <div class="card risk-card">
                <h3><i class="fas fa-exclamation-triangle"></i> High Risk</h3>
                <p data-count="<?= $highRisk ?>"><?= $highRisk ?></p>
            </div>
        </div>

        <div class="graph-container">
            <span class="graph-title">Emergency Trends (Last 7 Days)</span>
            <div class="graph-box">
                <canvas id="sosChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('sosChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($sosLabels); ?>,
                datasets: [{
                    label: 'SOS Activations',
                    data: <?= json_encode($sosData); ?>,
                    borderColor: '#C62828',
                    backgroundColor: 'rgba(198, 40, 40, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#C62828'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>
