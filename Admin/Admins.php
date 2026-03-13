<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

try {
    
    $query = "SELECT UserID, FullName, Email, Phone, CreatedAt 
              FROM Users 
              WHERE RoleID = 1 
              ORDER BY FullName ASC";

    $stmt = $pdo->query($query);
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) { 
    die("Database Error: " . $e->getMessage()); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | System Administrators</title>
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
        <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
        <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
        <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
        <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> <span>Health & AI</span></a>
        <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> <span>SOS & Emergency</span></a>
        <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
        <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> <span>Location</span></a>
        <a class="nav-btn active" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
        <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>

    <div class="content">
        <div class="header-section">
            <h1>System Administrators</h1>
            <a href="AdminCreate.php" class="btn-add">
                <i class="fas fa-plus"></i> Add New Admin
            </a>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" data-table-search="adminTable" placeholder="Search admins by name or email...">
        </div>

        <div class="card">
            <table id="adminTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Email Address</th>
                        <th>Phone</th>
                        <th>Joined Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($admins) > 0): ?>
                        <?php foreach($admins as $a): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: bold;">#<?= $a['UserID'] ?></td>
                            <td><strong><?= htmlspecialchars($a['FullName']) ?></strong></td>
                            <td><span class="admin-badge">ADMIN</span></td>
                            <td><?= htmlspecialchars($a['Email']) ?></td>
                            <td><?= htmlspecialchars($a['Phone'] ?? 'N/A') ?></td>
                            <td style="color: var(--text-muted);"><?= date('M d, Y', strtotime($a['CreatedAt'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No admins found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


</body>
</html>
