<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

try {
    $query = "
        SELECT 
            E.UserID, 
            E.FullName, 
            E.Phone,
            DATEDIFF(YEAR, E.DateOfBirth, GETDATE()) AS Age,
            -- Get Caregiver Name
            (SELECT TOP 1 C.FullName 
             FROM CareRelationships CR 
             JOIN Users C ON CR.CaregiverID = C.UserID 
             WHERE CR.ElderID = E.UserID) as CaregiverName,
            -- Get Risk Status
            (SELECT TOP 1 
                CASE 
                    WHEN (VitalTypeID = 1 AND Value > 100) OR (VitalTypeID = 2 AND Value > 140) THEN 'High' 
                    ELSE 'Stable' 
                END 
             FROM VitalRecords VR 
             WHERE VR.ElderID = E.UserID 
             ORDER BY RecordedAt DESC) as RiskStatus
        FROM Users E 
        -- This ensures we only get people NOT classified as Admin, Doctor, or Caregiver
        WHERE E.RoleID NOT IN (1, 2, 4) 
        ORDER BY E.FullName ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $elders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) { 
    die("Database Error: " . $e->getMessage()); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Elders Directory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">
    
    <script src="assets/app.js" defer></script>
</head>
<body class="app">

    <div class="sidebar">
        <h2>TRUSTCARE</h2>
        <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
        <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
        <a class="nav-btn active" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
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
        <div class="header-section">
            <h1>Elder Directory</h1>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" data-table-search="elderTable" placeholder="Search by name, ID or caregiver...">
        </div>

        <div class="card">
            <table id="elderTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Risk Status</th>
                        <th>Caregiver</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($elders) > 0): ?>
                        <?php foreach($elders as $e): ?>
                        <tr>
                            <td>#<?= $e['UserID'] ?></td>
                            <td><strong><?= htmlspecialchars($e['FullName']) ?></strong></td>
                            <td><?= $e['Age'] ?> Years</td>
                            <td>
                                <span class="badge <?= ($e['RiskStatus'] == 'High') ? 'badge-high' : 'badge-stable' ?>">
                                    <i class="fas <?= ($e['RiskStatus'] == 'High') ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
                                    <?= $e['RiskStatus'] ?? 'Stable' ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 600;">
                                    <i class="fas fa-user-nurse text-muted" style="margin-right: 5px;"></i>
                                    <?= htmlspecialchars($e['CaregiverName'] ?? 'Unassigned') ?>
                                </span>
                            </td>
                            <td>
                                <a href="EldersView.php?id=<?= $e['UserID'] ?>" class="btn-view">
                                    <i class="fas fa-eye"></i> View Profile
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No elders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


</body>
</html>
