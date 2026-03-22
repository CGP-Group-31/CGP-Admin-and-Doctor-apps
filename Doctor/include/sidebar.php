<?php

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar {
        width: 270px;
        min-height: 100vh;
        background: #2E7D7A;
        color: #ffffff;
        padding: 22px 16px;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        flex-shrink: 0;
        position: sticky;
        top: 0;
        height: 100vh;
        align-self: flex-start;
        overflow-y: auto;

        display: flex;
        flex-direction: column;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        margin-bottom: 24px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.08);
    }

    .sidebar-brand img {
        width: 85px;
        height: 85px;
        object-fit: contain;
        border-radius: 14px;
        background: #ffffff;
        padding: 0px;
        flex-shrink: 0;
    }

    .sidebar-brand-text span {
        display: block;
        margin-top: 4px;
        font-size: 18px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.78);
        font-family: 'Roboto', sans-serif;
    }

    .sidebar-user {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 14px 16px;
        margin-bottom: 24px;
    }

    .sidebar-user strong {
        display: block;
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #ffffff;
        word-break: break-word;
    }

    .sidebar-user span {
        display: block;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.78);
        line-height: 1.5;
        word-break: break-word;
    }

    .sidebar-nav {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sidebar-nav a {
        display: block;
        padding: 13px 15px;
        border-radius: 12px;
        color: #ffffff;
        font-family: 'Poppins', sans-serif;
        font-size: 15px;
        font-weight: 500;
        background: transparent;
        transition: background 0.2s ease, color 0.2s ease;
        text-decoration: none;
    }

    .sidebar-nav a:hover {
        background: rgba(255, 255, 255, 0.10);
    }

    .sidebar-nav a.active {
        background: #ffffff;
        color: #2E7D7A;
        font-weight: 600;
    }

    .sidebar-logout a {
        display: block;
        text-align: center;
        padding: 14px;
        border-radius: 12px;
        background: #C62828;
        color: #ffffff;
        font-family: 'Poppins', sans-serif;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .sidebar-logout {
        margin-top: auto;
        padding-top: 20px;
    }


    .sidebar-logout a:hover {
        background: #b71c1c;
    }

    @media (max-width: 980px) {
        .sidebar {
            width: 100%;
            min-height: auto;
            padding: 18px 14px;
            position: static;
            height: auto;
            overflow-y: visible;
        }
    }

    .dashboard-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #E5ECE9;
        margin-top: 20px;
    }

    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #EEF2F1;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .list-item strong {
        display: block;
        font-size: 15px;
    }

    .sub-text {
        font-size: 13px;
        color: #7C8B89;
    }

    .date {
        font-size: 12px;
        color: #9AA7A5;
    }

    .empty-text {
        color: #7C8B89;
        font-size: 14px;
    }
</style>

<div class="sidebar">
    <div class="sidebar-brand">
        <img src="FOR ELDER.png" alt="Trustcare Logo">
        <div class="sidebar-brand-text">
            <span>Doctor Portal</span>
        </div>
    </div>

    <div class="sidebar-user">
        <strong><?php echo htmlspecialchars($_SESSION['doctor_name'] ?? 'Doctor'); ?></strong>
        <span><?php echo htmlspecialchars($_SESSION['doctor_email'] ?? ''); ?></span>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
        <a href="patients.php"
            class="<?php echo in_array($currentPage, ['patients.php', 'patient_data.php', 'elder_medications.php'], true) ? 'active' : ''; ?>">Patients</a>
        <a href="profile.php" class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">Profile</a>
    </nav>

    <div class="sidebar-logout">
        <a href="logout.php">Logout</a>
    </div>
</div>
