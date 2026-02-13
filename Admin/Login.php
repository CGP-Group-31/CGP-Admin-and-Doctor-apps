<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>

  <style>
    /* ===== COLOR VARIABLES (ONLY YOUR PALETTE) ===== */
    :root {
      --header: #1F6F78;
      --checkins: #D6EFE6;
      --bg: #F6F7F3;
      --white: #FFFFFF;
      --text-main: #1E2A2A;
      --text-muted: #6F7F7D;
      --reminder: #E6B450;
      --sos: #C62828;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      height: 100vh;
      background: var(--bg);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ===== LOGIN CARD ===== */
    .login-card {
      width: 360px;
      background: var(--white);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .login-card h2 {
      text-align: center;
      color: var(--header);
      margin-bottom: 25px;
    }

    /* ===== INPUTS ===== */
    .login-input {
      width: 100%;
      padding: 12px;
      margin-bottom: 18px;
      border: 1px solid var(--text-muted);
      border-radius: 6px;
      font-size: 14px;
      color: var(--text-main);
    }

    .login-input:focus {
      outline: none;
      border-color: var(--header);
    }

    /* ===== BUTTON ===== */
    .login-btn {
      width: 100%;
      padding: 12px;
      background: var(--header);
      color: var(--white);
      border: none;
      border-radius: 6px;
      font-size: 15px;
      cursor: pointer;
    }

    .login-btn:hover {
      opacity: 0.9;
    }

    /* ===== ERROR MESSAGE (OPTIONAL) ===== */
    .error {
      color: var(--sos);
      text-align: center;
      font-size: 13px;
      margin-bottom: 10px;
      display: none;
    }
  </style>
</head>

<body>

  <div class="login-card">
    <h2>Admin Login</h2>

    <!-- Error message if needed -->
    <div class="error">Invalid username or password</div>

    <form>
      <input
        type="text"
        class="login-input"
        placeholder="Username"
        required
      />

      <input
        type="password"
        class="login-input"
        placeholder="Password"
        required
      />

      <button type="submit" class="login-btn">
        LOGIN
      </button>
    </form>
  </div>

</body>
</html>
