<?php
session_start();
include 'include/db.php';

if (!isset($_SESSION['doctor_logged_in'])) {
    header("Location: index.php");
    exit;
}
$doctorId = $_SESSION['doctor_id'];

$sqlPatients = "SELECT COUNT(*) FROM ElderProfiles WHERE PreferredDoctorID = :doctor_id";
$stmt = $pdo->prepare($sqlPatients);
$stmt->execute(['doctor_id' => $doctorId]);
$totalPatients = $stmt->fetchColumn();


$sqlReports = "SELECT COUNT(*) FROM VitalRecords vr
JOIN ElderProfiles ep ON vr.ElderID = ep.ElderID
WHERE ep.PreferredDoctorID = :doctor_id";

$stmt2 = $pdo->prepare($sqlReports);
$stmt2->execute(['doctor_id' => $doctorId]);
$totalReports = $stmt2->fetchColumn();

include 'include/header.php';
?>
<div class="dashboard-layout">
    <?php include 'include/sidebar.php'; ?>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1>TrustCare Doctor Dashboard</h1>
                <p>Welcome to the Trustcare doctor dashboard.</p>
            </div>

            <div class="doctor-mini">
                <strong><?php echo htmlspecialchars($_SESSION['doctor_name']); ?></strong>
                <span><?php echo htmlspecialchars($_SESSION['doctor_specialization']); ?></span>
            </div>
        </div>

        <div class="card-grid">

            <div class="info-card">
                <h3>Total Patients</h3>
                <p class="big-number"><?php echo $totalPatients; ?></p>
                <span>Patients under your care</span>
            </div>

            <div class="info-card">
                <h3>Reports</h3>
                <p class="big-number"><?php echo $totalReports; ?></p>
                <span>Total vitals recorded</span>
            </div>


        </div>
        <div class="dashboard-card">
            <h3>Recent Patients</h3>

            <?php
            $recentPatients = $pdo->prepare("
        SELECT TOP 5 u.FullName, u.Phone, u.CreatedAt
        FROM Users u
        JOIN ElderProfiles ep ON u.UserID = ep.ElderID
        WHERE ep.PreferredDoctorID = :doctor_id
        ORDER BY u.CreatedAt DESC
    ");

            $recentPatients->execute(['doctor_id' => $doctorId]);
            $patientsList = $recentPatients->fetchAll();
            ?>

            <?php if (!empty($patientsList)): ?>
                <?php foreach ($patientsList as $p): ?>
                    <div class="list-item">
                        <div>
                            <strong><?php echo htmlspecialchars($p['FullName']); ?></strong>
                            <span class="sub-text"><?php echo htmlspecialchars($p['Phone']); ?></span>
                        </div>
                        <div class="date">
                            <?php echo date('M d', strtotime($p['CreatedAt'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-text">No patients found</p>
            <?php endif; ?>

        </div>
    </main>
</div>
</body>

</html>