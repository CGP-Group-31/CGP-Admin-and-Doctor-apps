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

        
        $checkEmail = $pdo->prepare("SELECT UserID FROM Users WHERE Email = ?");
        $checkEmail->execute([$_POST['email']]);
        if ($checkEmail->fetch()) {
            throw new Exception("This email is already registered.");
        }

        
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
    <div class="error">
        <i class="fas fa-exclamation-circle"></i> Error: <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Doctor | Admin</title>
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
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-user-md fa-3x"></i>
            <h2 class="form-title">Register New Doctor</h2>
            <p class="text-soft">Create a medical profile and system credentials</p>
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
  </div>

</body>
</html>
