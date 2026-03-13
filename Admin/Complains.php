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
            C.ComplaintID,
            U.FullName AS ComplainantName,
            C.Subject,
            C.Description,
            C.Status,
            C.CreatedAt
        FROM Complaints C
        JOIN Users U ON C.ComplainantID = U.UserID
        ORDER BY C.CreatedAt DESC";
        
    $stmt = $pdo->query($query);
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | Complaints Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">

  
    <script src="assets/app.js" defer></script>
</head>

<body class="app">

  <div class="sidebar">
    <h2>TRUSTCARE</h2>
    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> Caregivers</a>
    <a class="nav-btn" href="Elders.php"><i class="fas fa-blind"></i> Elders</a>
    <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> Doctors</a>
    <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> Caregiver Links</a>
    <a class="nav-btn" href="HealthAI.php"><i class="fas fa-robot"></i> Health & AI</a>
    <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> SOS & Emergency</a>
    <a class="nav-btn active" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
    <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <h1>Complaints & Feedback</h1>
    <br>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Complainant</th>
            <th>Subject</th>
            <th>Date Reported</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($complaints)): ?>
            <tr><td colspan="6" style="text-align:center; padding: 30px;">No complaints found.</td></tr>
          <?php else: ?>
            <?php foreach ($complaints as $row): ?>
            <tr>
              <td><strong>#<?= $row['ComplaintID'] ?></strong></td>
              <td><?= htmlspecialchars($row['ComplainantName']) ?></td>
              <td><?= htmlspecialchars($row['Subject']) ?></td>
              <td style="color: var(--text-muted);"><?= date('M d, Y | h:i A', strtotime($row['CreatedAt'])) ?></td>
              <td>
                <?php 
                  $statusClass = 'status-pending';
                  $statusText = $row['Status'];
                  if(strtolower($statusText) == 'resolved') $statusClass = 'status-resolved';
                  if(strtolower($statusText) == 'under review') $statusClass = 'status-review';
                ?>
                <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
              </td>
              <td>
                  <a href="ComplainRespond.php?id=<?= $row['ComplaintID'] ?>" class="action-btn">Respond</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
