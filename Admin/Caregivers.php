<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Caregivers</title>
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
      min-height: 100vh;
      background: var(--bg);
      flex-direction: row;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      width: 240px;
      background: var(--sidebar);
      color: #fff;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease;
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
      padding: 20px;
      overflow-x: auto;
    }

    h1 {
      color: var(--text-main);
      margin-bottom: 15px;
    }

    /* ===== TOP BAR ===== */
    .top-bar {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .top-bar p {
      color: var(--text-muted);
    }

    .add-btn {
      padding: 10px 14px;
      background: var(--sidebar);
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 5px;
    }

    /* ===== TABLE ===== */
    .card {
      background: var(--card);
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
      color: var(--text-main);
      font-size: 14px;
    }

    th {
      background: var(--checkins);
    }

    .actions button {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      background: var(--checkins);
    }

    /* ===== RESPONSIVE ===== */
    @media screen and (max-width: 768px) {
      body {
        flex-direction: column;
      }
      .sidebar {
        width: 100%;
        display: flex;
        flex-direction: row;
        overflow-x: auto;
      }
      .sidebar h2 {
        flex: 1 0 auto;
      }
      .nav-btn {
        flex: 1 0 auto;
        text-align: center;
      }
      .content {
        padding: 15px;
      }
      table {
        min-width: 500px;
      }
    }
  </style>
</head>

<body>

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h2>Admin Panel</h2>

    <a class="nav-btn" href="Dashboard.php">Dashboard</a>
    <a class="nav-btn active" href="Caregivers.php">Caregivers</a>
    <a class="nav-btn" href="Elders.php">Elders</a>
    <a class="nav-btn" href="CaregiverLinks.php">Caregiver Links</a>
    <a class="nav-btn" href="HealthAI.php">Health & AI</a>
    <a class="nav-btn" href="Reminders.php">Reminders</a>
    <a class="nav-btn" href="SOS.php">SOS & Emergency</a>
    <a class="nav-btn" href="Messages.php">Messages</a>
    <a class="nav-btn" href="Location.php">Location</a>

    <a class="nav-btn logout" href="Login.php">Logout</a>
  </div>

  <!-- CONTENT -->
  <div class="content">
    <h1>Caregivers</h1>

    <div class="top-bar">
      <button class="add-btn">+ Add Caregiver</button>
    </div>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Assigned Elders</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>1</td>
            <td>John Silva</td>
            <td>0771234567</td>
            <td>Active</td>
            <td>2</td>
            <td class="actions">
              <button class="view">View</button>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Maria Fernando</td>
            <td>0719876543</td>
            <td>Inactive</td>
            <td>0</td>
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
