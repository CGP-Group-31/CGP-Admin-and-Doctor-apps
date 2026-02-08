<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Location</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    :root {
      --sidebar: #1F6F78;
      --bg: #F6F7F3;
      --card: #FFFFFF;
      --text-main: #1E2A2A;
      --text-muted: #6F7F7D;
      --checkins: #D6EFE6; /* light green action button */
      --sos: #C62828;

      --btn-color: var(--checkins);
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
      padding: 20px;
      overflow-x: auto;
    }

    h1 {
      color: var(--text-main);
      margin-bottom: 5px;
    }

    .subtitle {
      color: var(--text-muted);
      margin-bottom: 15px;
      font-size: 14px;
    }

    /* ===== CARD ===== */
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
      min-width: 500px;
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

    /* ===== ACTION BUTTON ===== */
    .action-btn {
      padding: 6px 14px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      background: var(--btn-color);
      color: var(--text-main);
    }

    /* ===== RESPONSIVE ===== */
    @media screen and (max-width: 768px) {
      body {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
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

      table {
        min-width: 400px;
      }
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
    <a class="nav-btn" href="CaregiverLinks.php">Caregiver Links</a>
    <a class="nav-btn" href="HealthAI.php">Health & AI</a>
    <a class="nav-btn" href="Reminders.php">Reminders</a>
    <a class="nav-btn" href="SOS.php">SOS & Emergency</a>
    <a class="nav-btn" href="Messages.php">Messages</a>
    <a class="nav-btn active" href="Location.php">Location</a>

    <a class="nav-btn logout" href="Login.php">Logout</a>
  </div>

  <!-- CONTENT -->
  <div class="content">
    <h1>Location</h1>
<br>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Last Known Location</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>1</td>
            <td>Kamal Silva</td>
            <td>Elder</td>
            <td>Colombo</td>
            <td>
              <button class="action-btn">View</button>
            </td>
          </tr>

          <tr>
            <td>2</td>
            <td>Nimal Perera</td>
            <td>Caregiver</td>
            <td>Kandy</td>
            <td>
              <button class="action-btn">View</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
