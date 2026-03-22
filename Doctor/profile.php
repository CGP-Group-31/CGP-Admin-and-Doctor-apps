<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['doctor_logged_in'])) {
    header("Location: index.php");
    exit;
}

$doctorId = $_SESSION['doctor_id'];
$sql = "SELECT 
            u.FullName, u.Email, u.Phone, u.DateOfBirth,
            u.Gender, u.address,
            d.LicenseNumber, d.Specialization, d.Hospital
        FROM Users u
        INNER JOIN Doctor d ON u.UserID = d.DoctorID
        WHERE u.UserID = :doctor_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['doctor_id' => $doctorId]);
$doctor = $stmt->fetch();
?>
<?php include 'include/header.php'; ?>
<style>
    .profile-card {
        background: #fff;
        border-radius: 18px;
        padding: 25px;
        border: 1px solid #E5ECE9;
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .profile-header h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 24px;
        margin: 0;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .box {
        background: #FAFBFB;
        border: 1px solid #EEF2F1;
        border-radius: 12px;
        padding: 15px;
    }

    .box span {
        font-size: 13px;
        color: #7C8B89;
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .box strong {
        font-size: 16px;
        color: #243333;
    }

    @media (max-width: 900px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-layout">
    <?php include 'include/sidebar.php'; ?>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1>Doctor Profile</h1>
                <p>View your account details at a glance.</p>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-header">
                <h1>Profile Information</h1>
            </div>

            <div class="grid">
                <div class="box">
                    <span>Full Name</span>
                    <strong><?php echo htmlspecialchars($doctor['FullName']); ?></strong>
                </div>

                <div class="box">
                    <span>Email</span>
                    <strong><?php echo htmlspecialchars($doctor['Email']); ?></strong>
                </div>

                <div class="box">
                    <span>Phone</span>
                    <strong><?php echo htmlspecialchars($doctor['Phone']); ?></strong>
                </div>

                <div class="box">
                    <span>Gender</span>
                    <strong><?php echo htmlspecialchars($doctor['Gender']); ?></strong>
                </div>

                <div class="box">
                    <span>Date of Birth</span>
                    <strong><?php echo htmlspecialchars($doctor['DateOfBirth']); ?></strong>
                </div>

                <div class="box">
                    <span>Address</span>
                    <strong><?php echo htmlspecialchars($doctor['address']); ?></strong>
                </div>

                <div class="box">
                    <span>Specialization</span>
                    <strong><?php echo htmlspecialchars($doctor['Specialization']); ?></strong>
                </div>

                <div class="box">
                    <span>Hospital</span>
                    <strong><?php echo htmlspecialchars($doctor['Hospital']); ?></strong>
                </div>

                <div class="box">
                    <span>License Number</span>
                    <strong><?php echo htmlspecialchars($doctor['LicenseNumber']); ?></strong>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
