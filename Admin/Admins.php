<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

try {
    // Fetching only Admins (RoleID 1)
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
    <style>
        :root {
            --primary: #2E7D7A;      /* Navigation / Main Buttons */
            --bg: #F4F5F0;           /* Main Background */
            --wellbeing: #D6EFE6;    /* Light Green Accents */
            --text-dark: #243333;    /* Main Text */
            --text-muted: #6F7F7D;   /* Secondary Text */
            --card: #FFFFFF;
            --sos: #C62828;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        
        body { 
            background: var(--bg); 
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar { 
            width: 260px; background: var(--primary); color: #fff; 
            height: 100vh; position: fixed; display: flex; flex-direction: column; 
        }
        .sidebar h2 { padding: 25px 20px; text-align: center; font-size: 1.5rem; background: rgba(0,0,0,0.1); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-btn { padding: 12px 20px; text-decoration: none; color: rgba(255,255,255,0.8); font-size: 14px; display: flex; align-items: center; transition: 0.3s; border-left: 4px solid transparent; }
        .nav-btn i { margin-right: 12px; width: 20px; text-align: center; }
        .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.1); color: #fff; border-left: 4px solid var(--wellbeing); }
        .logout { margin-top: auto; background: var(--sos); justify-content: center; font-weight: bold; padding: 15px; }

        /* Content Area */
        .content { flex: 1; margin-left: 260px; padding: 40px; }

        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-section h1 { font-size: 2rem; color: var(--primary); font-weight: 800; }
        
        .btn-add { 
            padding: 12px 24px; background: var(--primary); color: white; 
            border-radius: 8px; text-decoration: none; font-weight: 700; 
            display: flex; align-items: center; gap: 8px; transition: 0.3s;
        }
        .btn-add:hover { background: #246361; transform: translateY(-2px); }

        /* Search Bar */
        .search-box { position: relative; margin-bottom: 25px; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .search-box input { 
            padding: 12px 15px 12px 45px; border-radius: 8px; border: 1px solid #CFEDE2; 
            width: 100%; max-width: 450px; outline: none; box-shadow: 0 2px 10px rgba(0,0,0,0.03); 
        }

        /* Table Card */
        .card { background: var(--card); border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        
        thead th { 
            background: #F8FAF9; padding: 18px 15px; text-align: left; 
            font-size: 11px; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); border-bottom: 2px solid var(--bg);
        }

        tbody td { padding: 18px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: var(--text-dark); }
        tbody tr:hover { background-color: #fcfdfe; }
        
        .admin-badge {
            background: var(--wellbeing); color: var(--primary);
            padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ELDERCARE</h2>
        <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
        <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> <span>Caregivers</span></a>
        <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> <span>Elders</span></a>
        <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> <span>Doctors</span></a>
        <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> <span>Caregiver Links</span></a>
        <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> <span>Health & AI</span></a>
        <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> <span>SOS & Emergency</span></a>
        <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
        <a class="nav-btn active" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
        <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> <span>Location</span></a>
        <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
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
            <input type="text" id="searchInput" placeholder="Search admins by name or email...">
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
                            <td><strong style="color: var(--text-dark);"><?= htmlspecialchars($a['FullName']) ?></strong></td>
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

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#adminTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>