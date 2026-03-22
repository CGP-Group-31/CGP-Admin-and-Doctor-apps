<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['doctor_logged_in'])) {
    header("Location: index.php");
    exit;
}

$doctorId = $_SESSION['doctor_id'];
$patientId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($patientId <= 0) {
    header("Location: patients.php");
    exit;
}

$patient = null;
$caregivers = [];
$medicalProfile = null;
$medicalProfileError = '';

try {
    $sqlPatient = "SELECT u.UserID, u.FullName, u.Email, u.Phone, u.DateOfBirth,
            u.Gender, u.address, u.IsActive, u.CreatedAt, ep.PreferredDoctorID
        FROM ElderProfiles ep INNER JOIN Users u ON u.UserID = ep.ElderID
        WHERE ep.ElderID = :patient_id AND ep.PreferredDoctorID = :doctor_id
         AND u.RoleID = 5";

    $stmtPatient = $pdo->prepare($sqlPatient);
    $stmtPatient->execute([
        ':patient_id' => $patientId,
        ':doctor_id' => $doctorId
    ]);
    $patient = $stmtPatient->fetch();

    if (!$patient) {
        header("Location: patients.php");
        exit;
    }

    $sqlCaregivers = "
        SELECT  cr.RelationshipID, cr.RelationshipType, cr.IsPrimary,
            u.UserID AS CaregiverID, u.FullName, u.Email, u.Phone,
            u.Gender, u.IsActive FROM CareRelationships cr
        INNER JOIN Users u ON u.UserID = cr.CaregiverID
        WHERE cr.ElderID = :patient_id AND u.RoleID = 4
        ORDER BY cr.IsPrimary DESC, u.FullName ASC";

    $stmtCaregivers = $pdo->prepare($sqlCaregivers);
    $stmtCaregivers->execute([
        ':patient_id' => $patientId
    ]);
    $caregivers = $stmtCaregivers->fetchAll();

} catch (PDOException $e) {
    error_log("Patient data page DB error: " . $e->getMessage(), 0);
    header("Location: patients.php");
    exit;
}

$apiUrl = "http://159.65.158.217:8000/api/v1/caregiver/elder/medical-profile/" . urlencode($patientId);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_HTTPHEADER => [
        "accept: application/json"
    ],
]);

$apiResponse = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curlError) {
    $medicalProfileError = "Unable to load medical profile right now.";
} elseif ($httpCode === 200 && !empty($apiResponse)) {
    $decoded = json_decode($apiResponse, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $medicalProfile = $decoded;
    } else {
        $medicalProfileError = "Medical profile response format is invalid.";
    }
} else {
    $medicalProfileError = "Medical profile not available.";
}

function formatDateValue($dateValue)
{
    if (empty($dateValue)) {
        return '-';
    }

    $timestamp = strtotime($dateValue);
    if ($timestamp === false) {
        return htmlspecialchars($dateValue);
    }

    return date('Y-m-d', $timestamp);
}

function safeValue($value)
{
    if (!isset($value) || $value === null || trim((string) $value) === '') {
        return '-';
    }
    return htmlspecialchars((string) $value);
}
$vitalsData = [];

$vitalsApi = "http://159.65.158.217:8000/api/v1/caregiver/vitals/elder/" . urlencode($patientId) . "/latest?limit_per_type=3";

$ch3 = curl_init();
curl_setopt_array($ch3, [
    CURLOPT_URL => $vitalsApi,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["accept: application/json"],
]);

$response3 = curl_exec($ch3);
$httpCode3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
curl_close($ch3);

