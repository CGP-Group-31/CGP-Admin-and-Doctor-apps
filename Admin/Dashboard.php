<?php
session_start();
require 'include/db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

// Stats Initializers
$caregivers = 0; $elders = 0; $activeToday = 0;
$sosToday = 0; $missedMeds = 0; $highRisk = 0;

try {
    // Caregivers stay as RoleID 4
    $caregivers = $pdo->query("SELECT COUNT(*) FROM Users WHERE RoleID = 4 AND IsActive = 1")->fetchColumn();
    
    // NEW: Count any user who is NOT Admin (1), Doctor (2), or Caregiver (4)
    $elders = $pdo->query("SELECT COUNT(*) FROM Users WHERE RoleID NOT IN (1, 2, 4) AND IsActive = 1")->fetchColumn();
    
    $activeToday = $pdo->query("SELECT COUNT(DISTINCT UserID) FROM UserLogins WHERE CAST(LoginTime AS DATE) = CAST(GETDATE() AS DATE)")->fetchColumn();
    $sosToday = $pdo->query("SELECT COUNT(*) FROM SOSLogs WHERE CAST(TriggeredAt AS DATE) = CAST(GETDATE() AS DATE)")->fetchColumn();
    $missedMeds = $pdo->query("SELECT COUNT(*) FROM MedicationAdherence WHERE StatusID = 3 AND CAST(ScheduledFor AS DATE) = CAST(GETDATE() AS DATE)")->fetchColumn();
    
    // NEW: Updated high risk to look for the same "Elder" group
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

// --- SOS Graph Logic ---
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
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-color: #1F6F78;
            --bg: #F6F7F3;
            --card: #FFFFFF;
            --text-main: #1E2A2A;
            --text-muted: #6F7F7D;
            --accent: #E6B450;
            --sos: #C62828;
            --success: #2E7D32;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        /* FIXED SIDEBAR */
        .sidebar { 
            width: var(--sidebar-width); background: var(--sidebar-color); color: #fff; 
            height: 100vh; position: fixed; left: 0; top: 0; 
            display: flex; flex-direction: column; z-index: 1000; overflow-y: auto; 
        }
        .sidebar h2 { padding: 25px 20px; text-align: center; font-size: 1.5rem; background: rgba(0,0,0,0.1); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-btn { 
            padding: 12px 20px; text-decoration: none; color: rgba(255,255,255,0.8); font-size: 14px; 
            display: flex; align-items: center; transition: 0.3s; border-left: 4px solid transparent; 
        }
        .nav-btn i { margin-right: 12px; width: 20px; text-align: center; font-size: 16px; }
        .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.1); color: #fff; border-left: 4px solid var(--accent); }
        .logout { margin-top: auto; background: var(--sos); justify-content: center; font-weight: bold; padding: 15px; }

        /* CONTENT AREA */
        .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; }
        .header-box { margin-bottom: 30px; }
        .header-box h1 { font-size: 2rem; color: var(--sidebar-color); }

        /* DASHBOARD CARDS */
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: var(--card); padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-bottom: 4px solid transparent; transition: 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .card.active-users { border-color: #4AA0A1; }
        .card.sos-card { border-color: var(--sos); }
        .card.risk-card { border-color: var(--accent); }
        .card h3 { font-size: 13px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .card p { font-size: 32px; font-weight: 800; color: var(--text-main); }

        /* GRAPH */
        .graph-container { background: var(--card); border-radius: 15px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .graph-title { font-weight: bold; color: var(--text-main); margin-bottom: 20px; display: block; }
        .graph-box { height: 350px; position: relative; }

        @media (max-width: 1024px) {
            .sidebar { width: 70px; }
            .sidebar h2, .nav-btn span { display: none; }
            .content { margin-left: 70px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ELDERCARE</h2>
        <a class="nav-btn active" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
        <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
        <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
        <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
        <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
        <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> <span>Health & AI</span></a>
        <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> <span>SOS & Emergency</span></a>
        <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
        <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> <span>Location</span></a>
        <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>

    <div class="content">
        <div class="header-box">
            <h1>Dashboard Overview</h1>
            <p style="color: var(--text-muted);">System status and critical alerts for <?= date('F j, Y') ?></p>
        </div>

        <div class="card-grid">
            <div class="card">
                <h3><i class="fas fa-users-cog"></i> Caregivers</h3>
                <p><?= $caregivers ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-blind"></i> Elders</h3>
                <p><?= $elders ?></p>
            </div>
            <div class="card active-users">
                <h3><i class="fas fa-signal"></i> Active Today</h3>
                <p><?= $activeToday ?></p>
            </div>
            <div class="card sos-card">
                <h3><i class="fas fa-bell"></i> SOS Alerts</h3>
                <p><?= $sosToday ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-pills"></i> Missed Meds</h3>
                <p><?= $missedMeds ?></p>
            </div>
            <div class="card risk-card">
                <h3><i class="fas fa-exclamation-triangle"></i> High Risk</h3>
                <p><?= $highRisk ?></p>
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