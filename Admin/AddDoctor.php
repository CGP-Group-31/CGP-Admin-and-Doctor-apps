<!DOCTYPE html>
<html>
<head>
<title>Add Doctor</title>
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

.save { background: #1F6F78; color: white; }
.cancel { background: #ccc; }

</style>
</head>

<body>

<div class="card">
<h1>Add New Doctor</h1>

<form>
  <label>Doctor Name</label>
  <input type="text" placeholder="Enter name">

  <label>Specialization</label>
  <input type="text" placeholder="Enter specialization">

  <label>Contact</label>
  <input type="text" placeholder="Enter contact number">

  <label>Linked Elders</label>
  <textarea rows="3" placeholder="Enter linked elders"></textarea>

  <div class="buttons">
    <button type="submit" class="save">Save Doctor</button>
    <button type="button" class="cancel" onclick="window.history.back()">Cancel</button>
  </div>
</form>

</div>

</body>
</html>
