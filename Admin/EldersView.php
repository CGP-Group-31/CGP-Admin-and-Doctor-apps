<?php
// Dummy data
$elders = [
    1 => [
        'full_name' => 'Sunil Perera',
        'age' => 72,
        'gender' => 'Male',
        'health_status' => 'Stable',
        'risk_level' => 'Low',
        'caregiver' => 'John Silva',
        'address' => 'Colombo, Sri Lanka',
        'medications' => 'Medicine A, Medicine B',
        'allergies' => 'None'
    ],
    2 => [
        'full_name' => 'Mary Fernando',
        'age' => 80,
        'gender' => 'Female',
        'health_status' => 'Critical',
        'risk_level' => 'High',
        'caregiver' => 'Maria Fernando',
        'address' => 'Kandy, Sri Lanka',
        'medications' => 'Medicine C',
        'allergies' => 'Penicillin'
    ],
    3 => [
        'full_name' => 'Ravi Kumar',
        'age' => 68,
        'gender' => 'Male',
        'health_status' => 'Stable',
        'risk_level' => 'Low',
        'caregiver' => 'Alice Perera',
        'address' => 'Galle, Sri Lanka',
        'medications' => 'Medicine D',
        'allergies' => 'None'
    ]
];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!isset($elders[$id])) {
    echo "Elder not found!";
    exit;
}

$elder = $elders[$id];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Elder</title>
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

body {
  background: var(--bg);
  display: flex;
  justify-content: center;
  padding: 40px 0;
}

.card {
  background: var(--card);
  width: 650px; /* larger card */
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

h1 {
  color: var(--text-main);
  text-align: center;
  margin-bottom: 25px;
}

label {
  display: block;
  margin: 15px 0 5px;
  color: var(--text-main);
  font-weight: bold;
}

input, select, textarea {
  width: 100%;
  padding: 10px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 14px;
}

textarea { resize: vertical; }

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

.update-btn { background: var(--checkins); color: var(--text-main); }
.delete-btn { background: var(--sos); color: #fff; }
.cancel-btn { background: #ccc; color: var(--text-main); }

</style>
</head>
<body>

<div class="card">
  <h1>Elder View</h1>

  <form>
    <label>Full Name</label>
    <input type="text" value="<?= $elder['full_name'] ?>">

    <label>Age</label>
    <input type="number" value="<?= $elder['age'] ?>">

    <label>Gender</label>
    <select>
      <option <?= $elder['gender']=='Male'?'selected':'' ?>>Male</option>
      <option <?= $elder['gender']=='Female'?'selected':'' ?>>Female</option>
    </select>

    <label>Health Status</label>
    <input type="text" value="<?= $elder['health_status'] ?>">

    <label>Risk Level</label>
    <select>
      <option <?= $elder['risk_level']=='Low'?'selected':'' ?>>Low</option>
      <option <?= $elder['risk_level']=='Medium'?'selected':'' ?>>Medium</option>
      <option <?= $elder['risk_level']=='High'?'selected':'' ?>>High</option>
    </select>

    <label>Assigned Caregiver</label>
    <input type="text" value="<?= $elder['caregiver'] ?>">

    <label>Address</label>
    <input type="text" value="<?= $elder['address'] ?>">

    <label>Medications</label>
    <textarea><?= $elder['medications'] ?></textarea>

    <label>Allergies</label>
    <textarea><?= $elder['allergies'] ?></textarea>

    <div class="buttons">
      <button type="submit" class="update-btn">Update</button>
      <button type="button" class="delete-btn">Delete</button>
      <button type="button" class="cancel-btn" onclick="window.history.back();">Cancel</button>
    </div>
  </form>
</div>

</body>
</html>
