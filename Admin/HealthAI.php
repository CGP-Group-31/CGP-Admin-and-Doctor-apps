<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | Health & AI Insights</title>
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
    <a class="nav-btn active" href="HealthAI.php"><i class="fas fa-robot"></i> Health & AI</a>
    <a class="nav-btn" href="SOS.php"><i class="fas fa-ambulance"></i> SOS & Emergency</a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> Complains</a>
    <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <div class="header-flex">
        <h1>Health & AI Insights</h1>
        <span class="ai-badge"><i class="fas fa-microchip"></i> AI Engine Active</span>
    </div>

    <div class="cards">
      <div class="card">
        <i class="fas fa-chart-line"></i>
        <h2>Avg Health Score</h2>
        <p>78%</p>
      </div>
      <div class="card">
        <i class="fas fa-lightbulb"></i>
        <h2>AI Suggestions</h2>
        <p>12 Today</p>
      </div>
      <div class="card danger-border">
        <i class="fas fa-exclamation-triangle text-danger"></i>
        <h2>Critical Alerts</h2>
        <p>2 Active</p>
      </div>
    </div>

    <div class="table-card">
      <div class="table-header">
          <i class="fas fa-robot"></i> Recent Predictive Alerts
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Elder Name</th>
            <th>Observation</th>
            <th>AI Prediction</th>
            <th>Severity</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>#882</strong></td>
            <td>Sunil Perera</td>
            <td>Abnormal Sleep Pattern</td>
            <td>Potential Fatigue Risk</td>
            <td><span class="sev sev-high">HIGH</span></td>
            <td><a href="#" class="btn-view">Analyze</a></td>
          </tr>
          <tr>
            <td><strong>#879</strong></td>
            <td>Mary Fernando</td>
            <td>Step Count Decreased 40%</td>
            <td>Mobility Warning</td>
            <td><span class="sev sev-med">MEDIUM</span></td>
            <td><a href="#" class="btn-view">Analyze</a></td>
          </tr>
          <tr>
            <td><strong>#875</strong></td>
            <td>Robert Silva</td>
            <td>Consistent Vitals</td>
            <td>Stable Health</td>
            <td><span class="sev sev-low">LOW</span></td>
            <td><a href="#" class="btn-view">Analyze</a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
