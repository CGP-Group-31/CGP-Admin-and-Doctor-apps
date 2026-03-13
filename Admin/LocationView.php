<?php

$locations = [
    1 => [
        'name' => 'Kamal Silva',
        'role' => 'Elder',
        'contact' => '+94 77 123 4567',
        'address' => 'Negombo, Sri Lanka',
        'last_updated' => '2026-02-10 10:45 AM'
    ],
    2 => [
        'name' => 'Nimal Perera',
        'role' => 'Caregiver',
        'contact' => '+94 71 987 6543',
        'address' => 'Kandy, Sri Lanka',
        'last_updated' => '2026-02-09 05:30 PM'
    ]
];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!isset($locations[$id])) {
    echo "<div class='card' style='max-width:520px; margin:40px auto; text-align:center;'>Location not found!</div>";
    exit;
}

$location = $locations[$id];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/theme.css">
<meta charset="UTF-8">
<title>Location View</title>
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
    <a class="nav-btn" href="Complains.php"><i class="fas fa-exclamation-circle"></i> <span>Complains</span></a>
    <a class="nav-btn active" href="Location.php"><i class="fas fa-map-marker-alt"></i> <span>Location</span></a>
    <a class="nav-btn" href="Admins.php"><i class="fas fa-user-shield"></i> <span>Manage Admins</span></a>
    <a class="nav-btn logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
  </div>

  <div class="content">
    <div class="card">
      <h1>Location View</h1>

  <form>

    <label>Name</label>
    <input type="text" value="<?= $location['name'] ?>">

    <label>Role</label>
    <input type="text" value="<?= $location['role'] ?>">

    <label>Contact</label>
    <input type="text" value="<?= $location['contact'] ?>">

    <label>Address</label>
    <input type="text" id="addressInput"
           value="<?= $location['address'] ?>"
           onkeyup="updateMap()">

    <label>Last Updated</label>
    <input type="text" value="<?= $location['last_updated'] ?>" readonly>

    
    <div class="map-container">
      <iframe id="mapFrame"
        src="https://maps.google.com/maps?q=<?= urlencode($location['address']) ?>&output=embed">
      </iframe>
    </div>

    <div class="buttons">
      <button type="submit" class="update-btn">Update</button>
      <button type="button" class="delete-btn">Delete</button>
      <button type="button" class="cancel-btn" onclick="window.history.back();">Cancel</button>
    </div>

  </form>
    </div>
  </div>

<script>
function updateMap() {
  var address = document.getElementById("addressInput").value;
  document.getElementById("mapFrame").src =
    "https://maps.google.com/maps?q=" +
    encodeURIComponent(address) +
    "&output=embed";
}
</script>

</body>
</html>
