<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

try {
    /** * SQL Logic:
     * 1. JOIN Users with locationtrack.
     * 2. Use ROW_NUMBER() to grab ONLY the most recent GPS ping for each user.
     * 3. Exclude Admins (RoleID 1) from the tracking list.
     */
    $query = "
        SELECT 
            u.UserID, 
            u.FullName, 
            u.RoleID,
            CASE 
                WHEN u.RoleID = 2 THEN 'Doctor'
                WHEN u.RoleID = 4 THEN 'Caregiver'
                ELSE 'Elder'
            END AS RoleName,
            lt.Latitude,
            lt.Longitude,
            lt.RecordedAt
        FROM Users u
        LEFT JOIN (
            SELECT ElderID, Latitude, Longitude, RecordedAt,
                   ROW_NUMBER() OVER (PARTITION BY ElderID ORDER BY RecordedAt DESC) as rn
            FROM locationtrack
        ) lt ON u.UserID = lt.ElderID AND lt.rn = 1
        WHERE u.RoleID != 1
        ORDER BY u.RoleID ASC, u.FullName ASC";
        
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
    <style>
        :root {
            --sidebar: #1F6F78;
            --bg: #F4F5F0;
            --card: #FFFFFF;
            --text-main: #243333;
            --text-muted: #6F7F7D;
            --accent: #E6B450;
            --wellbeing: #D6EFE6;
            --sos: #C62828;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; min-height: 100vh; background: var(--bg); color: var(--text-main); }

        /* Sidebar */
        .sidebar { width: 260px; background: var(--sidebar); color: #fff; display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 1000; }
        .sidebar h2 { padding: 25px 20px; text-align: center; background: rgba(0,0,0,0.1); border-bottom: 1px solid rgba(255,255,255,0.1); font-size: 1.5rem; }
        .nav-btn { padding: 14px 20px; text-decoration: none; color: rgba(255,255,255,0.8); font-size: 14px; display: flex; align-items: center; transition: 0.3s; border-left: 4px solid transparent; }
        .nav-btn i { margin-right: 12px; width: 20px; text-align: center; }
        .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.1); color: #fff; border-left: 4px solid var(--accent); }
        .logout { margin-top: auto; background: var(--sos); padding: 15px; text-align: center; font-weight: bold; }

        /* Content Area */
        .content { flex: 1; margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        h1 { color: var(--sidebar); font-size: 2rem; font-weight: 800; margin-bottom: 5px; }
        .subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 30px; }

        .search-box { position: relative; margin-bottom: 25px; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .search-box input { 
            width: 100%; max-width: 450px; padding: 12px 15px 12px 45px; 
            border-radius: 8px; border: 1px solid #ddd; outline: none; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.03); 
        }

        /* Table Styling */
        .card { background: var(--card); border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 18px 15px; text-align: left; }
        th { background: #f8fafb; color: var(--text-muted); text-transform: uppercase; font-size: 11px; letter-spacing: 1px; border-bottom: 2px solid var(--bg); }
        td { border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        tr:hover { background-color: #fcfdfe; }

        /* Badges */
        .role-badge { padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .role-Elder { background: #E3F2FD; color: #1976D2; }
        .role-Caregiver { background: #F3E5F5; color: #7B1FA2; }
        .role-Doctor { background: #E8F5E9; color: #2E7D32; }

        /* GPS Status */
        .gps-data { line-height: 1.4; }
        .gps-coords { font-weight: 600; color: var(--text-main); display: block; }
        .gps-time { font-size: 11px; color: var(--text-muted); display: block; }
        .no-data { color: var(--sos); font-size: 12px; font-weight: 600; }

        /* Button */
        .action-btn { 
            padding: 8px 16px; background: var(--sidebar); color: white; 
            text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 700;
            display: inline-flex; align-items: center; gap: 8px; transition: 0.3s;
        }
        .action-btn:hover { background: #165057; box-shadow: 0 4px 10px rgba(31, 111, 120, 0.2); }
        .btn-disabled { background: #eee; color: #aaa; cursor: not-allowed; pointer-events: none; }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>ELDERCARE</h2>
        <a class="nav-btn" href="Dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> Caregivers</a>
        <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> Elders</a>
        <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> Doctors</a>
        <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> Caregiver Links</a>
        <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> Health & AI</a>
        <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> SOS & Emergency</a>
        <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
        <a class="nav-btn active" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
        <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> Manage Admins</a>
        <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <h1>Location Directory</h1>
        <p class="subtitle">Monitor real-time GPS coordinates for elders and staff.</p>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search by name, role, or coordinates..." onkeyup="searchTable()">
        </div>

        <div class="card">
            <table id="locationTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Role</th>
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
                            <td style="color: var(--text-muted); font-weight: bold;">#<?= $u['UserID'] ?></td>
                            <td><strong style="color: var(--text-dark);"><?= htmlspecialchars($u['FullName']) ?></strong></td>
                            <td><span class="role-badge role-<?= $u['RoleName'] ?>"><?= $u['RoleName'] ?></span></td>
                            <td class="gps-data">
                                <?php if (!empty($u['Latitude'])): ?>
                                    <span class="gps-coords">
                                        <i class="fas fa-satellite-dish" style="color: var(--sidebar); margin-right: 5px;"></i>
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

    <script>
        function searchTable() {
            const filter = document.getElementById("searchInput").value.toLowerCase();
            const rows = document.querySelectorAll("#locationTable tbody tr");
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
            });
        }
    </script>
</body>
</html>