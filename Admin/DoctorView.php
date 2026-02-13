<?php
$doctors = [
  1 => [
    "name" => "Dr. Perera",
    "specialization" => "Cardiologist",
    "contact" => "0771234567",
    "elders" => "Kamal Silva, Sunil Fernando"
  ],
  2 => [
    "name" => "Dr. Silva",
    "specialization" => "Neurologist",
    "contact" => "0719876543",
    "elders" => "Nimal Perera"
  ],
  3 => [
    "name" => "Dr. Jayasinghe",
    "specialization" => "General Physician",
    "contact" => "0764567890",
    "elders" => "Not Assigned"
  ]
];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!isset($doctors[$id])) {
  echo "Doctor not found!";
  exit;
}

$doctor = $doctors[$id];
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor View</title>
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
  width: 600px;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

h1 {
  text-align: center;
  margin-bottom: 25px;
  color: #1E2A2A;
}

label {
  display: block;
  margin-top: 15px;
  font-weight: bold;
}

input, textarea {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.buttons {
  text-align: center;
  margin-top: 25px;
}

button {
  padding: 10px 20px;
  margin: 0 8px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.update { background: #D6EFE6; }
.delete { background: #C62828; color: white; }
.cancel { background: #ccc; }

</style>
</head>

<body>

<div class="card">
<h1>View / Edit Doctor</h1>

<form>
  <label>Doctor Name</label>
  <input type="text" value="<?= $doctor['name'] ?>">

  <label>Specialization</label>
  <input type="text" value="<?= $doctor['specialization'] ?>">

  <label>Contact</label>
  <input type="text" value="<?= $doctor['contact'] ?>">

  <label>Linked Elders</label>
  <textarea rows="3"><?= $doctor['elders'] ?></textarea>

  <div class="buttons">
    <button type="submit" class="update">Update</button>
    <button type="button" class="delete">Delete</button>
    <button type="button" class="cancel" onclick="window.history.back()">Cancel</button>
  </div>
</form>

</div>

</body>
</html>
