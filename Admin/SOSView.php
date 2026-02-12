<?php
$sosRecords = [
  1 => [
    "elder" => "Sunil Perera",
    "age" => 72,
    "sos_type" => "Medical Emergency",
    "time" => "09:15 AM",
    "date" => "2026-02-11",
    "location" => "Colombo 05, Sri Lanka",
    "description" => "Chest pain reported. Immediate medical attention required.",
    "caregiver" => "John Silva",
    "doctor" => "Dr. Perera",
    "status" => "Active"
  ],
  2 => [
    "elder" => "Mary Fernando",
    "age" => 80,
    "sos_type" => "Fall Detected",
    "time" => "03:20 PM",
    "date" => "2026-02-11",
    "location" => "Kandy, Sri Lanka",
    "description" => "Fall detected in living room. No response initially.",
    "caregiver" => "Maria Fernando",
    "doctor" => "Dr. Silva",
    "status" => "Active"
  ]
];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!isset($sosRecords[$id])) {
  echo "SOS Record Not Found!";
  exit;
}

$sos = $sosRecords[$id];
?>

<!DOCTYPE html>
<html>
<head>
<title>SOS Details</title>
<style>
body {
  background: #F6F7F3;
  font-family: Arial;
  display: flex;
  justify-content: center;
  padding: 40px 0;
}

.card {
  background: white;
  width: 650px;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

h1 {
  text-align: center;
  margin-bottom: 25px;
  color: #1E2A2A;
}

.detail {
  margin-bottom: 15px;
}

.label {
  font-weight: bold;
  color: #1E2A2A;
}

.value {
  margin-top: 5px;
  color: #6F7F7D;
}

.status-active {
  color: #C62828;
  font-weight: bold;
}

.back-btn {
  margin-top: 25px;
  text-align: center;
}

button {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  background: #1F6F78;
  color: white;
  cursor: pointer;
}

</style>
</head>

<body>

<div class="card">
<h1>SOS Emergency Details</h1>

<div class="detail">
  <div class="label">Elder Name</div>
  <div class="value"><?= $sos['elder'] ?> (Age: <?= $sos['age'] ?>)</div>
</div>

<div class="detail">
  <div class="label">SOS Type</div>
  <div class="value"><?= $sos['sos_type'] ?></div>
</div>

<div class="detail">
  <div class="label">Date & Time</div>
  <div class="value"><?= $sos['date'] ?> - <?= $sos['time'] ?></div>
</div>

<div class="detail">
  <div class="label">Location</div>
  <div class="value"><?= $sos['location'] ?></div>
</div>

<div class="detail">
  <div class="label">Description</div>
  <div class="value"><?= $sos['description'] ?></div>
</div>

<div class="detail">
  <div class="label">Assigned Caregiver</div>
  <div class="value"><?= $sos['caregiver'] ?></div>
</div>

<div class="detail">
  <div class="label">Assigned Doctor</div>
  <div class="value"><?= $sos['doctor'] ?></div>
</div>

<div class="detail">
  <div class="label">Status</div>
  <div class="value status-active"><?= $sos['status'] ?></div>
</div>

<div class="back-btn">
  <button onclick="window.history.back()">Back to SOS List</button>
</div>

</div>

</body>
</html>
