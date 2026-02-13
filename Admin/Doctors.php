<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Doctors</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    :root {
      --sidebar: #1F6F78;
      --bg: #F6F7F3;
      --card: #FFFFFF;
      --text-main: #1E2A2A;
      --text-muted: #6F7F7D;
      --checkins: #D6EFE6;
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
    }

    h1 {
      color: var(--text-main);
      margin-bottom: 15px;
    }

    /* ===== SEARCH ===== */
    .search {
      max-width: 350px;
      padding: 10px 14px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-bottom: 15px;
    }

    /* ===== CARD ===== */
    .card {
      background: var(--card);
      padding: 15px;
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
      font-size: 14px;
      text-align: left;
    }

    th {
      background: var(--checkins);
    }

    .action-btn {
      padding: 6px 14px;
      border: none;
      border-radius: 4px;
      background: var(--checkins);
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
  <a class="nav-btn" href="CaregiverLinks.php">Caregiver Links</a>
  <a class="nav-btn" href="HealthAI.php">Health & AI</a>
  <a class="nav-btn" href="Reminders.php">Reminders</a>
  <a class="nav-btn" href="SOS.php">SOS & Emergency</a>
  <a class="nav-btn" href="Complains.php">Complains</a>
  <a class="nav-btn" href="Location.php">Location</a>

  <a class="nav-btn logout" href="Login.php">Logout</a>
</div>

<!-- CONTENT -->
<div class="content">
  <h1>Doctors</h1>

 <div style="margin-bottom:15px;">
  <input
    type="text"
    id="searchInput"
    placeholder="Search doctor or elder..."
    onkeyup="searchTable()"
    style="
      width: 300px;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    "
  >
</div>
<div style="margin-bottom:15px;">
  <a href="AddDoctor.php">
    <button style="
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      background: #1F6F78;
      color: white;
      font-size: 14px;
      cursor: pointer;
    ">
      + Add Doctor
    </button>
  </a>
</div>



  <div class="card">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Doctor Name</th>
        <th>Specialization</th>
        <th>Contact</th>
        <th>Linked Elders</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <td>1</td>
        <td>Dr. Perera</td>
        <td>Cardiologist</td>
        <td>0771234567</td>
        <td>
          Kamal Silva<br>
          Sunil Fernando
        </td>
        <td>
      <button class="action-btn" onclick="window.location.href='DoctorView.php?id=1'">View</button>

        </td>
      </tr>

      <tr>
        <td>2</td>
        <td>Dr. Silva</td>
        <td>Neurologist</td>
        <td>0719876543</td>
        <td>
          Nimal Perera
        </td>
        <td>
          <button class="action-btn">View</button>
        </td>
      </tr>

      <tr>
        <td>3</td>
        <td>Dr. Jayasinghe</td>
        <td>General Physician</td>
        <td>0764567890</td>
        <td>
          Not Assigned
        </td>
        <td>
          <button class="action-btn">View</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<script>
function searchTable() {
  const input = document.getElementById("searchInput");
  const filter = input.value.toLowerCase();
  const table = document.querySelector("table");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) { // skip header
    let rowText = rows[i].innerText.toLowerCase();
    rows[i].style.display = rowText.includes(filter) ? "" : "none";
  }
}
</script>


</body>
</html>
