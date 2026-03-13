<?php
session_start();
require 'include/db.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username']); 
    $password = trim($_POST['password']);

    
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE RoleID = 1 AND (FullName = :input1 OR Email = :input2)");
    
    
    $stmt->execute([
        'input1' => $usernameOrEmail,
        'input2' => $usernameOrEmail
    ]);
    
    $admin = $stmt->fetch();

    if ($admin) {
        
        if (password_verify($password, $admin['PasswordHash'])) {
            $_SESSION['admin_id'] = $admin['UserID'];
            $_SESSION['admin_name'] = $admin['FullName'];
            header('Location: Dashboard.php');
            exit;
        } 
        
        elseif ($password === $admin['PasswordHash']) {
            $_SESSION['admin_id'] = $admin['UserID'];
            $_SESSION['admin_name'] = $admin['FullName'];
            header('Location: Dashboard.php');
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Admin account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/theme.css">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  
    <script src="assets/app.js" defer></script>
</head>
<body class="auth">
  <div class="login-card">
    <div class="login-hero">
      <h1>TrustCare Admin Portal</h1>
      <p>Secure access for TrustCare administrators.</p>
      <p>Monitor safety, manage care teams, and respond faster.</p>
    </div>

    <div class="login-form">
      <h2>TrustCare Login</h2>
      <p>Sign in to manage TrustCare operations.</p>

      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="text" name="username" class="login-input" placeholder="Username or Email" required/>
        <input type="password" name="password" class="login-input" placeholder="Password" required/>
        <button type="submit" class="login-btn">Sign In</button>
      </form>
    </div>
  </div>
</body>
</html>
