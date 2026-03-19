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
            U.UserID, 
            U.FullName, 
            U.Phone, 
            U.Email,
            U.IsActive,
            (SELECT COUNT(*) FROM CareRelationships CR WHERE CR.CaregiverID = U.UserID) as AssignedCount
        FROM Users U
        WHERE U.RoleID = 4
        ORDER BY U.FullName ASC
    ";
    $stmt = $pdo->query($query);
    $caregivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Caregiver Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">

    
    <script src="assets/app.js" defer></script>
</head>
<body class="app">

    <div class="sidebar">
        <h2>TRUSTCARE</h2>
        <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
        <a class="nav-btn active" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
        <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
        <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
        <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
        <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> <span>Health & AI</span></a>
        <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> <span>SOS & Emergency</span></a>
        <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
        <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
        <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>

    <div class="content">
        <div class="header-section">
            <h1>Caregiver Directory</h1>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" data-table-search="caregiverTable" placeholder="Search by name, email or phone...">
        </div>

        <div class="card">
            <table id="caregiverTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact Info</th>
                        <th>Status</th>
                        <th>Assignments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($caregivers as $c): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($c['UserID']) ?></td>
                        <td><strong><?= htmlspecialchars($c['FullName']) ?></strong></td>
                        <td>
                            <div style="margin-bottom: 4px;"><i class="fas fa-envelope text-muted" style="font-size:12px;"></i> <?= htmlspecialchars($c['Email']) ?></div>
                            <div><i class="fas fa-phone text-muted" style="font-size:12px;"></i> <?= htmlspecialchars($c['Phone']) ?></div>
                        </td>
                        <td>
                            <span class="badge <?= $c['IsActive'] ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $c['IsActive'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <span style="font-weight: 600;"><i class="fas fa-users text-primary" style="margin-right: 5px;"></i> <?= $c['AssignedCount'] ?></span>
                        </td>
                        <td>
                            <a href="CaregiversView.php?id=<?= $c['UserID'] ?>" class="btn-view">View Profile</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


</body>
</html>
