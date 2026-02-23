<?php
session_start();
require 'include/db.php'; // your database connection

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch user with RoleID = 1 (Admin)
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE RoleID = 1 AND FullName = :username");
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    if ($admin) {
        // For plain-text passwords:
        if ($password === $admin['PasswordHash']) {
            $_SESSION['admin_id'] = $admin['UserID'];
            $_SESSION['admin_name'] = $admin['FullName'];
            header('Location: Dashboard.php');
            exit;
        } else {
            $error = "Invalid password.";
        }

        // If using hashed passwords in DB, uncomment:
        // if (password_verify($password, $admin['PasswordHash'])) { ... }
    } else {
        $error = "Admin not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <style>
    :root {
      --header: #1F6F78;
      --bg: #F6F7F3;
      --white: #FFFFFF;
      --text-main: #1E2A2A;
      --text-muted: #6F7F7D;
      --sos: #C62828;
    }
    * { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
    body { height:100vh; background:var(--bg); display:flex; align-items:center; justify-content:center; }
    .login-card { width:360px; background:var(--white); padding:30px; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.1); }
    .login-card h2 { text-align:center; color:var(--header); margin-bottom:25px; }
    .login-input { width:100%; padding:12px; margin-bottom:18px; border:1px solid var(--text-muted); border-radius:6px; font-size:14px; color:var(--text-main); }
    .login-input:focus { outline:none; border-color:var(--header); }
    .login-btn { width:100%; padding:12px; background:var(--header); color:var(--white); border:none; border-radius:6px; font-size:15px; cursor:pointer; }
    .login-btn:hover { opacity:0.9; }
    .error { color:var(--sos); text-align:center; font-size:13px; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>Admin Login</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="username" class="login-input" placeholder="Username" required/>
      <input type="password" name="password" class="login-input" placeholder="Password" required/>
      <button type="submit" class="login-btn">LOGIN</button>
    </form>
  </div>
</body>
</html>
