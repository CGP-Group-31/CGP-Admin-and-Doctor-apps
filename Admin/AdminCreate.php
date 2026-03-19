<?php
session_start();
require 'include/db.php';


if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

$error_msg = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        
        $check = $pdo->prepare("SELECT UserID FROM Users WHERE Email = ?");
        $check->execute([$_POST['email']]);
        if ($check->fetch()) {
            throw new Exception("This email is already registered to another administrator.");
        }

        
        
        $sql = "INSERT INTO Users (FullName, Email, Phone, PasswordHash, RoleID, IsActive, CreatedAt) 
                VALUES (?, ?, ?, ?, 1, 1, GETDATE())";
        
        $stmt = $pdo->prepare($sql);
        $hashedPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $stmt->execute([
            $_POST['full_name'],
            $_POST['email'],
            $_POST['phone'] ?? 'N/A',
            $hashedPass
        ]);

        $pdo->commit();
        $success_msg = "New administrator account created successfully!";
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error_msg = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin | TrustCare</title>
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
    <a class="nav-btn active" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
  </div>

  <div class="content">
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-user-shield"></i>
            <h2>Add Administrator</h2>
            <p class="text-soft" style="font-size: 13px; margin: 5px 0 0 0;">Grant system access to new staff</p>
        </div>

    <form class="form-body" method="POST">
        <?php if($error_msg): ?>
            <div class="msg error"><?= $error_msg ?></div>
        <?php endif; ?>

        <?php if($success_msg): ?>
            <div class="msg success"><?= $success_msg ?></div>
            <script>setTimeout(() => { window.location.href='Admins.php'; }, 2000);</script>
        <?php endif; ?>

        <div class="input-group">
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="Enter full name" required>
        </div>

        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="admin@eldercare.com" required>
        </div>

        <div class="input-group">
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="e.g. +1 555 0123">
        </div>

        <div class="input-group">
            <label>Temporary Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <div class="btn-group">
            <a href="Admins.php" class="btn btn-cancel">Go Back</a>
            <button type="submit" class="btn btn-save">Create Admin</button>
        </div>
    </form>
    </div>
  </div>

</body>
</html>
