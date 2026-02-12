<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    :root {
      --sidebar: #1F6F78;
      --bg: #F6F7F3;
      --card: #FFFFFF;
      --text-main: #1E2A2A;
      --text-muted: #6F7F7D;
      --checkins: #D6EFE6;
      --reminder: #E6B450;
      --sos: #C62828;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
      height: 100vh;
      background: var(--bg);
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      width: 240px;
      background: var(--sidebar);
      color: #fff;
      display: flex;
      flex-direction: column;
    }

    .sidebar h2 {
      padding: 20px;
      text-align: center;
      font-size: 20px;
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .nav-btn {
      padding: 14px 20px;
      cursor: pointer;
      border: none;
      background: none;
      color: #fff;
      text-align: left;
      font-size: 14px;
    }

    .nav-btn:hover,
    .nav-btn.active {
      background: rgba(255,255,255,0.15);
    }

    .logout {
      margin-top: auto;
      background: var(--sos);
      text-align: center;
    }

    /* ===== MAIN CONTENT ===== */
    .content {
      flex: 1;
      padding: 25px;
      overflow-y: auto;
    }

    .page {
      display: none;
    }

    .page.active {
      display: block;
    }

    /* ===== DASHBOARD CARDS ===== */
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .card {
      background: var(--card);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .card h3 {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 10px;
    }

    .card p {
      font-size: 26px;
      font-weight: bold;
      color: var(--text-main);
    }

    .sos-card p {
      color: var(--sos);
    }

    .risk-card p {
      color: var(--reminder);
    }

    /* ===== GRAPH PLACEHOLDER ===== */
    .graph {
      height: 260px;
      background: var(--card);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-muted);
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    h1 {
      color: var(--text-main);
      margin-bottom: 20px;
    }
  </style>
</head>

<body>

<div class="sidebar">
  <h2>Admin Panel</h2>

  <a class="nav-btn active" href="Dashboard.php">Dashboard</a>
  <a class="nav-btn" href="Caregivers.php">Caregivers</a>
  <a class="nav-btn" href="Elders.php">Elders</a>
  <a class="nav-btn" href="Doctors.php">Doctors</a>
  <a class="nav-btn" href="CaregiverLinks.php">Caregiver Links</a>
  <a class="nav-btn" href="HealthAI.php">Health & AI</a>
  <a class="nav-btn" href="Reminders.php">Reminders</a>
  <a class="nav-btn" href="SOS.php">SOS & Emergency</a>
  <a class="nav-btn" href="Complains.php">Complains</a>
  <a class="nav-btn" href="Location.php">Location</a>

  <a class="nav-btn logout" href="Login.php">Logout</a>
</div>



  <!-- ===== CONTENT ===== -->
  <div class="content">

    <!-- DASHBOARD -->
    <div id="dashboard" class="page active">
      <h1>Dashboard</h1>

      <div class="card-grid">
        <div class="card">
          <h3>Total Caregivers</h3>
          <p>--</p>
        </div>
        <div class="card">
          <h3>Total Elders</h3>
          <p>--</p>
        </div>
        <div class="card">
          <h3>Active Today</h3>
          <p>--</p>
        </div>
        <div class="card sos-card">
          <h3>SOS Today</h3>
          <p>--</p>
        </div>
        <div class="card">
          <h3>Missed Medications</h3>
          <p>--</p>
        </div>
        <div class="card risk-card">
          <h3>High Risk Elders</h3>
          <p>--</p>
        </div>
      </div>

      <div class="graph">
        SOS & Reports Activity Graph
      </div>
    </div>

    <!-- OTHER PAGES -->
    <div id="caregivers" class="page"><h1>Caregivers</h1></div>
    <div id="elders" class="page"><h1>Elders</h1></div>
    <div id="links" class="page"><h1>Caregiver Links</h1></div>
    <div id="health" class="page"><h1>Health & AI</h1></div>
    <div id="reminders" class="page"><h1>Reminders</h1></div>
    <div id="sos" class="page"><h1>SOS & Emergency</h1></div>
    <div id="messages" class="page"><h1>Messages</h1></div>
    <div id="location" class="page"><h1>Location</h1></div>

  </div>

  <!-- ===== JS NAVIGATION ===== -->
  <script>
    function showPage(pageId) {
      document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));

      document.getElementById(pageId).classList.add('active');
      event.target.classList.add('active');
    }
  </script>

</body>
</html>
