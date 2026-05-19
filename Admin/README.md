# TrustCare Admin Portal

TrustCare Admin Portal is the administration web application for the TrustCare elderly care platform. It allows administrators to monitor system activity, manage care users, review health and AI signals, track emergency SOS alerts, and respond to user complaints from one secure dashboard.

The application is built as a PHP web app connected to a Microsoft SQL Server database. It is designed to run locally through XAMPP or a PHP-enabled web server.

## Main Purpose

The admin app gives TrustCare staff a central place to:

- View real-time operational statistics for caregivers, elders, SOS alerts, active users, missed medication, and high-risk health records.
- Manage users such as administrators, doctors, caregivers, and elders.
- Review caregiver-to-elder relationships.
- Monitor emergency SOS alerts with latest available GPS location data.
- View AI wellness check-ins, mood signals, safety flags, and generated care reports.
- Handle complaints and update complaint statuses.
- Keep the care platform organized, safer, and easier to operate.

## Technology Stack

- Backend: PHP
- Database: Microsoft SQL Server
- Database access: PDO with `sqlsrv`
- Frontend: HTML5, CSS3, JavaScript
- Styling: Custom CSS in `assets/theme.css`
- Icons: Font Awesome CDN
- Charts: Chart.js CDN
- Runtime: XAMPP / Apache with PHP
- Authentication: PHP sessions
- Password handling: `password_hash()` and `password_verify()`

## Core Features

### 1. Secure Admin Login

Admins sign in through `Login.php` using username/full name or email and password. After successful login, the app stores the admin ID and admin name in PHP session variables.

Important behavior:

- Only users with `RoleID = 1` can log in as administrators.
- Passwords are checked using `password_verify()`.
- Legacy plain-text password comparison is also present for older records.
- Unauthenticated users are redirected to `Login.php`.
- `logout.php` clears the session and redirects users back to login.

### 2. Dashboard Overview

`Dashboard.php` provides the main system summary.

Dashboard metrics include:

- Active caregivers
- Active elders
- Users active today
- SOS alerts triggered today
- Missed medication records for today
- High-risk elder count based on vital records
- SOS emergency trend chart for the last 7 days

Special dashboard behavior:

- Uses SQL Server date functions such as `GETDATE()` and `DATEADD()`.
- Uses Chart.js to draw the emergency trend graph.
- Uses animated counters through `assets/app.js`.
- Cards link directly to related admin modules.

### 3. Caregiver Management

`Caregivers.php` lists all caregiver accounts.

Caregiver features include:

- View caregiver ID, name, email, phone, active status, and assigned elder count.
- Search caregivers by table content.
- Open detailed caregiver profile pages.
- View the number of elders assigned to each caregiver.
- Delete caregiver records from the profile view, including related medication, login, device, SOS, and relationship records.

### 4. Elder Management

`Elders.php` lists elder users in the system.

Elder features include:

- View elder ID, name, age, assigned caregiver, and health risk status.
- Search elders by name, ID, or caregiver.
- Display risk status using recent vital records.
- Open detailed elder profile pages.
- View caregiver assignment information.
- Delete elder records and related appointments, medications, adherence records, vitals, SOS logs, devices, login records, and caregiver relationships.

### 5. Doctor Management

`Doctors.php`, `DoctorCreate.php`, and `DoctorView.php` support doctor administration.

Doctor features include:

- List registered doctors.
- Create new doctor accounts.
- Store doctor credentials such as license number, specialization, and hospital.
- Create linked records in both `Users` and `Doctor` tables.
- Hash doctor passwords before saving.
- Prevent duplicate doctor email registration.
- View doctor profile information.
- Delete doctor accounts and related appointment/login records.

### 6. Admin Management

`Admins.php` and `AdminCreate.php` allow system administrator management.

Admin features include:

- View existing administrator accounts.
- Create new administrator accounts.
- Save admins as `RoleID = 1`.
- Hash temporary passwords.
- Prevent duplicate admin email registration.
- Redirect back to the admin list after successful creation.

