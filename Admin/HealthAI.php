<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | Health & AI Insights</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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
      --ai-purple: #6c5ce7;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

    body { display: flex; min-height: 100vh; background: var(--bg); }

    .sidebar { width: 240px; background: var(--sidebar); color: #fff; display: flex; flex-direction: column; position: fixed; height: 100vh; }
    .sidebar h2 { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); font-size: 1.2rem; letter-spacing: 1px; }
    .nav-btn { padding: 14px 20px; text-decoration: none; color: #fff; font-size: 14px; display: flex; align-items: center; transition: 0.3s; }
    .nav-btn i { margin-right: 10px; width: 20px; text-align: center; }
    .nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.15); border-left: 4px solid var(--reminder); }
    .logout { margin-top: auto; background: var(--sos); text-align: center; font-weight: bold; }

    .content { flex: 1; margin-left: 240px; padding: 40px; }
    .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    h1 { color: var(--text-main); font-size: 24px; }
    
    .ai-badge { background: var(--ai-purple); color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }

    .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .card { background: var(--card); padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: center; border-top: 4px solid var(--sidebar); }
    .card i { font-size: 2rem; color: var(--sidebar); margin-bottom: 10px; }
    .card h2 { font-size: 14px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px; }
    .card p { font-size: 24px; font-weight: bold; color: var(--text-main); }


    .table-card { background: var(--card); padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .table-header { margin-bottom: 15px; font-weight: bold; color: var(--sidebar); display: flex; align-items: center; }
    .table-header i { margin-right: 8px; }

    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
    th { background: #f8fafb; color: var(--text-muted); font-size: 12px; text-transform: uppercase; }

  
    .sev { padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; }
    .sev-high { background: #ffebee; color: var(--sos); }
    .sev-med { background: #fff3e0; color: #ef6c00; }
    .sev-low { background: #e8f5e9; color: #2e7d32; }

    .btn-view { background: var(--sidebar); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 12px; transition: 0.3s; text-decoration: none; }
    .btn-view:hover { opacity: 0.8; }

  </style>
</head>

<body>

  <div class="sidebar">
    <h2>ELDERCARE</h2>

    <a class="nav-btn" href="Dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
    <a class="nav-btn" href="Caregivers.php"><i class="fas fa-user-nurse"></i> Caregivers</a>
    <a class="nav-btn" href="Elders.php"><i class="fas fa-user-friends"></i> Elders</a>
    <a class="nav-btn" href="Doctors.php"><i class="fas fa-user-md"></i> Doctors</a>
    <a class="nav-btn" href="CaregiverLinks.php"><i class="fas fa-link"></i> Caregiver Links</a>
    <a class="nav-btn active" href="HealthAI.php"><i class="fas fa-robot"></i> Health & AI</a>
    <a class="nav-btn" href="SOS.php"><i class="fas fa-heartbeat"></i> SOS & Emergency</a>
    <a class="nav-btn" href="Complains.php"><i class="fas fa-comment-dots"></i> Complains</a>
    <a class="nav-btn" href="Location.php"><i class="fas fa-map-marker-alt"></i> Location</a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="Login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
      <div class="card" style="border-top-color: var(--sos);">
        <i class="fas fa-exclamation-triangle" style="color: var(--sos);"></i>
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