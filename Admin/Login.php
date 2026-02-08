<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>

  <!-- Styles -->
  <style>
    /* ---------- VARIABLES ---------- */
    :root {
      --header-bg: #1F6F78;
      --page-bg: #F6F7F3;
      --card-bg: #FFFFFF;
      --text-main: #1E2A2A;
      --text-secondary: #6F7F7D;
      --input-bg: #FFFFFF;
      --btn-primary: #1F6F78;
      --btn-primary-hover: #16555f;
      --link-color: #1F6F78;
      --danger: #C62828;
      --accent: #E6B450;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    body {
      background: var(--page-bg);
      color: var(--text-main);
      display: flex;
      height: 100vh;
      align-items: center;
      justify-content: center;
    }

    /* ---------- HEADER / NAVBAR ---------- */
    header {
      width: 100%;
      background: var(--header-bg);
      padding: 15px 30px;
      color: #fff;
      font-size: 22px;
      font-weight: bold;
    }

    /* ---------- CONTAINER ---------- */
    .login-wrapper {
      width: 100%;
      max-width: 400px;
      margin: 30px auto;
      padding: 30px;
      background: var(--card-bg);
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .login-wrapper h2 {
      text-align: center;
      color: var(--header-bg);
      margin-bottom: 25px;
    }

    /* ---------- INPUTS ---------- */
    .login-input {
      width: 100%;
      padding: 12px 14px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 7px;
      background: var(--input-bg);
      font-size: 15px;
      color: var(--text-main);
    }

    .login-input:focus {
      outline: none;
      border-color: var(--header-bg);
      box-shadow: 0 0 5px rgba(31,111,120,0.3);
    }

    /* ---------- BUTTON ---------- */
    .btn-login {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 7px;
      background-color: var(--btn-primary);
      color: #fff;
      font-size: 16px;
      cursor: pointer;
      transition: 0.2s ease;
    }

    .btn-login:hover {
      background-color: var(--btn-primary-hover);
    }

    .forgot-link {
      display: block;
      margin-top: 10px;
      text-align: right;
      color: var(--link-color);
      font-size: 14px;
      text-decoration: none;
    }

    .forgot-link:hover {
      text-decoration: underline;
    }

    /* ---------- ERROR MESSAGE ---------- */
    .error-msg {
      color: var(--danger);
      text-align: center;
      margin-bottom: 10px;
      font-size: 14px;
    }
  </style>
</head>

<body>

  <header>
    Admin Dashboard
  </header>

  <div class="login-wrapper">
    <h2>Admin Login</h2>

    <!-- Error (show if needed) -->
    <div id="error" class="error-msg" style="display: none;">Invalid credentials</div>

    <!-- Login Form -->
    <form id="loginForm">
      <input
        type="text"
        class="login-input"
        placeholder="Username"
        id="username"
        required
      />

      <input
        type="password"
        class="login-input"
        placeholder="Password"
        id="password"
        required
      />

      <button type="submit" class="btn-login">LOGIN</button>
    </form>

    <a href="#" class="forgot-link">Forgot Password?</a>
  </div>

</body>
</html>