### 7. Caregiver Links

`CaregiverLinks.php` and `CaregiverLinksView.php` show relationships between elders and caregivers.

Caregiver relationship features include:

- View elder name, caregiver name, relationship type, primary/secondary status, and date linked.
- Open a relationship detail page.
- Delete relationship records.
- Distinguish primary caregivers from secondary caregivers.

### 8. Health and AI Insights

`HealthAI.php` provides wellness and AI monitoring for elder check-ins.

Health AI metrics include:

- Check-ins today
- Check-ins during the last 7 days
- High-risk check-ins during the last 7 days
- Missed or failed AI check-in runs
- Safety flags from chat messages
- Total generated care reports
- Care reports generated during the last 7 days
- Dominant mood from recent elder forms

Health AI tables include:

- Recent wellness check-ins
- Elder mood, stress, loneliness, pain areas, activities, and calculated risk level
- Recent AI check-in runs
- Check-in status, planned time, completed time, detected mood, and notes

Special Health AI behavior:

- Calculates risk scores from form answers such as bad overall day, stress, loneliness, low energy, low appetite, and sad mood.
- Uses SQL Server `CROSS APPLY`, `OUTER APPLY`, and `STRING_AGG()` to prepare summarized insights.
- Shows clear risk badges for low, medium, and high-risk check-ins.

### 9. SOS and Emergency Monitoring

`SOS.php` and `SOSView.php` support emergency review.

SOS features include:

- List emergency SOS records by newest first.
- Show elder name and triggered time.
- Fetch latest known latitude and longitude from `LocationTrack`.
- Link GPS coordinates to Google Maps.
- Open detailed SOS records.

This module helps admins quickly inspect emergency events and locate elders using the latest available location data.

### 10. Complaints and Feedback

`Complains.php` and `ComplainRespond.php` allow administrators to review and respond to complaints.

Complaint features include:

- View complaint ID, complainant, subject, reported date, and status.
- Open each complaint response screen.
- Update complaint status.
- Display statuses such as pending, under review, and resolved with visual badges.

## User Roles

The app depends on role IDs stored in the `Users` table.

| Role | RoleID | Description |
| --- | ---: | --- |
| Admin | 1 | System administrator with access to the admin portal |
| Doctor | 2 | Medical professional account |
| Caregiver | 4 | Caregiver assigned to elders |
| Elder | Other role IDs | Elder users monitored by the care system |

## Important Database Tables

The admin portal reads from and writes to several SQL Server tables:

- `Users`
- `Doctor`
- `CareRelationships`
- `UserLogins`
- `UserDevices`
- `SOSLogs`
- `LocationTrack`
- `MedicationAdherence`
- `MedicationSchedules`
- `Medications`
- `VitalRecords`
- `Appointments`
- `Complaints`
- `ElderForm`
- `ElderFormInPain`
- `ElderFormActivity`
- `CheckInRuns`
- `ChatMessages`
- `CareReports`
- `MoodTypes`

## How the App Works

1. The user opens `index.php`.
2. `index.php` checks whether an admin session exists.
3. If logged in, the admin is redirected to `Dashboard.php`.
4. If not logged in, the user is redirected to `Login.php`.
5. After login, every protected page checks `$_SESSION['admin_id']`.
6. Pages connect to SQL Server through `include/db.php`.
7. Data is displayed in dashboards, cards, tables, badges, charts, and detail views.
8. Admin actions such as creating doctors/admins, deleting users, deleting relationships, and updating complaints are written back to the database.

## Special Features

- Real-time dashboard summary cards.
- 7-day SOS trend visualization with Chart.js.
- Health AI insight page with mood, risk, safety flag, and check-in analytics.
- Automatic risk score classification for elder wellness check-ins.
- Google Maps links for emergency GPS coordinates.
- Caregiver and elder assignment visibility.
- Admin and doctor account creation with hashed passwords.
- Table search filtering using JavaScript.
- Animated page reveal effects and animated numeric counters.
- Responsive sidebar layout for smaller screens.
- Unified TrustCare visual theme with custom CSS variables.

