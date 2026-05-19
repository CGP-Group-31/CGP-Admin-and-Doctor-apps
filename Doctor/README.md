# TrustCare Doctor Portal

TrustCare Doctor Portal is the doctor-facing web application for the TrustCare elderly care platform. It allows doctors to securely log in, view their assigned patients, inspect patient health information, review caregiver links, check latest vitals, and view medication schedules and daily adherence reports.

The application is built with PHP and Microsoft SQL Server, with selected medical and medication data loaded from TrustCare backend API endpoints.

## Main Purpose

The doctor app gives registered doctors a focused workspace to:

- Access only the patients assigned to them.
- View patient demographics and contact details.
- Review linked caregiver information.
- Inspect medical profile data such as allergies, chronic conditions, surgeries, blood type, and emergency notes.
- View latest patient vital records.
- Review medicine lists and daily medication adherence.
- See doctor profile and professional information.

## Technology Stack

- Backend: PHP
- Database: Microsoft SQL Server
- Database access: PDO with `sqlsrv`
- External API access: PHP cURL
- Frontend: HTML5 and CSS3
- Styling: Inline page styles and shared includes
- Fonts: Google Fonts, Poppins and Roboto
- Runtime: XAMPP / Apache with PHP
- Authentication: PHP sessions
- Password handling: `password_verify()`

## Core Features

### 1. Doctor Login

`index.php` is the login page for doctors.

Login behavior includes:

- Doctors log in with email and password.
- The app checks the `Users` table and joins the `Doctor` table.
- Only users with `RoleID = 2` can access the doctor portal.
- Inactive accounts are blocked.
- Passwords are verified with `password_verify()`.
- Session ID is regenerated after successful login.
- Doctor details are saved in session variables for use across the portal.
- `LastLogin` is updated after successful login.

Stored session values include:

- Doctor login status
- Doctor ID
- Doctor name
- Doctor email
- Phone number
- License number
- Specialization
- Hospital

### 2. Dashboard

`dashboard.php` is the main doctor dashboard.

Dashboard features include:

- Shows total patients assigned to the logged-in doctor.
- Shows total vital records/reports for assigned patients.
- Displays a recent patient list.
- Uses the logged-in doctor ID to restrict dashboard data.
- Shows doctor name and specialization in the top bar.

The dashboard only counts patients where the doctor is selected as the preferred doctor in `ElderProfiles`.

### 3. Patient List

`patients.php` lists all active elder patients assigned to the doctor.

Patient list features include:

- Shows patient ID, full name, phone, email, and gender.
- Filters patients by the logged-in doctor's ID.
- Only active elder users are shown.
- Supports search by patient name or mobile number.
- Provides a direct link to each patient detail page.

Important access rule:

- A doctor can only see patients where `ElderProfiles.PreferredDoctorID` matches their own doctor ID.

### 4. Patient Data

`patient_data.php` displays detailed information for a selected patient.

Patient data sections include:

- Basic patient information
- Date of birth, gender, address, and account status
- Linked caregiver details
- Latest vital measurements
- Medical profile details
- Shortcut to medicine/adherence page

Access protection:

- The page checks the selected patient ID.
- If the patient is not assigned to the logged-in doctor, the doctor is redirected back to `patients.php`.

### 5. Caregiver Information

Inside `patient_data.php`, doctors can view caregivers linked to the selected elder.

Caregiver details include:

- Caregiver ID
- Full name
- Phone
- Email
- Relationship type
- Primary caregiver status
- Active/inactive status

This information comes from the `CareRelationships` table joined with the `Users` table.

### 6. Medical Profile

`patient_data.php` loads the elder medical profile from an external TrustCare API.

API endpoint used:

```text
http://159.65.158.217:8000/api/v1/caregiver/elder/medical-profile/{elder_id}
```

Medical profile fields include:

- Blood type
- Doctor name
- Allergies
- Chronic conditions
- Past surgeries
- Emergency notes

The page handles API errors by showing a clear message when the medical profile cannot be loaded or is unavailable.

### 7. Latest Vitals

`patient_data.php` also loads recent patient vitals from a TrustCare API endpoint.

API endpoint used:

```text
http://159.65.158.217:8000/api/v1/caregiver/vitals/elder/{elder_id}/latest?limit_per_type=3
```

Vitals behavior includes:

- Groups vitals by category.
- Shows the latest value for each vital type.
- Displays the unit and recorded time.
- Shows an empty state when no vitals are available.

### 8. Medication and Daily Adherence

`elder_medications.php` displays medication information for a selected elder.

Medication features include:

- Shows the elder's full medicine list.
- Shows dosage, instructions, repeat days, start date, end date, and scheduled times.
- Shows daily medication adherence for a selected date.
- Supports date filtering with an HTML date input.
- Displays status badges for medication states such as taken, missed, and skipped.

Medication API endpoint:

```text
http://159.65.158.217:8000/api/v1/caregiver/medication/elder/{elder_id}
```

Daily adherence API endpoint:

```text
http://159.65.158.217:8000/api/v1/daily-reports/elder/{elder_id}/medication?date={selected_date}
```

### 9. Doctor Profile

`profile.php` displays the logged-in doctor's account and professional information.

Profile fields include:

- Full name
- Email
- Phone
- Gender
- Date of birth
- Address
- Specialization
- Hospital
- License number

