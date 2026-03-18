<?php
session_start();
require 'include/db.php';

if (isset($_SESSION['doctor_logged_in']) && $_SESSION['doctor_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        try {
            $sql = "SELECT u.UserID, u.RoleID, u.FullName, u.Email, u.Phone, u.PasswordHash, u.IsActive, d.LicenseNumber,
                    d.Specialization, d.Hospital FROM Users u INNER JOIN Doctor d ON d.DoctorID = u.UserID
                WHERE u.Email = :email AND u.RoleID = 2";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $doctor = $stmt->fetch();

            if ($doctor) {
                if ((int)$doctor['IsActive'] !== 1) {
                    $error = "Your account is inactive. Please contact the administrator.";
                } elseif (password_verify($password, $doctor['PasswordHash'])) {
                    session_regenerate_id(true);

                    $_SESSION['doctor_logged_in'] = true;
                    $_SESSION['doctor_id'] = $doctor['UserID'];
                    $_SESSION['doctor_name'] = $doctor['FullName'];
                    $_SESSION['doctor_email'] = $doctor['Email'];
                    $_SESSION['doctor_phone'] = $doctor['Phone'];
                    $_SESSION['doctor_license'] = $doctor['LicenseNumber'];
                    $_SESSION['doctor_specialization'] = $doctor['Specialization'];
                    $_SESSION['doctor_hospital'] = $doctor['Hospital'];

                    $update = $pdo->prepare("UPDATE Users SET LastLogin = GETDATE() WHERE UserID = :id");
                    $update->execute([':id' => $doctor['UserID']]);

                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage(), 0);
            $error = "Something went wrong. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trustcare Doctor Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2E7D7A;
            --main-background: #D6EFE6;
            --section-separator: #BEE8DA;
            --section-background: #BEE8DA;
            --background: #F6F7F3;
            --container-background: #F6F7F3;
            --primary-text: #243333;
            --description-text: #6F7F7D;
            --text-shade: #7C8B89;
            --alert-non-critical: #E6B566;
            --button: #C62828;
            --success: #2E7D32;
            --danger: #C62828;
            --white: #ffffff;
            --border: rgba(36, 51, 51, 0.08);
            --shadow: 0 20px 60px rgba(46, 125, 122, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--main-background);
            min-height: 100vh;
            color: var(--primary-text);
        }

        .page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 30px 18px;
        }

        .login-shell {
            width: 100%;
            max-width: 1180px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            background: rgba(246, 247, 243, 0.95);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .brand-side {
            background: rgba(36, 51, 51, 0.96);
            color: #fff;
            padding: 52px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-logo-wrap {
            width: 240px;
            height: 240px;
            background: rgba(255,255,255,0.12);
            border-radius: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            overflow: hidden;
        }

        .brand-logo-wrap img {
            width: 260px;
            height: 260px;
            object-fit: contain;
        }

        .brand-subtitle {
            font-size: 16px;
            line-height: 1.8;
            color: rgba(255,255,255,0.82);
            max-width: 500px;
        }

        .form-side {
            background: var(--container-background);
            padding: 50px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-card {
            width: 100%;
            max-width: 420px;
        }

        .form-badge {
            display: inline-block;
            background: rgba(46, 125, 122, 0.08);
            color: var(--primary);
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 18px;
        }

        .form-title {
            font-family: 'Poppins', sans-serif;
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .form-description {
            font-size: 15px;
            color: var(--description-text);
            line-height: 1.7;
            margin-bottom: 26px;
        }

        .alert {
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-error {
            background: rgba(198, 40, 40, 0.08);
            color: var(--danger);
            border: 1px solid rgba(198, 40, 40, 0.14);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            height: 56px;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 0 18px;
            background: #fff;
            font-size: 16px;
            outline: none;
            transition: 0.25s ease;
        }

        .form-input:focus {
            border-color: rgba(46, 125, 122, 0.45);
            box-shadow: 0 0 0 4px rgba(46, 125, 122, 0.10);
        }

        .btn-login {
            width: 100%;
            height: 56px;
            border: none;
            border-radius: 16px;
            background: var(--primary);
            color: #fff;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            cursor: pointer;
            transition: 0.25s ease;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background: #266a67;
        }

        .mini-note {
            margin-top: 18px;
            text-align: center;
            color: var(--text-shade);
            font-size: 13px;
        }

        @media (max-width: 980px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .brand-side,
            .form-side {
                padding: 34px 24px;
            }

            .brand-logo-wrap {
                width: 150px;
                height: 150px;
            }

            .brand-logo-wrap img {
                width: 115px;
                height: 115px;
            }

         
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="login-shell">
            <div class="brand-side">
                <div class="brand-logo-wrap">
                    <img src="FOR ELDER.png" alt="Trustcare Logo">
                </div>

                <p class="brand-subtitle">
                    Welcome to Trustcare Doctor System, designed to help doctors manage patient care efficiently and securely.
                </p>
            </div>

            <div class="form-side">
                <div class="form-card">
                    <div class="form-badge">Doctor Login</div>
                    <h2 class="form-title">Welcome back</h2>
                    <p class="form-description">
                        Please login with your registered doctor account.
                    </p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="Enter your email"
                                required
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Enter your password"
                                required>
                        </div>

                        <button type="submit" name="login" class="btn-login">Login</button>

                        <p class="mini-note">Only authorized doctors can access this system.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>