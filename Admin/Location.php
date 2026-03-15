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
            u.UserID, 
            u.FullName, 
            lt.Latitude,
            lt.Longitude,
            lt.RecordedAt
        FROM Users u
        LEFT JOIN (
            SELECT ElderID, Latitude, Longitude, RecordedAt,
                   ROW_NUMBER() OVER (PARTITION BY ElderID ORDER BY RecordedAt DESC) as rn
            FROM locationtrack
        ) lt ON u.UserID = lt.ElderID AND lt.rn = 1
        WHERE u.RoleID = 5
        ORDER BY u.FullName ASC";
        
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Location Directory | Admin</title>
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
        <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> Health & AI</a>
        <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> SOS & Emergency</a>
        <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
        <a class="nav-btn active" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
        <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> Manage Admins</a>
        <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <h1>Location Directory</h1>
        <p class="subtitle">Monitor real-time GPS coordinates for elders.</p>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" data-table-search="locationTable" placeholder="Search by name, role, or coordinates...">
        </div>

        <div class="card">
            <table id="locationTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Current GPS Coordinates</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="5" style="text-align:center; padding: 40px; color: var(--text-muted);">No records found in the database.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="text-muted" style="font-weight: bold;">#<?= $u['UserID'] ?></td>
                            <td><strong><?= htmlspecialchars($u['FullName']) ?></strong></td>
                            <td class="gps-data">
                                <?php if (!empty($u['Latitude'])): ?>
                                    <span class="gps-coords">
                                        <i class="fas fa-satellite-dish text-primary" style="margin-right: 5px;"></i>
                                        <?= $u['Latitude'] ?>, <?= $u['Longitude'] ?>
                                    </span>
                                    <span class="gps-time">
                                        Last Updated: <?= date('M d, g:i A', strtotime($u['RecordedAt'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="no-data"><i class="fas fa-exclamation-triangle"></i> No GPS Signal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($u['Latitude'])): ?>
                                    <a href="https://www.google.com/maps?q=<?= $u['Latitude'] ?>,<?= $u['Longitude'] ?>" target="_blank" class="action-btn">
                                        <i class="fas fa-map-marked-alt"></i> View on Map
                                    </a>
                                <?php else: ?>
                                    <a href="#" class="action-btn btn-disabled">
                                        <i class="fas fa-location-arrow"></i> Tracking Off
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


</body>
</html>