## Setup Instructions

### 1. Requirements

Install the following:

- XAMPP or another Apache/PHP server
- PHP with PDO enabled
- Microsoft SQL Server PDO driver for PHP (`sqlsrv`)
- Access to the TrustCare SQL Server database

### 2. Place the Project

Place the `Admin` folder inside your XAMPP web directory:

```text
C:\xampp\htdocs\CGP\Admin
```

### 3. Configure Database

Database connection settings are stored in:

```text
include/db.php
```

The file creates a PDO connection using:

- SQL Server host
- Port `1433`
- Database name
- SQL Server username
- SQL Server password

For production, move credentials out of the source code and load them from environment variables or a protected configuration file.

### 4. Run the App

Start Apache in XAMPP, then open:

```text
http://localhost/CGP/Admin/
```

The app will redirect to login or dashboard depending on the current session.

## Page Reference

| Page | Purpose |
| --- | --- |
| `index.php` | Entry point and login/dashboard redirect |
| `Login.php` | Admin login screen |
| `logout.php` | Ends the admin session |
| `Dashboard.php` | Main dashboard and system metrics |
| `Caregivers.php` | Caregiver directory |
| `CaregiversView.php` | Caregiver profile and delete action |
| `Elders.php` | Elder directory |
| `EldersView.php` | Elder profile, caregiver assignment data, and delete action |
| `Doctors.php` | Doctor directory |
| `DoctorCreate.php` | Create doctor accounts |
| `DoctorView.php` | Doctor profile and delete action |
| `Admins.php` | Administrator directory |
| `AdminCreate.php` | Create administrator accounts |
| `CaregiverLinks.php` | Elder-caregiver relationship list |
| `CaregiverLinksView.php` | Relationship details and delete action |
| `HealthAI.php` | Health, wellness, AI check-in, and safety analytics |
| `SOS.php` | Emergency SOS alert list |
| `SOSView.php` | Detailed SOS alert and location information |
| `Complains.php` | Complaint list |
| `ComplainRespond.php` | Complaint review and status update |
| `Location.php` | Redirects to dashboard |
| `LocationView.php` | Redirects to dashboard |

## Security Notes

- Admin pages are protected by session checks.
- New admin and doctor passwords are hashed before being stored.
- SQL queries use prepared statements for form inserts, login, deletes, and updates in key areas.
- Some old login records may still use plain text password comparison for backward compatibility. This should be migrated to hashed passwords only.
- Database credentials are currently stored directly in `include/db.php`. This should be changed before production deployment.
- Hard delete operations remove related records from multiple tables. Use carefully and ensure backups exist.

## UI and Design

The interface uses a clean TrustCare theme focused on healthcare administration:

- Fixed sidebar navigation
- Soft green care-focused color palette
- Status badges for active, inactive, stable, high risk, pending, under review, and resolved states
- Responsive layout for desktop and smaller screens
- Searchable tables
- Card-based dashboard metrics
- Consistent buttons, forms, tables, detail cards, and alert messages

## Future Improvements

- Move database credentials to environment variables.
- Remove legacy plain-text password checking.
- Add role-based permission levels for different admin staff.
- Add soft delete support instead of hard deleting users.
- Add audit logs for admin actions.
- Add pagination for large tables.
- Add export options for reports, SOS logs, and complaints.
- Add CSRF protection to forms and delete actions.
- Add automated tests for database operations and page access control.

## Project Summary

TrustCare Admin Portal is the control center for managing the elderly care platform. It connects user management, caregiver assignments, emergency tracking, complaint handling, health monitoring, and AI wellness insights into one admin application. The app helps administrators quickly understand what is happening in the care system and take action when elders, caregivers, doctors, or support requests need attention.
