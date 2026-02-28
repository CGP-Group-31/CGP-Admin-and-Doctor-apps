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
        
        body { 
            background: var(--bg); 
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        .sidebar { 
            width: var(--sidebar-width); 
            background: var(--sidebar-color); 
            color: #fff; 
            height: 100vh; 
            position: fixed; 
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar h2 { padding: 25px 20px; text-align: center; font-size: 1.5rem; background: rgba(0,0,0,0.1); border-bottom: 1px solid rgba(255,255,255,0.1); }
        
        .nav-btn { 
            padding: 12px 20px; 
            text-decoration: none; 
            color: rgba(255,255,255,0.8); 
            font-size: 14px; 
            display: flex; 
            align-items: center; 
            transition: 0.3s; 
            border-left: 4px solid transparent;
        }

        .nav-btn i { margin-right: 12px; width: 20px; text-align: center; font-size: 16px; }
        .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.1); color: #fff; border-left: 4px solid var(--accent); }
        
        .logout { margin-top: auto; background: var(--sos); justify-content: center; font-weight: bold; padding: 15px; }

        .content { 
            flex: 1; 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            min-height: 100vh;
        }

        .header-section { margin-bottom: 30px; }
        .header-section h1 { font-size: 2rem; color: var(--sidebar-color); }
        
   
        .search-box { position: relative; margin-bottom: 25px; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .search-box input { 
            padding: 12px 15px 12px 45px; 
            border-radius: 30px; 
            border: 1px solid #ddd; 
            width: 400px; 
            outline: none; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        }
        
    
        .card { background: var(--card); border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        
        thead th { 
            background: #f1f5f9; 
            padding: 18px 15px; 
            text-align: left; 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            color: var(--text-muted);
            position: sticky;
            top: 0;
        }

        tbody td { padding: 18px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        tbody tr:hover { background-color: #fcfdfe; }
        

        .badge { padding: 5px 12px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge-stable { background: #dcfce7; color: var(--success); }
        .badge-high { background: #fee2e2; color: var(--sos); }

        .btn-view { 
            padding: 8px 15px; 
            background: var(--sidebar-color); 
            color: white; 
            border-radius: 8px; 
            text-decoration: none; 
            font-size: 12px; 
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-view:hover { background: #165057; }

        @media (max-width: 1024px) {
            .sidebar { width: 70px; }
            .sidebar h2, .nav-btn span { display: none; }
            .content { margin-left: 70px; }
            .search-box input { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ELDERCARE</h2>
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
        <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>

    <div class="content">
        <div class="header-section">
            <h1>Elder Directory</h1>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search by name, ID or caregiver...">
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
                                    <i class="fas fa-user-nurse" style="color: #aaa; margin-right: 5px;"></i>
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

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#elderTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>