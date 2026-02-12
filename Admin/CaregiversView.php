<?php
// Dummy data
$caregivers = [
    1 => [
        'full_name' => 'John Silva',
        'phone' => '0771234567',
        'email' => 'johnsilva@gmail.com',
        'status' => 'Active',
        'assigned_elders' => 2,
        'address' => 'Colombo, Sri Lanka'
    ],
    2 => [
        'full_name' => 'Maria Fernando',
        'phone' => '0719876543',
        'email' => 'mariaf@gmail.com',
        'status' => 'Inactive',
        'assigned_elders' => 0,
        'address' => 'Kandy, Sri Lanka'
    ]
];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!isset($caregivers[$id])) {
    echo "Caregiver not found!";
    exit;
}

$caregiver = $caregivers[$id];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Caregiver</title>
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
      --btn-color: var(--checkins);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

    body {
      background: var(--bg);
      display: flex;
      justify-content: center; /* center horizontally */
      align-items: flex-start; /* top align */
      padding: 40px 0;
    }

    .card {
      background: var(--card);
      width: 600px; /* bigger card */
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

    input, select {
      width: 100%;
      padding: 10px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
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

    .update-btn { background: var(--checkins); color: var(--text-main); }
    .delete-btn { background: var(--sos); color: #fff; }
    .cancel-btn { background: #ccc; color: var(--text-main); }

  </style>
</head>
<body>

  <div class="card">
    <h1>Caregiver Info</h1>

    <form>
      <label>Full Name</label>
      <input type="text" value="<?= $caregiver['full_name'] ?>">

      <label>Phone</label>
      <input type="text" value="<?= $caregiver['phone'] ?>">

      <label>Email</label>
      <input type="email" value="<?= $caregiver['email'] ?>">

      <label>Status</label>
      <select>
        <option <?= $caregiver['status']=='Active'?'selected':'' ?>>Active</option>
        <option <?= $caregiver['status']=='Inactive'?'selected':'' ?>>Inactive</option>
      </select>

      <label>Assigned Elders</label>
      <input type="number" value="<?= $caregiver['assigned_elders'] ?>">

      <label>Address</label>
      <input type="text" value="<?= $caregiver['address'] ?>">

      <div class="buttons">
        <button type="submit" class="update-btn">Update</button>
        <button type="button" class="delete-btn">Delete</button>
        <button type="button" class="cancel-btn" onclick="window.history.back();">Cancel</button>
      </div>
    </form>
  </div>

</body>
</html>
