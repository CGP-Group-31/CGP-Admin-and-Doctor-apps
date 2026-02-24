<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Respond to Complaint</title>
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


.content {
  flex: 1;
  padding: 20px;
}

h1 {
  color: var(--text-main);
  margin-bottom: 15px;
}


.card {
  background: var(--card);
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
  max-width: 700px;
}

.detail-row {
  margin-bottom: 12px;
}

.detail-row strong {
  display: block;
  color: var(--text-main);
  margin-bottom: 4px;
}

.detail-row span {
  color: var(--text-muted);
}


textarea {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  resize: none;
  min-height: 120px;
  margin-top: 10px;
}


.btn {
  margin-top: 15px;
  padding: 8px 18px;
  border: none;
  border-radius: 5px;
  background: var(--checkins);
  cursor: pointer;
  font-size: 14px;
}
</style>
</head>

<body>


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
  <a class="nav-btn active" href="Complains.php">Complains</a>
  <a class="nav-btn" href="Location.php">Location</a>

  <a class="nav-btn logout" href="Login.php">Logout</a>
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
