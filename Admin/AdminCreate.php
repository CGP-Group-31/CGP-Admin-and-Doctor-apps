<?php
session_start();
require 'include/db.php';

// Access Control
if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

$error_msg = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Check if Email exists
        $check = $pdo->prepare("SELECT UserID FROM Users WHERE Email = ?");
        $check->execute([$_POST['email']]);
        if ($check->fetch()) {
            throw new Exception("This email is already registered to another administrator.");
        }

        // 2. Insert into Users table as Admin (RoleID = 1)
        // Using 'PasswordHash' as per your database schema
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
    <title>Create Admin | Eldercare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { 
            --primary: #2E7D7A; 
            --bg: #F4F5F0; 
            --wellbeing: #BEE8DA; 
            --text-dark: #243333; 
            --text-muted: #6F7F7D;
            --sos: #C62828;
        }

        body { background: var(--bg); color: var(--text-dark); font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        
        .form-card { background: #FFFFFF; width: 100%; max-width: 500px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; }
        
        .form-header { background: var(--primary); color: white; padding: 30px; text-align: center; }
        .form-header i { font-size: 40px; margin-bottom: 10px; }
        
        .form-body { padding: 35px; }
        
        .msg { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center; font-weight: 600; }
        .error { background: #FEE2E2; color: var(--sos); border: 1px solid #FCA5A5; }
        .success { background: var(--wellbeing); color: var(--primary); border: 1px solid var(--primary); }

        .input-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
        
        input { 
            width: 100%; padding: 12px; border: 1px solid #CFEDE2; border-radius: 8px; outline: none; 
            background: #FAFAFA; color: var(--text-dark); box-sizing: border-box; transition: 0.3s; 
        }
        
        input:focus { border-color: var(--primary); background: #FFF; box-shadow: 0 0 0 3px rgba(46, 125, 122, 0.1); }

        .btn-group { margin-top: 30px; display: flex; gap: 12px; }
        
        .btn { flex: 1; padding: 14px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; transition: 0.3s; font-size: 14px; }
        
        .btn-save { background: var(--primary); color: white; }
        .btn-cancel { background: #EEE; color: var(--text-muted); }
        
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
    </style>
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-user-shield"></i>
        <h2>Add Administrator</h2>
        <p style="font-size: 13px; opacity: 0.8; margin: 5px 0 0 0;">Grant system access to new staff</p>
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

</body>
</html>