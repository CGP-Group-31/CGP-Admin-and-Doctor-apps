<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Caregiver Links</title>
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
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .nav-btn {
      padding: 14px 20px;
      text-decoration: none;
      color: #fff;
      font-size: 14px;
      display: block;
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

    /* ===== CONTENT ===== */
    .content {
      flex: 1;
      padding: 25px;
      overflow-y: auto;
    }

    h1 {
      color: var(--text-main);
      margin-bottom: 20px;
    }

    /* ===== TABLE ===== */
    .card {
      background: var(--card);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
      font-size: 14px;
      color: var(--text-main);
    }

    th {
      background: var(--checkins);
    }

    .actions button {
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
    }

    .view {
      background: var(--checkins);
    }

    .edit {
      background: var(--reminder);
    }

    .delete {
      background: var(--sos);
      color: #fff;
    }

    /* ===== TOP ACTION ===== */
    .top-bar {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .add-btn {
      padding: 10px 14px;
      background: var(--sidebar);
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
  </style>
</head>

<body>

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h2>Admin Panel</h2>

    <a class="nav-btn" href="Dashboard.php">Dashboard</a>
    <a class="nav-btn" href="Caregivers.php">Caregivers</a>
    <a class="nav-btn" href="Elders.php">Elders</a>
    <a class="nav-btn" href="Doctors.php">Doctors</a>
    <a class="nav-btn active" href="CaregiverLinks.php">Caregiver Links</a>
    <a class="nav-btn" href="HealthAI.php">Health & AI</a>
    <a class="nav-btn" href="Reminders.php">Reminders</a>
    <a class="nav-btn" href="SOS.php">SOS & Emergency</a>
    <a class="nav-btn" href="Complains.php">Complains</a>
    <a class="nav-btn" href="Location.php">Location</a>

    <a class="nav-btn logout" href="Login.php">Logout</a>
  </div>

  <!-- CONTENT -->
  <div class="content">
    <h1>Caregiver Links</h1>

<br>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Link Name</th>
            <th>Type</th>
            <th>Assigned Caregiver</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <!-- Dummy rows for now -->
          <tr>
            <td>1</td>
            <td>Medication Guide</td>
            <td>PDF</td>
            <td>John Silva</td>
            <td>Active</td>
            <td class="actions">
              <button class="view">View</button>
            </td>
          </tr>

          <tr>
            <td>2</td>
            <td>Exercise Videos</td>
            <td>Video</td>
            <td>Maria Fernando</td>
            <td>Inactive</td>
            <td class="actions">
              <button class="view">View</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
