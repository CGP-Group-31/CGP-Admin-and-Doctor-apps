<?php
// Dummy location data
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
    echo "Location not found!";
    exit;
}

$location = $locations[$id];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Location View</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root {
  --bg: #F6F7F3;
  --card: #FFFFFF;
  --text-main: #1E2A2A;
  --text-muted: #6F7F7D;
  --checkins: #D6EFE6;
  --sos: #C62828;
}

* { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }

body {
  background: var(--bg);
  display: flex;
  justify-content: center;
  padding: 40px 0;
}

.card {
  background: var(--card);
  width: 650px;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

h1 {
  text-align: center;
  margin-bottom: 25px;
  color: var(--text-main);
}

label {
  display: block;
  margin: 15px 0 5px;
  font-weight: bold;
  color: var(--text-main);
}

input {
  width: 100%;
  padding: 10px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 14px;
}

.map-container {
  margin-top: 20px;
  border-radius: 8px;
  overflow: hidden;
}

iframe {
  width: 100%;
  height: 300px;
  border: 0;
}

.buttons {
  margin-top: 25px;
  text-align: center;
}

.buttons button {
  padding: 10px 20px;
  margin: 0 10px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
}

.update-btn { background: var(--checkins); }
.delete-btn { background: var(--sos); color:#fff; }
.cancel-btn { background: #ccc; }

</style>
</head>
<body>

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

    <!-- MAP -->
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
