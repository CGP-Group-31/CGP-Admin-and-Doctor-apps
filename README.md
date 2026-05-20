# TrustCare Admin and Doctor Web Portals

TrustCare is an elderly care management platform that provides separate web portals for administrators and doctors. These portals help care teams manage users, monitor elder health information, review emergencies, track caregiver relationships, respond to complaints, and support medical follow-up through a browser-based interface.

The system is divided into two main applications:

- `Admin` app for system administrators and care operation staff.
- `Doctor` app for doctors who monitor assigned elder patients.

## Main Purpose

The purpose of these apps is to make elderly care operations easier, faster, and safer. The Admin app gives staff a complete view of the platform and its users. The Doctor app gives doctors a focused view of their assigned patients, including medical profiles, vitals, caregivers, and medicines.

Together, both apps support:

- User and role management.
- Elder health monitoring.
- Emergency response review.
- Caregiver assignment visibility.
- Doctor-patient care coordination.
- Complaint handling.
- Medication and adherence review.
- AI-based wellness insight review.

## Technology Used

- PHP for backend page logic.
- HTML5 for page structure.
- CSS3 for custom layouts and responsive styling.
- JavaScript for table filtering, animations, and UI behavior.
- PHP sessions for login and protected pages.
- PHP password functions for authentication.
- PHP cURL for external API requests in the Doctor app.
- Chart.js for dashboard graph visualization in the Admin app.
- Font Awesome for Admin app icons.
- Google Fonts for Doctor app typography.
- Apache/XAMPP for local hosting.

## Admin App Overview

The Admin app is the main control center of TrustCare. It is designed for administrators who need to monitor the full platform, manage important users, and respond to issues.

Admins can manage:

- Administrators.
- Doctors.
- Caregivers.
- Elders.
- Caregiver and elder relationships.
- SOS emergency alerts.
- Complaints.
- Health and AI wellness data.

## Admin App Features

### Secure Admin Login

The Admin app has a login page for administrator access. Only admin users can enter the portal. After successful login, the system stores the admin session and protects the internal pages from unauthenticated access.

Admin login includes:

- Username/email based login.
- Password verification.
- Session-based access control.
- Redirect to dashboard after login.
- Logout function to clear the active session.

### Admin Dashboard

The dashboard gives administrators a quick overview of the TrustCare system.

Dashboard cards show:

- Total active caregivers.
- Total active elders.
- Active users for the current day.
- SOS alerts triggered today.
- Missed medication count.
- High-risk elder count.

The dashboard also includes a 7-day SOS emergency trend chart. This helps administrators quickly understand whether emergency activity is increasing or decreasing.

### Caregiver Management

The caregiver section lists caregiver accounts in the system.

Caregiver management includes:

- Full name.
- Email.
- Phone number.
- Active/inactive status.
- Number of assigned elders.
- Search support.
- Profile viewing.
- Caregiver deactivate actions from the profile page.

This helps admins monitor who is responsible for elder care and whether caregivers are active in the platform.

### Elder Management

The elder section lists elder users and their care status.

Elder management includes:

- Full name.
- Age.
- Risk status.
- Assigned caregiver.
- Search support.
- Elder profile viewing.
- Elder deactivate actions from the profile page.

The elder list shows whether an elder is stable or high-risk based on recent health/vital information. This makes it easier for admins to identify users who may need attention.

### Doctor Management

The doctor section allows administrators to view and manage doctors.

Doctor management includes:

- Doctor list.
- Doctor profile view.
- New doctor account creation.
- Doctor license number.
- Specialization.
- Hospital or clinic.
- Doctor contact information.
- Password creation for doctor access.
- Doctor deactivate actions from the profile page.

This allows the care platform to keep doctor records organized and linked to patient care.

### Administrator Management

The admin management section allows existing administrators to create and view admin accounts.

Admin management includes:

- Admin account list.
- New admin account creation.
- Admin name.
- Email.
- Phone.
- Temporary password setup.

This is useful when new staff members need access to the TrustCare Admin Portal.

### Caregiver Link Management

Caregiver links show the relationship between elders and caregivers.

Caregiver link details include:

- Elder name.
- Caregiver name.
- Relationship type.
- Primary or secondary caregiver status.
- Date linked.
- Relationship detail view.
- Delete relationship action.

This feature helps admins understand who is responsible for each elder and whether the caregiver is primary or secondary.

### Health and AI Insights

The Health and AI page is one of the most important special features in the Admin app. It summarizes wellness check-ins, AI activity, and risk signals.

Health and AI metrics include:

