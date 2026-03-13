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
            D.DoctorID,
            U.FullName,
            U.Phone,
            D.LicenseNumber,
            D.Specialization,
            D.Hospital
        FROM Doctor D
        JOIN Users U ON D.DoctorID = U.UserID
        WHERE U.RoleID = 2 
        ORDER BY U.FullName ASC";
        
    $stmt = $pdo->query($query);
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Doctors Management</title>
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
        <a class="nav-btn active" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
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
    <h1>Doctor Directory</h1>
    <a href="DoctorCreate.php" class="btn-action">
        <i class="fas fa-plus"></i> Add New Doctor
    </a>
</div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" data-table-search="doctorTable" placeholder="Search by ID, name, or specialty...">
        </div>

        <div class="card">
            <table id="doctorTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>License</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Hospital</th>
                        <th>Contact No</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($doctors)): ?>
                        <tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">No doctors found.</td></tr>
                    <?php else: ?>
                        <?php foreach($doctors as $d): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: bold;">#<?= htmlspecialchars($d['DoctorID']) ?></td>
                            <td><span class="badge-licence"><?= htmlspecialchars($d['LicenseNumber']) ?></span></td>
                            <td><strong><?= htmlspecialchars($d['FullName']) ?></strong></td>
                            <td><?= htmlspecialchars($d['Specialization']) ?></td>
                            <td><i class="fas fa-hospital text-muted"></i> <?= htmlspecialchars($d['Hospital']) ?></td>
                            <td><?= htmlspecialchars($d['Phone']) ?></td>
                            <td><a href="DoctorView.php?id=<?= $d['DoctorID'] ?>" class="btn-action">View Profile</a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


</body>
</html>
