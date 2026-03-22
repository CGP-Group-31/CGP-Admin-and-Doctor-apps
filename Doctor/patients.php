<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['doctor_logged_in'])) {
    header("Location: index.php");
    exit;
}

$doctorId = $_SESSION['doctor_id'];
$search = trim($_GET['search'] ?? '');
$patients = [];

try {
    $sql = "SELECT u.UserID, u.FullName, u.Phone, u.Email, u.Gender, u.DateOfBirth, u.address, u.IsActive
        FROM ElderProfiles ep INNER JOIN Users u ON u.UserID = ep.ElderID
         WHERE ep.PreferredDoctorID = :doctor_id AND u.IsActive = 1 AND u.RoleID = 5";

    $params = [
        ':doctor_id' => $doctorId
    ];

    if ($search !== '') {
        $sql .= " AND (u.FullName LIKE :search_name OR u.Phone LIKE :search_phone)";
        $params[':search_name'] = '%' . $search . '%';
        $params[':search_phone'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY u.FullName ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Patients page error: " . $e->getMessage(), 0);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients - Trustcare</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #2E7D7A;
            --main-background: #D6EFE6;
            --background: #F6F7F3;
            --container-background: #FFFFFF;
            --primary-text: #243333;
            --description-text: #6F7F7D;
            --text-shade: #7C8B89;
            --button: #C62828;
            --border: #E5ECE9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--background);
            color: var(--primary-text);
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .content {
            flex: 1;
            padding: 28px;
        }

        .top-section {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 22px;
        }

        .top-section h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .top-section p {
            color: var(--description-text);
            font-size: 15px;
            line-height: 1.7;
        }

        .search-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 22px;
        }

        .search-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 240px;
            height: 50px;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0 16px;
            font-size: 15px;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--primary);
        }

        .search-btn,
        .reset-btn {
            height: 50px;
            padding: 0 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .search-btn {
            background: var(--primary);
            color: #fff;
        }

        .reset-btn {
            background: #EAEFEE;
            color: var(--primary-text);
        }

        .table-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
        }

        .table-head {
            padding: 20px 22px;
            border-bottom: 1px solid var(--border);
        }

        .table-head h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 22px;
            font-weight: 700;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #F8FAF9;
            color: var(--primary-text);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            text-align: left;
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody td {
            padding: 16px 18px;
            font-size: 15px;
            color: var(--primary-text);
            border-bottom: 1px solid #EEF2F1;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #FBFCFC;
        }

        .status-badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
        }

        .status-active {
            background: #E4F4EE;
            color: #1B6E4B;
        }

        .status-inactive {
            background: #FCEAEA;
            color: #B42318;
        }

        .view-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 92px;
            padding: 10px 14px;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .empty-state {
            padding: 30px 22px;
            color: var(--description-text);
            font-size: 15px;
        }

        @media (max-width: 980px) {
            .layout {
                flex-direction: column;
            }

            .content {
                padding: 18px;
            }
        }
    </style>
</head>

<body>
    <div class="layout">
        <?php include 'include/sidebar.php'; ?>

        <main class="content">
            <div class="top-section">
                <h1>Patients</h1>
                <p>Search your patients by patient name or mobile number.</p>
            </div>

            <div class="search-card">
                <form method="GET" class="search-form">
                    <input type="text" name="search" class="search-input"
                        placeholder="Search by patient name or mobile number"
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-btn">Search</button>
                    <a href="patients.php" class="reset-btn">Reset</a>
                </form>
            </div>

            <div class="table-card">
                <div class="table-head">
                    <h2>Patient List</h2>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Full Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($patients)): ?>
                                <?php foreach ($patients as $patient): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($patient['UserID']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['FullName']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['Phone']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['Email'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($patient['Gender'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ((int) $patient['IsActive'] === 1): ?>
                                                <span class="status-badge status-active">Active</span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a class="view-btn"
                                                href="patient_data.php?id=<?php echo urlencode($patient['UserID']); ?>">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-state">
                                        No patients found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>