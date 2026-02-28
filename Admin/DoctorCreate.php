<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

$error_msg = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Verify Email uniqueness
        $checkEmail = $pdo->prepare("SELECT UserID FROM Users WHERE Email = ?");
        $checkEmail->execute([$_POST['email']]);
        if ($checkEmail->fetch()) {
            throw new Exception("This email is already registered.");
        }

        /** * 2. Insert into Users Table 
         * UPDATED: Changed 'Password' to 'PasswordHash' 
         * UPDATED: Added Gender and DateOfBirth to match your screenshot
         */
        $queryUser = "INSERT INTO Users (FullName, Phone, Email, PasswordHash, RoleID, Gender, DateOfBirth, IsActive, CreatedAt) 
                      VALUES (?, ?, ?, ?, 2, ?, ?, 1, GETDATE())";
        
        $stmtUser = $pdo->prepare($queryUser);
        $hashedPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $stmtUser->execute([
            $_POST['full_name'], 
            $_POST['phone'] ?? 'N/A', 
            $_POST['email'], 
            $hashedPass,
            $_POST['gender'] ?? 'Male',
            $_POST['dob'] ?? '1980-01-01'
        ]);
        
        $newUserID = $pdo->lastInsertId();

        // 3. Insert into Doctor Table (Confirmed columns: DoctorID, LicenseNumber, Specialization, Hospital)
        $stmtDoc = $pdo->prepare("INSERT INTO Doctor (DoctorID, LicenseNumber, Specialization, Hospital) VALUES (?, ?, ?, ?)");
        $stmtDoc->execute([
            $newUserID, 
            $_POST['license'], 
            $_POST['specialty'], 
            $_POST['hospital']
        ]);

        $pdo->commit();
        echo "<script>alert('Doctor created successfully!'); window.location.href='Doctors.php';</script>";
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error_msg = $e->getMessage();
    }
}
?>

<?php if($error_msg): ?>
    <div style="background: #C62828; color: white; padding: 15px; margin: 20px; border-radius: 8px; font-weight: bold; text-align: center;">
        <i class="fas fa-exclamation-circle"></i> Error: <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Doctor | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #1F6F78; --bg: #F6F7F3; --accent: #E6B450; }
        body { background: var(--bg); font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; padding: 40px; }
        
        .form-card { background: white; width: 100%; max-width: 700px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .form-header { background: var(--sidebar); color: white; padding: 30px; text-align: center; }
        .form-body { padding: 40px; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .section-title { grid-column: span 2; margin-top: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0; color: var(--sidebar); font-weight: bold; text-transform: uppercase; font-size: 13px; }
        
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: #555; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; transition: 0.3s; }
        input:focus { border-color: var(--sidebar); box-shadow: 0 0 0 3px rgba(31, 111, 120, 0.1); }

        .btn-group { margin-top: 40px; display: flex; gap: 15px; }
        .btn { flex: 1; padding: 15px; border-radius: 10px; border: none; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; transition: 0.3s; }
        .btn-save { background: var(--sidebar); color: white; }
        .btn-cancel { background: #eee; color: #333; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-user-md fa-3x"></i>
        <h2 style="margin-top:10px;">Register New Doctor</h2>
        <p style="opacity:0.8;">Create a medical profile and system credentials</p>
    </div>

    <form class="form-body" method="POST">
        <div class="grid">
            <div class="section-title">Personal & Account Info</div>
            
            <div style="grid-column: span 2;">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Dr. John Smith" required>
            </div>

            <div>
                <label>Email Address</label>
                <input type="email" name="email" placeholder="john@hospital.com" required>
            </div>
            
            <div>
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="+1 234 567 890" required>
            </div>

            <div style="grid-column: span 2;">
                <label>System Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="section-title">Professional Credentials</div>

            <div>
                <label>License Number</label>
                <input type="text" name="license" placeholder="MED-99203" required>
            </div>

            <div>
                <label>Specialization</label>
                <input type="text" name="specialty" placeholder="Geriatrician / Cardiologist" required>
            </div>

            <div style="grid-column: span 2;">
                <label>Primary Hospital / Clinic</label>
                <input type="text" name="hospital" placeholder="City General Medical Center" required>
            </div>
        </div>

        <div class="btn-group">
            <a href="Doctors.php" class="btn btn-cancel">Go Back</a>
            <button type="submit" class="btn btn-save">Create Doctor Account</button>
        </div>
    </form>
</div>

</body>
</html>