Profile data is loaded from the `Users` and `Doctor` tables using the logged-in doctor ID.

### 10. Logout

`logout.php` securely ends the doctor session.

Logout behavior includes:

- Clears the `$_SESSION` array.
- Removes the session cookie when session cookies are enabled.
- Destroys the session.
- Redirects the user to `index.php`.

## User Role

The Doctor Portal is specifically for doctor accounts.

| Role | RoleID | Description |
| --- | ---: | --- |
| Doctor | 2 | Medical professional with access to assigned elder patients |
| Caregiver | 4 | Linked caregiver records shown inside patient details |
| Elder | 5 | Patient records assigned to doctors |

## Important Database Tables

The doctor portal uses these SQL Server tables:

- `Users`
- `Doctor`
- `ElderProfiles`
- `VitalRecords`
- `CareRelationships`

The portal also displays data from external TrustCare API responses for:

- Medical profiles
- Latest vitals
- Medication lists
- Daily medication adherence reports

## How the App Works

1. The doctor opens `index.php`.
2. If already logged in, the doctor is redirected to `dashboard.php`.
3. If not logged in, the doctor enters email and password.
4. The app checks the doctor account in `Users` and `Doctor`.
5. After successful login, doctor details are stored in the session.
6. Protected pages check `$_SESSION['doctor_logged_in']`.
7. The dashboard shows assigned patient and report counts.
8. The Patients page lists only elders assigned to the logged-in doctor.
9. The Patient Data page verifies the patient belongs to the doctor before showing details.
10. External APIs provide medical profile, latest vitals, medication, and adherence data.
11. Logout clears the session and returns the user to login.

## Special Features

- Doctor-only secure login.
- Active account check before portal access.
- Session regeneration after successful login.
- Doctor-specific patient filtering.
- Searchable patient list.
- Patient access validation to prevent viewing unassigned patients.
- Medical profile loading through external API.
- Latest vitals loaded through external API.
- Daily medication adherence by selected date.
- Medicine schedule display with time chips.
- Caregiver relationship visibility inside patient details.
- Clean healthcare-focused dashboard layout.
- Responsive sidebar and page layouts.

## Setup Instructions

### 1. Requirements

Install the following:

- XAMPP or another Apache/PHP server
- PHP with PDO enabled
- Microsoft SQL Server PDO driver for PHP (`sqlsrv`)
- PHP cURL extension enabled
- Access to the TrustCare SQL Server database
- Access to the TrustCare backend API server

### 2. Place the Project

Place the `Doctor` folder inside your XAMPP web directory:

```text
C:\xampp\htdocs\CGP\Doctor
```

### 3. Configure Database

Database connection settings are stored in:

```text
include/db.php
```

The connection file uses:

- SQL Server host
- Port `1433`
- Database name
- SQL Server username
- SQL Server password

For production, database credentials should be moved out of source code and loaded from environment variables or a protected configuration file.

### 4. Run the App

Start Apache in XAMPP, then open:

```text
http://localhost/CGP/Doctor/
```

The app opens the doctor login page unless a valid doctor session already exists.

## Page Reference

| Page | Purpose |
| --- | --- |
| `index.php` | Doctor login page |
| `dashboard.php` | Doctor dashboard with assigned patient and report counts |
| `patients.php` | Searchable list of assigned patients |
| `patient_data.php` | Patient profile, caregivers, medical profile, and vitals |
| `elder_medications.php` | Medicine list and daily medication adherence |
| `profile.php` | Logged-in doctor profile |
| `logout.php` | Clears the doctor session |
| `include/db.php` | SQL Server database connection |
| `include/header.php` | Shared document header and dashboard styles |
| `include/sidebar.php` | Shared doctor sidebar navigation |

## Security Notes

- Protected pages require `$_SESSION['doctor_logged_in']`.
- Login only allows active users with `RoleID = 2`.
- Password verification uses `password_verify()`.
- Session ID is regenerated after successful login.
- Patient pages verify that the selected elder is assigned to the logged-in doctor.
- SQL queries use prepared statements.
- Database credentials are currently stored directly in `include/db.php`; this should be changed before production deployment.
- External API calls use plain HTTP. For production, HTTPS should be used.
- API responses should be validated carefully before display.

## UI and Design

The Doctor Portal uses a clean healthcare dashboard style:

- Left sidebar navigation
- Doctor identity shown in the sidebar
- Soft TrustCare green color theme
- Card-based dashboard statistics
- Patient tables with search and action buttons
- Profile information cards
- Vital cards with value, unit, and recorded time
- Medication status badges
- Responsive layout for smaller screens

## Future Improvements

- Move database and API configuration to environment variables.
- Use HTTPS for all API requests.
- Add API authentication tokens if required by the backend.
- Add loading states for API-based medical, vital, and medication data.
- Add pagination for large patient lists.
- Add richer trend charts for vitals.
- Add appointment management for doctors.
- Add clinical notes or prescription update workflows.
- Add audit logging for patient record access.
- Centralize CSS into shared stylesheet files.

## Project Summary

TrustCare Doctor Portal helps doctors monitor assigned elder patients from a secure web interface. It combines database-driven patient access, caregiver relationship visibility, medical profile data, latest vitals, and medication adherence reports so doctors can quickly understand each patient's care context and health status.
