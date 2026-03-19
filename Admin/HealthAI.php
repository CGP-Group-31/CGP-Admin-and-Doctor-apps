<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

$checkinsToday = 0;
$checkinsWeek = 0;
$highRiskWeek = 0;
$missedWeek = 0;
$safetyFlagsWeek = 0;
$aiReportsTotal = 0;
$aiReportsWeek = 0;
$dominantMood = 'N/A';

$recentCheckins = [];
$recentRuns = [];

try {
    $checkinsToday = $pdo->query("SELECT COUNT(*) FROM ElderForm WHERE InfoDate = CAST(GETDATE() AS DATE)")->fetchColumn();

    $checkinsWeek = $pdo->query("SELECT COUNT(*) FROM ElderForm WHERE InfoDate >= DATEADD(day, -6, CAST(GETDATE() AS DATE))")->fetchColumn();

    $highRiskWeek = $pdo->query("
        SELECT COUNT(*)
        FROM ElderForm EF
        CROSS APPLY (
            SELECT
                (CASE WHEN EF.OverallDay = 'Not Good' THEN 2 ELSE 0 END) +
                (CASE WHEN EF.StressLevel = 'Yes' THEN 2 ELSE 0 END) +
                (CASE WHEN EF.LonelinessLevel = 'Always' THEN 2 ELSE 0 END) +
                (CASE WHEN EF.EnergyLevel = 'Low' THEN 1 ELSE 0 END) +
                (CASE WHEN EF.AppetiteLevel = 'Low' THEN 1 ELSE 0 END) +
                (CASE WHEN EF.Mood = 'Sad' THEN 1 ELSE 0 END)
                AS RiskScore
        ) R
        WHERE EF.InfoDate >= DATEADD(day, -6, CAST(GETDATE() AS DATE))
        AND R.RiskScore >= 5
    ")->fetchColumn();

    $missedWeek = $pdo->query("SELECT COUNT(*) FROM CheckInRuns WHERE Status IN ('Missed','Failed') AND CAST(PlannedAt AS DATE) >= DATEADD(day, -6, CAST(GETDATE() AS DATE))")->fetchColumn();

    $safetyFlagsWeek = $pdo->query("SELECT COUNT(*) FROM ChatMessages WHERE SafetyFlag IS NOT NULL AND CAST(CreatedAt AS DATE) >= DATEADD(day, -6, CAST(GETDATE() AS DATE))")->fetchColumn();

    $aiReportsTotal = $pdo->query("SELECT COUNT(*) FROM CareReports")->fetchColumn();
    $aiReportsWeek = $pdo->query("SELECT COUNT(*) FROM CareReports WHERE CAST(GeneratedAt AS DATE) >= DATEADD(day, -6, CAST(GETDATE() AS DATE))")->fetchColumn();

    $dominantMoodStmt = $pdo->query("
        SELECT TOP 1 Mood, COUNT(*) AS Total
        FROM ElderForm
        WHERE InfoDate >= DATEADD(day, -6, CAST(GETDATE() AS DATE))
        GROUP BY Mood
        ORDER BY Total DESC
    ");
    $dominantMoodRow = $dominantMoodStmt->fetch(PDO::FETCH_ASSOC);
    if ($dominantMoodRow && !empty($dominantMoodRow['Mood'])) {
        $dominantMood = $dominantMoodRow['Mood'];
    }

    $recentCheckins = $pdo->query("
        SELECT TOP 12
            EF.CheckInID,
            U.FullName,
            EF.InfoDate,
            EF.Mood,
            EF.StressLevel,
            EF.LonelinessLevel,
            EF.OverallDay,
            EF.EnergyLevel,
            EF.AppetiteLevel,
            PA.PainAreas,
            AC.Activities,
            R.RiskScore,
            CASE
                WHEN R.RiskScore >= 5 THEN 'High'
                WHEN R.RiskScore >= 3 THEN 'Medium'
                ELSE 'Low'
            END AS RiskLevel
        FROM ElderForm EF
        JOIN Users U ON EF.ElderID = U.UserID
        OUTER APPLY (
            SELECT STRING_AGG(PainArea, ', ') AS PainAreas
            FROM ElderFormInPain
            WHERE CheckInID = EF.CheckInID
        ) PA
        OUTER APPLY (
            SELECT STRING_AGG(ActivityName, ', ') AS Activities
            FROM ElderFormActivity
            WHERE CheckInID = EF.CheckInID
        ) AC
        CROSS APPLY (
            SELECT
                (CASE WHEN EF.OverallDay = 'Not Good' THEN 2 ELSE 0 END) +
                (CASE WHEN EF.StressLevel = 'Yes' THEN 2 ELSE 0 END) +
                (CASE WHEN EF.LonelinessLevel = 'Always' THEN 2 ELSE 0 END) +
                (CASE WHEN EF.EnergyLevel = 'Low' THEN 1 ELSE 0 END) +
                (CASE WHEN EF.AppetiteLevel = 'Low' THEN 1 ELSE 0 END) +
                (CASE WHEN EF.Mood = 'Sad' THEN 1 ELSE 0 END)
                AS RiskScore
        ) R
        ORDER BY EF.RecordedAt DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $recentRuns = $pdo->query("
        SELECT TOP 10
            CR.RunID,
            U.FullName,
            CR.Status,
            CR.PlannedAt,
            CR.CompletedAt,
            CR.Notes,
            MT.MoodName AS DetectedMood
        FROM CheckInRuns CR
        JOIN Users U ON CR.ElderID = U.UserID
        LEFT JOIN MoodTypes MT ON CR.DetectedMoodID = MT.MoodID
        ORDER BY CR.TriggeredAt DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("HealthAI Query Error: " . $e->getMessage());
}

function status_class($status) {
    $status = strtolower(trim($status));
    if ($status === 'completed') {
        return 'badge-stable';
    }
    if ($status === 'waitinguser' || $status === 'triggered') {
        return 'status-review';
    }
    if ($status === 'missed' || $status === 'failed') {
        return 'badge-high';
    }
    return 'badge';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | Health & AI Insights</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="assets/theme.css">
  <script src="assets/app.js" defer></script>
</head>

<body class="app">

  <div class="sidebar">
    <h2>TRUSTCARE</h2>

    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> Caregivers</a>
    <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> Elders</a>
    <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> Doctors</a>
    <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> Caregiver Links</a>
    <a class="nav-btn active" href="HealthAI.php"><i class="fas fa-robot"></i> Health & AI</a>
    <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> SOS & Emergency</a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <div class="header-flex">
        <div class="header-left">
            <h1>Health & AI Insights</h1>
            <p class="text-muted">Latest wellness signals and automated check-in activity</p>
        </div>
        <div class="header-right">
            <span class="ai-badge"><i class="fas fa-microchip"></i> AI Engine Active</span>
            <span class="badge" style="margin-left: 10px;"><i class="fas fa-smile"></i> Dominant Mood: <?= htmlspecialchars($dominantMood) ?></span>
            <span class="badge" style="margin-left: 10px;"><i class="fas fa-file-alt"></i> Reports 7 Days: <?= (int)$aiReportsWeek ?></span>
        </div>
    </div>

    <div class="cards">
      <div class="card">
        <i class="fas fa-heartbeat"></i>
        <h2>Check-ins Today</h2>
        <p><?= (int)$checkinsToday ?></p>
      </div>
      <div class="card">
        <i class="fas fa-calendar-check"></i>
        <h2>Check-ins (7 Days)</h2>
        <p><?= (int)$checkinsWeek ?></p>
      </div>
      <div class="card danger-border">
        <i class="fas fa-exclamation-triangle text-danger"></i>
        <h2>High-Risk (7 Days)</h2>
        <p><?= (int)$highRiskWeek ?></p>
      </div>
      <div class="card">
        <i class="fas fa-user-clock"></i>
        <h2>Missed / Failed (7 Days)</h2>
        <p><?= (int)$missedWeek ?></p>
      </div>
      <div class="card">
        <i class="fas fa-shield-heart"></i>
        <h2>Safety Flags (7 Days)</h2>
        <p><?= (int)$safetyFlagsWeek ?></p>
      </div>
      <div class="card">
        <i class="fas fa-file-alt"></i>
        <h2>AI Reports (Total)</h2>
        <p><?= (int)$aiReportsTotal ?></p>
      </div>
    </div>

    <div class="table-card">
      <div class="table-header">
          <i class="fas fa-robot"></i> Recent Wellness Check-ins
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Elder</th>
            <th>Date</th>
            <th>Mood</th>
            <th>Stress</th>
            <th>Loneliness</th>
            <th>Pain Areas</th>
            <th>Activities</th>
            <th>Risk</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentCheckins)): ?>
            <tr><td colspan="9" class="no-data">No check-ins available.</td></tr>
          <?php else: ?>
            <?php foreach ($recentCheckins as $row): ?>
            <tr>
              <td><span class="id-badge">#<?= (int)$row['CheckInID'] ?></span></td>
              <td><strong><?= htmlspecialchars($row['FullName']) ?></strong></td>
              <td><?= date('M d, Y', strtotime($row['InfoDate'])) ?></td>
              <td><?= htmlspecialchars($row['Mood']) ?></td>
              <td><?= htmlspecialchars($row['StressLevel']) ?></td>
              <td><?= htmlspecialchars($row['LonelinessLevel']) ?></td>
              <td><?= $row['PainAreas'] ? htmlspecialchars($row['PainAreas']) : '<span class="text-muted">None</span>' ?></td>
              <td><?= $row['Activities'] ? htmlspecialchars($row['Activities']) : '<span class="text-muted">None</span>' ?></td>
              <td>
                <?php if ($row['RiskLevel'] === 'High'): ?>
                  <span class="sev sev-high">HIGH</span>
                <?php elseif ($row['RiskLevel'] === 'Medium'): ?>
                  <span class="sev sev-med">MEDIUM</span>
                <?php else: ?>
                  <span class="sev sev-low">LOW</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="table-card">
      <div class="table-header">
          <i class="fas fa-comments"></i> Recent AI Check-in Runs
      </div>
      <table>
        <thead>
          <tr>
            <th>Run ID</th>
            <th>Elder</th>
            <th>Status</th>
            <th>Planned</th>
            <th>Completed</th>
            <th>Detected Mood</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentRuns)): ?>
            <tr><td colspan="7" class="no-data">No check-in runs found.</td></tr>
          <?php else: ?>
            <?php foreach ($recentRuns as $row): ?>
            <tr>
              <td><span class="id-badge">#<?= (int)$row['RunID'] ?></span></td>
              <td><strong><?= htmlspecialchars($row['FullName']) ?></strong></td>
              <td><span class="badge <?= status_class($row['Status']) ?>"><?= htmlspecialchars($row['Status']) ?></span></td>
              <td><?= date('M d, Y h:i A', strtotime($row['PlannedAt'])) ?></td>
              <td><?= $row['CompletedAt'] ? date('M d, Y h:i A', strtotime($row['CompletedAt'])) : '<span class="text-muted">Pending</span>' ?></td>
              <td><?= $row['DetectedMood'] ? htmlspecialchars($row['DetectedMood']) : '<span class="text-muted">N/A</span>' ?></td>
              <td><?= $row['Notes'] ? htmlspecialchars($row['Notes']) : '<span class="text-muted">No notes</span>' ?></td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