if ($httpCode3 == 200) {
    $decoded3 = json_decode($response3, true);
    if (isset($decoded3['categories'])) {
        $vitalsData = $decoded3['categories'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Data Trustcare</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #2E7D7A;
            --background: #F6F7F3;
            --card-bg: #FFFFFF;
            --primary-text: #243333;
            --description-text: #6F7F7D;
            --text-shade: #7C8B89;
            --border: #E5ECE9;
            --soft-bg: #FAFBFB;
            --danger: #C62828;
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

        .page-header {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .page-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 15px;
            color: var(--description-text);
            line-height: 1.7;
        }

        .top-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0;
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }

        .top-actions a {
            padding: 12px 18px;
            text-decoration: none;
            color: var(--primary-text);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            background: #fff;
            transition: 0.2s ease;
        }

        .top-actions a:hover {
            background: #F4F7F6;
        }

        .top-actions .primary-btn {
            background: var(--primary);
            color: #fff;
        }

        .top-actions .primary-btn:hover {
            background: #276c69;
        }

        .top-actions .divider {
            width: 1px;
            height: 42px;
            background: var(--border);
        }

        .section-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 18px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .section-head {
            padding: 20px 22px;
            border-bottom: 1px solid var(--border);
        }

        .section-head h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 22px;
            font-weight: 700;
        }

        .section-head p {
            margin-top: 6px;
            color: var(--description-text);
            font-size: 14px;
            line-height: 1.6;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
            padding: 22px;
        }

        .detail-box {
            background: var(--soft-bg);
            border: 1px solid #EEF2F1;
            border-radius: 14px;
            padding: 16px;
        }

        .detail-box span {
            display: block;
            font-size: 13px;
            color: var(--text-shade);
            margin-bottom: 8px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .detail-box strong {
            display: block;
            font-size: 16px;
            color: var(--primary-text);
            line-height: 1.6;
            word-break: break-word;
        }

        .medical-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
            padding: 22px;
        }

        .medical-box {
            background: var(--soft-bg);
            border: 1px solid #EEF2F1;
            border-radius: 14px;
            padding: 18px;
        }

        .medical-box.full {
            grid-column: 1 / -1;
        }

        .medical-box h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-shade);
            margin-bottom: 10px;
        }


        .medical-box p {
            font-size: 15px;
            color: var(--primary-text);
            line-height: 1.8;
            word-break: break-word;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* ===== VITALS GRID UI ===== */
        .vitals-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 20px;
        }

        .vital-card {
            background: linear-gradient(135deg, #F8FAF9, #FFFFFF);
            border: 1px solid #E5ECE9;
            border-radius: 16px;
            padding: 18px;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .vital-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06);
        }

        .vital-title {
            font-size: 13px;
            font-weight: 600;
            color: #7C8B89;
            margin-bottom: 10px;
        }

        .vital-value {
            font-size: 28px;
            font-weight: 700;
            color: #2E7D7A;
        }

        .vital-unit {
            font-size: 14px;
            color: #7C8B89;
            margin-left: 5px;
        }

        .vital-time {
            font-size: 12px;
            color: #9AA7A5;
            margin-top: 6px;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #7C8B89;
        }

        .empty-state .icon {
            font-size: 42px;
            margin-bottom: 10px;
        }

        thead th {
            background: #F8FAF9;
            padding: 16px 18px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        tbody td {
            padding: 16px 18px;
            border-bottom: 1px solid #EEF2F1;
            font-size: 15px;
            vertical-align: middle;
        }

        .badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .badge-green {
            background: #E4F4EE;
            color: #1B6E4B;
        }

        .badge-gray {
            background: #EDF1F0;
            color: #51625F;
        }

        .message-box {
            padding: 20px 22px;
            color: var(--description-text);
            font-size: 15px;
            line-height: 1.7;
        }

        .error-box {
            padding: 16px 18px;
            margin: 22px;
            border: 1px solid #F0D0D0;
            background: #FFF5F5;
            color: var(--danger);
            border-radius: 12px;
            font-size: 14px;
        }

        @media (max-width: 1100px) {

            .details-grid,
            .medical-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 980px) {
            .layout {
                flex-direction: column;
            }

            .content {
                padding: 18px;
            }

            .top-actions {
                width: 100%;
            }
        }

        @media (max-width: 700px) {
            .top-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .top-actions .divider {
                width: 100%;
                height: 1px;
            }

            .top-actions a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="layout">
        <?php include 'include/sidebar.php'; ?>

        <main class="content">
            <div class="page-header">
                <div class="page-header-top">
                    <div>
                        <h1 class="page-title">Patient Data</h1>
                        <p class="page-subtitle">
                            View patient profile, caregiver details, and medical profile information.
                        </p>
                    </div>

                    <div class="top-actions">
                        <a href="patients.php" class="back-btn">
                            ← Back
                        </a>
                        <a href="elder_medications.php?elder_id=<?php echo urlencode($patientId); ?>"
                            class="medicine-btn">
                            View Medicines
                        </a>

                    </div>

                    <div class="section-card">
                        <div class="section-head">
                            <h2>Patient Information</h2>
                            <p>Basic details of the selected patient.</p>
                        </div>

                        <div class="details-grid">
                            <div class="detail-box">
                                <span>Patient ID</span>
                                <strong><?php echo htmlspecialchars($patient['UserID']); ?></strong>
                            </div>

                            <div class="detail-box">
                                <span>Full Name</span>
                                <strong><?php echo htmlspecialchars($patient['FullName']); ?></strong>
                            </div>

                            <div class="detail-box">
                                <span>Phone</span>
                                <strong><?php echo htmlspecialchars($patient['Phone']); ?></strong>
                            </div>

                            <div class="detail-box">
                                <span>Email</span>
                                <strong><?php echo safeValue($patient['Email'] ?? null); ?></strong>
                            </div>

                            <div class="detail-box">
                                <span>Date of Birth</span>
                                <strong><?php echo formatDateValue($patient['DateOfBirth']); ?></strong>
                            </div>

                            <div class="detail-box">
                                <span>Gender</span>
                                <strong><?php echo safeValue($patient['Gender'] ?? null); ?></strong>
                            </div>

                            <div class="detail-box">
                                <span>Address</span>
                                <strong><?php echo safeValue($patient['address'] ?? null); ?></strong>
                            </div>

                            <div class="detail-box">
                                <span>Status</span>
                                <strong>
                                    <?php if ((int) $patient['IsActive'] === 1): ?>
                                        <span class="badge badge-green">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-gray">Inactive</span>
                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                    <div class="section-card">
                        <div class="section-head">
                            <h2>Latest Vitals</h2>
                            <p>Recent health measurements of the patient</p>
                        </div>

                        <div class="section-body">

                            <?php if (!empty($vitalsData)) { ?>

                                <div class="vitals-grid">

                                    <?php foreach ($vitalsData as $vital) {
                                        $latest = $vital['last'][0] ?? null;
                                        ?>

                                        <div class="vital-card">

                                            <div class="vital-title">
                                                <?php echo safeValue($vital['vital_name']); ?>
                                            </div>

                                            <?php if ($latest) { ?>

                                                <div class="vital-value">
                                                    <?php echo safeValue($latest['value']); ?>
                                                    <span class="vital-unit">
                                                        <?php echo safeValue($vital['unit']); ?>
                                                    </span>
                                                </div>

                                                <div class="vital-time">
                                                    <?php echo date('M d, H:i', strtotime($latest['recorded_at'])); ?>
                                                </div>

                                            <?php } else { ?>

                                                <div class="vital-time">No records</div>

                                            <?php } ?>

                                        </div>

                                    <?php } ?>

                                </div>

                            <?php } else { ?>

                                <div class="empty-state">
                                    <div class="icon"></div>
                                    <h3>No Vitals Data</h3>
                                    <p>This patient has no recorded vitals yet.</p>
                                </div>

                            <?php } ?>

                        </div>
                    </div>
                    <div class="section-card">
                        <div class="section-head">
                            <h2>Medical Profile</h2>
                        </div>

                        <?php if ($medicalProfileError !== ''): ?>
                            <div class="error-box"><?php echo htmlspecialchars($medicalProfileError); ?></div>
                        <?php elseif ($medicalProfile): ?>
                            <div class="medical-grid">
                                <div class="medical-box">
                                    <h3>Blood Type</h3>
                                    <p><?php echo safeValue($medicalProfile['BloodType'] ?? null); ?></p>
                                </div>

                                <div class="medical-box">
                                    <h3>Doctor Name</h3>
                                    <p><?php echo safeValue($medicalProfile['DoctorName'] ?? null); ?></p>
                                </div>

                                <div class="medical-box full">
                                    <h3>Allergies</h3>
                                    <p><?php echo safeValue($medicalProfile['Allergies'] ?? null); ?></p>
                                </div>

                                <div class="medical-box full">
                                    <h3>Chronic Conditions</h3>
                                    <p><?php echo safeValue($medicalProfile['ChronicConditions'] ?? null); ?></p>
                                </div>

                                <div class="medical-box full">
                                    <h3>Past Surgeries</h3>
                                    <p><?php echo safeValue($medicalProfile['PastSurgeries'] ?? null); ?></p>
                                </div>

                                <div class="medical-box full">
                                    <h3>Emergency Notes</h3>
                                    <p><?php echo safeValue($medicalProfile['EmergencyNotes'] ?? null); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="message-box">No medical profile data found.</div>
                        <?php endif; ?>
                    </div>

                    <div class="section-card">
                        <div class="section-head">
                            <h2>Caregiver Information</h2>
                            <p>Caregiver records linked to this patient.</p>
                        </div>

                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Caregiver ID</th>
                                        <th>Full Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Relationship</th>
                                        <th>Primary</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($caregivers)): ?>
                                        <?php foreach ($caregivers as $caregiver): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($caregiver['CaregiverID']); ?></td>
                                                <td><?php echo htmlspecialchars($caregiver['FullName']); ?></td>
                                                <td><?php echo htmlspecialchars($caregiver['Phone']); ?></td>
                                                <td><?php echo safeValue($caregiver['Email'] ?? null); ?></td>
                                                <td><?php echo safeValue($caregiver['RelationshipType'] ?? null); ?></td>
                                                <td>
                                                    <?php if ((int) $caregiver['IsPrimary'] === 1): ?>
                                                        <span class="badge badge-green">Yes</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-gray">No</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ((int) $caregiver['IsActive'] === 1): ?>
                                                        <span class="badge badge-green">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-gray">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="message-box">No caregiver records found.</td>
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