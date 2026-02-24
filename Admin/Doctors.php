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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        .sidebar { 
            width: var(--sidebar-width); background: var(--sidebar-color); color: #fff; 
            height: 100vh; position: fixed; left: 0; top: 0; 
            display: flex; flex-direction: column; z-index: 1000; overflow-y: auto; 
        }
        .sidebar h2 { padding: 25px 20px; text-align: center; font-size: 1.5rem; background: rgba(0,0,0,0.1); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-btn { padding: 12px 20px; text-decoration: none; color: rgba(255,255,255,0.8); font-size: 14px; display: flex; align-items: center; transition: 0.3s; border-left: 4px solid transparent; }
        .nav-btn i { margin-right: 12px; width: 20px; text-align: center; }
        .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.1); color: #fff; border-left: 4px solid var(--accent); }
        .logout { margin-top: auto; background: var(--sos); justify-content: center; font-weight: bold; padding: 15px; }

        .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; }
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-box h1 { color: var(--sidebar-color); font-size: 2rem; }

        .search-box { position: relative; margin-bottom: 25px; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .search-box input { padding: 12px 15px 12px 45px; border-radius: 30px; border: 1px solid #ddd; width: 400px; outline: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

        .card { background: var(--card); border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f1f5f9; padding: 18px 15px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--text-muted); letter-spacing: 1px; }
        tbody td { padding: 18px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        tbody tr:hover { background-color: #fcfdfe; }

        .badge-licence { background: #e0f2f1; color: #00796b; padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: bold; }
        .btn-action { padding: 8px 16px; background: var(--sidebar-color); color: white; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; transition: 0.3s; }
        .btn-action:hover { background: #165057; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ELDERCARE</h2>
        <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
        <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
        <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
        <a class="nav-btn active" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
        <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
        <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> <span>Health & AI</span></a>
        <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> <span>SOS & Emergency</span></a>
        <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
        <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> <span>Location</span></a>
        <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>

    <div class="content">
        <div class="header-box">
            <h1>Doctor Directory</h1>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search by ID, name, or specialty...">
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
                            <td><i class="fas fa-hospital" style="color: #999;"></i> <?= htmlspecialchars($d['Hospital']) ?></td>
                            <td><?= htmlspecialchars($d['Phone']) ?></td>
                            <td><a href="DoctorView.php?id=<?= $d['DoctorID'] ?>" class="btn-action">View Profile</a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#doctorTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>