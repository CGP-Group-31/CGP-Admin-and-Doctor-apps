<?php
session_start();
require 'include/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_response'])) {
    try {
        $newStatus = $_POST['status'];
        
        $stmt = $pdo->prepare("UPDATE Complaints SET Status = ? WHERE ComplaintID = ?");
        $stmt->execute([$newStatus, $id]);
        
        echo "<script>alert('Complaint status updated to " . $newStatus . "'); window.location.href='Complains.php';</script>";
        exit;
    } catch (PDOException $e) {
        $error = "Error updating record: " . $e->getMessage();
    }
}
try {
    $query = "
        SELECT 
            C.*, 
            U.FullName AS ComplainantName, 
            U.Phone AS ComplainantPhone,
            U.Email AS ComplainantEmail
        FROM Complaints C
        JOIN Users U ON C.ComplainantID = U.UserID
        WHERE C.ComplaintID = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $complain = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$complain) {
        die("<div class='card' style='max-width:520px; margin:40px auto; text-align:center;'><h2>Complaint Not Found</h2><a href='Complains.php' class='text-primary'>Return to List</a></div>");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Complaint #<?= $id ?></title>
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
    <a class="nav-btn active" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
  </div>

  <div class="content">
    <div class="respond-card">
    <div class="top-bar">
        <h2>Process Complaint</h2>
        <div class="case-id">TICKET #<?= $id ?></div>
    </div>

    <div class="content-body">
        <div class="section-header"><i class="fas fa-user"></i> Complainant Information</div>
        <div class="meta-info">
            <div class="meta-item">
                <label>Name</label>
                <span><?= htmlspecialchars($complain['ComplainantName']) ?></span>
            </div>
            <div class="meta-item">
                <label>Date Filed</label>
                <span><?= date('M d, Y', strtotime($complain['CreatedAt'])) ?></span>
            </div>
            <div class="meta-item">
                <label>Phone</label>
                <span><?= htmlspecialchars($complain['ComplainantPhone']) ?></span>
            </div>
            <div class="meta-item">
                <label>Email</label>
                <span><?= htmlspecialchars($complain['ComplainantEmail'] ?? 'N/A') ?></span>
            </div>
        </div>

        <div class="section-header"><i class="fas fa-envelope-open-text"></i> Complaint Details</div>
        <div class="original-message">
            <div class="subject-line"><?= htmlspecialchars($complain['Subject']) ?></div>
            <div class="message-body"><?= nl2br(htmlspecialchars($complain['Description'])) ?></div>
        </div>

        <form method="POST">
            <div class="section-header"><i class="fas fa-clipboard-check"></i> Resolution Action</div>
            
            <label>Set Current Status</label>
            <select name="status">
                <option value="Pending" <?= $complain['Status'] == 'Pending' ? 'selected' : '' ?>>Pending (New)</option>
                <option value="Under Review" <?= $complain['Status'] == 'Under Review' ? 'selected' : '' ?>>Under Review (In Progress)</option>
                <option value="Resolved" <?= $complain['Status'] == 'Resolved' ? 'selected' : '' ?>>Resolved (Completed)</option>
            </select>

            <label>Admin Internal Notes</label>
            <textarea placeholder="Describe the outcome or steps taken to address this complaint..."></textarea>

            <div class="actions">
                <a href="Complains.php" class="btn btn-cancel">Discard Changes</a>
                <button type="submit" name="submit_response" class="btn btn-submit">Update & Save Ticket</button>
            </div>
        </form>
    </div>
    </div>
  </div>

</body>
</html>