- Check-ins today.
- Check-ins in the last 7 days.
- High-risk check-ins in the last 7 days.
- Missed or failed AI check-ins.
- Safety flags from chat messages.
- Total care reports.
- Care reports generated in the last 7 days.
- Dominant mood from recent elder check-ins.

The page also shows recent wellness check-ins with:

- Elder name.
- Date.
- Mood.
- Stress level.
- Loneliness level.
- Pain areas.
- Activities.
- Risk level.

Risk levels are shown as low, medium, or high. This helps administrators notice elders who may be emotionally, mentally, or physically at risk.

### AI Check-In Runs

The Admin app displays recent AI check-in runs.

AI check-in data includes:

- Run ID.
- Elder name.
- Check-in status.
- Planned time.
- Completed time.
- Detected mood.
- Notes.

This helps admins review whether automated check-ins are working properly and whether any elders missed or failed a check-in.

### SOS and Emergency Monitoring

The SOS section lists emergency alerts triggered by elders.

SOS features include:

- SOS alert ID.
- Elder name.
- Triggered time.
- Latest available location coordinates.
- Google Maps link for location review.
- Detailed SOS alert page.

This feature helps admins respond faster during emergencies by giving them a direct way to inspect alert details and location information.

### Complaint Handling

The complaints section allows admins to view and manage complaints or feedback submitted by users.

Complaint handling includes:

- Complaint ID.
- Complainant name.
- Complaint subject.
- Complaint description.
- Reported date.
- Current status.
- Response/update screen.

Complaint statuses can be shown as pending, under review, or resolved. This helps admins track support issues and follow up with users.

### Admin UI Features

The Admin app includes several interface improvements:

- Fixed sidebar navigation.
- Dashboard cards.
- Searchable tables.
- Status badges.
- Emergency trend graph.
- Animated number counters.
- Page reveal effects.
- Responsive layout for smaller screens.
- Clear colors for danger, warning, active, stable, pending, and resolved states.

## Doctor App Overview

The Doctor app is designed for medical professionals using TrustCare. It gives doctors a clean workspace to review assigned elder patients and check medical information relevant to ongoing care.

Doctors can view:

- Assigned patient list.
- Patient profile details.
- Linked caregivers.
- Medical profile information.
- Latest vital records.
- Medication list.
- Daily medication adherence.
- Doctor profile information.

## Doctor App Features

### Secure Doctor Login

The Doctor app has its own login page. Only doctor accounts can access it.

Doctor login includes:

- Email and password login.
- Active account check.
- Doctor role validation.
- Password verification.
- Session creation after successful login.
- Session regeneration for safer login handling.
- Last login update.
- Redirect to dashboard after login.

### Doctor Dashboard

The Doctor dashboard gives a quick overview of the logged-in doctor's work.

Dashboard information includes:

- Total assigned patients.
- Total patient reports or vital records.
- Recent patients.
- Doctor name.
- Doctor specialization.

This helps doctors quickly see their patient workload and recent patient activity.

### Patient List

The patient list shows elders assigned to the logged-in doctor.

Patient list features include:

- Patient ID.
- Full name.
- Phone number.
- Email.
- Gender.
- Search by patient name.
- Search by mobile number.
- View button for patient details.

Doctors only see patients assigned to them. This keeps the Doctor app focused and prevents doctors from viewing unrelated patient records.

### Patient Detail Page

The patient detail page gives doctors a complete view of a selected elder.

Patient details include:

- Patient ID.
- Full name.
- Phone number.
- Email.
- Date of birth.
- Gender.
- Address.
- Active/inactive status.

The page also validates that the selected patient belongs to the logged-in doctor. If the patient is not assigned to that doctor, the app redirects back to the patient list.

### Caregiver Information

The patient detail page includes caregiver information linked to the elder.

Caregiver details include:

- Caregiver ID.
- Full name.
- Phone.
- Email.
- Relationship type.
- Primary caregiver status.
- Active/inactive status.

This helps doctors know who supports the elder outside the medical context.

### Medical Profile

The Doctor app displays medical profile data for each patient.

Medical profile information includes:

- Blood type.
- Doctor name.
- Allergies.
- Chronic conditions.
- Past surgeries.
- Emergency notes.

If the medical profile cannot be loaded, the page shows a clear message instead of breaking the layout.

### Latest Vitals

The Doctor app displays recent vital measurements in readable cards.

Vital cards include:

- Vital name.
- Latest value.
- Measurement unit.
- Recorded date/time.

If no vitals are available, the page shows an empty state. This keeps the doctor informed without showing confusing blank data.

