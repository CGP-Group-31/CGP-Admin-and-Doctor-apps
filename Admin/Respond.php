<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">
<meta charset="UTF-8">
<title>Respond to Complaint</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">


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
  <h1>Respond to Complaint</h1>

  <div class="card">


    <div class="detail-row">
      <strong>Elder Name:</strong>
      <span>Kamal Silva</span>
    </div>

    <div class="detail-row">
      <strong>Time:</strong>
      <span>09:00 AM</span>
    </div>

    <div class="detail-row">
      <strong>Message:</strong>
      <span>Need help with medication</span>
    </div>

    <form>
      <div class="detail-row">
        <strong>Your Response:</strong>
        <textarea placeholder="Type your response here..."></textarea>
      </div>

      <button type="submit" class="btn">Send Response</button>
    </form>

  </div>
</div>

</body>
</html>
