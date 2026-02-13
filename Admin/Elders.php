<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Elders</title>
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

* { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
body { display:flex; height:100vh; background: var(--bg); }

/* Sidebar */
.sidebar { width:240px; background: var(--sidebar); color:#fff; display:flex; flex-direction:column; }
.sidebar h2 { padding:20px; text-align:center; border-bottom:1px solid rgba(255,255,255,0.2); }
.nav-btn { padding:14px 20px; text-decoration:none; color:#fff; display:block; }
.nav-btn:hover, .nav-btn.active { background: rgba(255,255,255,0.15); }
.logout { margin-top:auto; background: var(--sos); text-align:center; }

/* Content */
.content { flex:1; padding:25px; overflow-y:auto; }
h1 { color: var(--text-main); margin-bottom:20px; }

#searchInput {
  width: 300px; padding: 10px 15px; border-radius: 25px; border: 1px solid #ccc; margin-bottom: 20px; font-size: 14px;
}

.card { background: var(--card); padding:20px; border-radius:10px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
table { width:100%; border-collapse:collapse; }
th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; font-size:14px; color: var(--text-main); }
th { background: var(--checkins); }
.actions button { padding:6px 10px; border:none; border-radius:4px; cursor:pointer; font-size:12px; background: var(--checkins); }
</style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Panel</h2>
  <a class="nav-btn" href="Dashboard.php">Dashboard</a>
  <a class="nav-btn" href="Caregivers.php">Caregivers</a>
  <a class="nav-btn active" href="Elders.php">Elders</a>
  <a class="nav-btn" href="Doctors.php">Doctors</a>
  <a class="nav-btn" href="CaregiverLinks.php">Caregiver Links</a>
  <a class="nav-btn" href="HealthAI.php">Health & AI</a>
  <a class="nav-btn" href="Reminders.php">Reminders</a>
  <a class="nav-btn" href="SOS.php">SOS & Emergency</a>
  <a class="nav-btn" href="Complains.php">Complains</a>
  <a class="nav-btn" href="Location.php">Location</a>
  <a class="nav-btn logout" href="Login.php">Logout</a>
</div>

<div class="content">
<h1>Elders</h1>

<input type="text" id="searchInput" placeholder="Search Elders...">

<div class="card">
  <table id="elderTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Health Status</th>
        <th>Risk Level</th>
        <th>Caregiver Assigned</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>Sunil Perera</td>
        <td>72</td>
        <td>Stable</td>
        <td>Low</td>
        <td>John Silva</td>
        <td class="actions">
          <a href="EldersView.php?id=1"><button>View</button></a>
        </td>
      </tr>
      <tr>
        <td>2</td>
        <td>Mary Fernando</td>
        <td>80</td>
        <td>Critical</td>
        <td>High</td>
        <td>Maria Fernando</td>
        <td class="actions">
          <a href="EldersView.php?id=2"><button>View</button></a>
        </td>
      </tr>
      <tr>
        <td>3</td>
        <td>Ravi Kumar</td>
        <td>68</td>
        <td>Stable</td>
        <td>Low</td>
        <td>Alice Perera</td>
        <td class="actions">
          <a href="EldersView.php?id=3"><button>View</button></a>
        </td>
      </tr>
    </tbody>
  </table>
</div>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const table = document.getElementById('elderTable').getElementsByTagName('tbody')[0];

searchInput.addEventListener('keyup', function() {
  const filter = this.value.toLowerCase();
  const rows = table.getElementsByTagName('tr');

  for (let i = 0; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName('td');
    let match = false;

    for (let j = 1; j < cells.length - 1; j++) {
      if (cells[j].textContent.toLowerCase().includes(filter)) {
        match = true;
        break;
      }
    }
    rows[i].style.display = match ? '' : 'none';
  }
});
</script>

</body>
</html>