### Medication List

The medicine page shows the elder's medication schedule.

Medicine information includes:

- Medicine name.
- Dosage.
- Instructions.
- Scheduled times.
- Repeat days.
- Start date.
- End date.

Scheduled medicine times are displayed as small time chips for quick scanning.

### Daily Medication Adherence

Doctors can also review daily medication adherence for a selected elder.

Adherence details include:

- Medicine name.
- Dosage.
- Scheduled time.
- Status.
- Date filter.

Statuses such as taken, missed, and skipped are shown using visual badges. This helps doctors understand whether the elder is following their medication plan.

### Doctor Profile

The Doctor profile page displays the logged-in doctor's own account and professional details.

Profile details include:

- Full name.
- Email.
- Phone.
- Gender.
- Date of birth.
- Address.
- Specialization.
- Hospital.
- License number.

### Doctor UI Features

The Doctor app includes:

- Clean login screen.
- Sidebar navigation.
- Doctor identity display in sidebar.
- Dashboard cards.
- Searchable patient table.
- Patient profile cards.
- Caregiver tables.
- Medical profile cards.
- Vitals cards.
- Medication tables.
- Adherence status badges.
- Responsive layout.

## What Happens in the Apps

### Admin Flow

1. Admin opens the Admin app.
2. The app checks if an admin session exists.
3. If not logged in, the admin is sent to the login page.
4. After login, the admin enters the dashboard.
5. The dashboard shows system summary cards and emergency trends.
6. Admin can navigate to caregivers, elders, doctors, admins, links, health AI, SOS, and complaints.
7. Admin can create doctor and admin accounts.
8. Admin can view user profiles and relationship details.
9. Admin can review emergencies and complaints.
10. Admin can logout to end the session.

### Doctor Flow

1. Doctor opens the Doctor app.
2. The app checks if a doctor session exists.
3. If not logged in, the doctor is sent to the login page.
4. After login, the doctor enters the dashboard.
5. The dashboard shows assigned patient count and recent patients.
6. Doctor opens the Patients page to search and select an elder.
7. Doctor views patient details, caregivers, medical profile, and vitals.
8. Doctor opens the medicine page to review medication schedules and adherence.
9. Doctor can view their own profile.
10. Doctor can logout to end the session.

## Special Features

- Separate portals for admin and doctor roles.
- Session-protected pages.
- Role-specific login behavior.
- Admin dashboard with key platform metrics.
- Emergency SOS trend graph.
- AI wellness insight monitoring.
- Elder risk level review.
- Caregiver assignment tracking.
- Complaint status handling.
- Emergency location review through map links.
- Doctor-only assigned patient access.
- Patient medical profile view.
- Latest vitals display.
- Medication schedule and adherence tracking.
- Searchable user and patient tables.
- Visual status badges.
- Responsive healthcare-focused interface.

## Setup and Run

Install or enable:

- XAMPP or another Apache/PHP server.
- PHP.
- Required PHP extensions for the project.
- Browser access to the local project URL.
- Access to required TrustCare services and APIs.

Place the project in:

```text
C:\xampp\htdocs\CGP
```

Run Admin app:

```text
http://localhost/CGP/Admin/
```

Run Doctor app:

```text
http://localhost/CGP/Doctor/
```

## Security Notes

- Admin pages require a valid admin session.
- Doctor pages require a valid doctor session.
- Login pages verify role-specific access.
- Passwords are checked using PHP password handling.
- Logout clears the active session.
- Doctor patient pages restrict access to assigned patients only.
- Sensitive service settings should not be exposed in public source code.
- Secure production deployment should use HTTPS.
- Delete and update actions should be used carefully because they can affect important care records.

## UI and Design

- The Admin app uses a TrustCare themed layout with sidebar navigation, cards, graphs, tables, badges, and action buttons.
- The Doctor app uses a clinical dashboard layout with sidebar navigation, patient tables, profile cards, vitals cards, and medicine tables.
- Both apps use soft healthcare colors.
- Both apps are designed for quick scanning of important information.
- Responsive layouts make the pages easier to use on smaller screens.
  
## Project Summary

TrustCare includes an Admin app and Doctor app for elderly care management. The Admin app handles full platform operations such as user management, emergency monitoring, complaints, caregiver links, and AI wellness insights. The Doctor app focuses on assigned patient care by showing patient profiles, caregivers, medical information, latest vitals, medicines, and adherence reports.

Together, these apps help care teams monitor elders, respond to problems faster, and keep care information organized in one connected system.
