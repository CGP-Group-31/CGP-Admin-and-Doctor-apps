<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['doctor_logged_in'])) {
    header("Location: index.php");
    exit;
}

$elder_id = isset($_GET['elder_id']) ? $_GET['elder_id'] : null;
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$medicines = [];

$apiUrl = "http://159.65.158.217:8000/api/v1/caregiver/medication/elder/" . urlencode($elder_id);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_HTTPHEADER => ["accept: application/json"],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    $decoded = json_decode($response, true);
    if (is_array($decoded)) {
        $medicines = $decoded;
    }
}

$dailyData = [];

$dailyApi = "http://159.65.158.217:8000/api/v1/daily-reports/elder/" . urlencode($elder_id) . "/medication?date=" . urlencode($selected_date);

$ch2 = curl_init();
curl_setopt_array($ch2, [
    CURLOPT_URL => $dailyApi,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_HTTPHEADER => ["accept: application/json"]
]);

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

if ($httpCode2 == 200) {
    $decoded2 = json_decode($response2, true);
    if (isset($decoded2['items'])) {
        $dailyData = $decoded2['items'];
    }
}
?>
<?php include 'include/header.php'; ?>

<style>
    :root {
        --card-bg: #FFFFFF;
        --border: #E5ECE9;
        --text: #243333;
        --subtext: #6F7F7D;
    }

    .section-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 18px;
        margin-bottom: 22px;
        overflow: hidden;
    }

    .section-head {
        padding: 20px;
        border-bottom: 1px solid var(--border);
    }

    .section-head h2 {
        font-family: 'Poppins';
        font-size: 22px;
        margin: 0;
    }

    .section-body {
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #F8FAF9;
        padding: 14px;
        text-align: left;
        font-family: 'Poppins';
        font-size: 14px;
        border-bottom: 1px solid var(--border);
    }

    td {
        padding: 14px;
        border-bottom: 1px solid #EEF2F1;
    }

    tr:hover {
        background: #FAFBFB;
    }

    .time-chip {
        background: #EDF1F0;
        padding: 5px 8px;
        border-radius: 6px;
        margin: 2px;
        display: inline-block;
        font-size: 12px;
    }

    .badge {
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-family: 'Poppins';
    }

    .taken {
        background: #E4F4EE;
        color: #1B6E4B;
    }

    .missed {
        background: #FDECEA;
        color: #C62828;
    }

    .skipped {
        background: #FFF4E5;
        color: #F57C00;
    }

    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .back-btn {
        padding: 10px 14px;
        background: #2E7D7A;
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-family: 'Poppins';
        font-size: 14px;
        font-weight: 600;
    }

    .date-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    input[type="date"] {
        padding: 8px;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    button {
        padding: 8px 14px;
        background: #2E7D7A;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }
</style>

<div class="dashboard-layout">
    <?php include 'include/sidebar.php'; ?>
    <main class="main-content">
        <div class="topbar">
            <div>
                <h1>Medicine Management</h1>
                <p>Daily adherence and medication list for this elder.</p>
            </div>

            <a href="patient_data.php?id=<?php echo $elder_id; ?>" class="back-btn">Back</a>
        </div>

        <div class="top-bar">
            <form method="GET" class="date-form">
                <input type="hidden" name="elder_id" value="<?php echo $elder_id; ?>">
                <input type="date" name="date" value="<?php echo $selected_date; ?>">
                <button>Filter</button>
            </form>
        </div>

        <div class="section-card">
            <div class="section-head">
                <h2>Daily Medication (Adherence)</h2>
            </div>

            <div class="section-body">
                <?php if (!empty($dailyData)) { ?>
                    <table>
                        <tr>
                            <th>Medicine</th>
                            <th>Dosage</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>

                        <?php foreach ($dailyData as $item) { ?>
                            <tr>
                                <td><?php echo $item['medication_name']; ?></td>
                                <td><?php echo $item['dosage']; ?></td>
                                <td><?php echo date('H:i', strtotime($item['scheduled_for'])); ?></td>
                                <td>
                                    <span class="badge <?php echo strtolower($item['status']); ?>">
                                        <?php echo $item['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } else { ?>
                    <p>No records found.</p>
                <?php } ?>
            </div>
        </div>

        <div class="section-card">
            <div class="section-head">
                <h2>Medicine List (Elder ID: <?php echo $elder_id; ?>)</h2>
            </div>

            <div class="section-body">
                <?php if (!empty($medicines)) { ?>
                    <table>
                        <tr>
                            <th>Medicine</th>
                            <th>Dosage</th>
                            <th>Instructions</th>
                            <th>Times</th>
                            <th>Repeat</th>
                            <th>Start</th>
                            <th>End</th>
                        </tr>

                        <?php foreach ($medicines as $med) { ?>
                            <tr>
                                <td><strong><?php echo $med['name']; ?></strong></td>
                                <td><?php echo $med['dosage']; ?></td>
                                <td><?php echo $med['instructions']; ?></td>
                                <td>
                                    <?php foreach ($med['times'] as $time) { ?>
                                        <span class="time-chip"><?php echo $time; ?></span>
                                    <?php } ?>
                                </td>
                                <td><?php echo $med['repeatDays']; ?></td>
                                <td><?php echo $med['startDate']; ?></td>
                                <td><?php echo $med['endDate']; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } else { ?>
                    <p>No medicines found.</p>
                <?php } ?>

            </div>
        </div>

    </main>
</div>
</body>
</html>
