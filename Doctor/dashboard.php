<?php
session_start();
include 'include/db.php';
if (!isset($_SESSION['doctor_logged_in'])) {
    header("Location: index.php");
    exit;
}

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
                <p>.</p>
            </div>


            <div class="info-card">
                <h3>Reports</h3>
                <p>.</p>
            </div>
        </div>

        
    </main>
</div>

</body>
</html>