Copy🚗 Dhaka Drive — Automobile Driving Training Institute

A web-based management system for a driving training institute, built as a System Design and Analysis project for East West University (CSE347 — Information System Analysis and Design).


📋 Table of Contents

About the Project
Features
Tech Stack
Project Structure
Database Schema
Getting Started
Default Admin Credentials
Course Offerings & Pricing
System Design
Team Members
Limitations
License


About the Project
Dhaka Drive is a full-stack web application that digitalises the day-to-day operations of a driving training institute. It provides a centralised platform where students can register, enrol in courses, make payments, track their progress, and access learning materials — while administrators can manage users, enrollments, scheduling, and communications from a single dashboard.
The project was designed and developed following BRTA (Bangladesh Road Transport Authority) guidelines and is tailored for the Bangladeshi driving education context, supporting payment methods like bKash and Nagad.

Features
👨‍🎓 Learner / Student

Sign up with OTP-based email/phone verification
Secure login with session management
View and enrol in available driving courses
Select preferred schedule and training location
Pay course fees online (bKash / Nagad) or offline (cash)
View payment history and transaction records
Track enrollment and training status
Access and download learning materials (PDFs, documents)
Submit support requests / messages to admin
Receive in-app notifications

🛠️ Admin

Manage all learner accounts (activate, suspend, block)
Review and approve/reject enrollment requests
Assign training locations and schedule slots
Confirm offline payments
Respond to learner support messages
Monitor payment transactions
View login history and user activity


Tech Stack
LayerTechnologyFrontendHTML5, CSS3, JavaScript (Vanilla)BackendPHP (procedural + OOP helpers)DatabaseMySQLSession HandlingPHP native sessionsPayment MethodsbKash, Nagad (manual gateway integration)StylingCustom CSS (css/style.css)Learning MaterialsPDF, DOCX (served as static files)

Project Structure
dhakadrive/
│
├── index.php                  # Public landing / home page
├── login.php                  # Login page
├── otp-verify.php             # OTP verification page
├── dashboard.php              # Learner dashboard
├── admin.php                  # Admin dashboard
├── notifications.php          # Notifications page
├── config.php                 # DB connection & helper functions
│
├── course-car.php             # Car Driving Course details & enrol
├── course-bike.php            # Motorcycle Riding Course details & enrol
├── course-scooter.php         # Scooter Riding Lessons details & enrol
├── course-bicycle.php         # Bicycle Safety Program details & enrol
│
├── actions/                   # All form/AJAX action handlers (PHP)
│   ├── login_process.php      # Authenticate user
│   ├── signup_process.php     # Register new user
│   ├── otp_process.php        # Validate OTP
│   ├── logout.php             # Destroy session
│   ├── enroll_process.php     # Submit enrollment request
│   ├── submit_payment.php     # Process payment submission
│   ├── update_enrollment_status.php  # Admin: approve/reject enrollment
│   ├── update_user_status.php        # Admin: block/activate accounts
│   ├── schedule_class.php     # Admin: assign schedule slot
│   ├── send_message.php       # Learner: send support message
│   ├── admin_reply.php        # Admin: reply to messages
│   ├── submit_document.php    # Upload user documents
│   ├── update_profile.php     # Update learner profile
│   └── update_user_profile.php # Admin: update any user profile
│
├── partials/
│   ├── header.php             # Shared HTML head + navbar
│   └── footer.php             # Shared footer
│
├── css/
│   └── style.css              # Global stylesheet
│
├── js/
│   ├── app.js                 # Frontend logic (dashboard, enrollment, payments)
│   └── admin.js               # Admin dashboard JS
│
├── materials/                 # Downloadable learning materials
│   ├── DhakaDrive-Traffic-Signs.pdf
│   ├── DhakaDrive-Vehicle-Checklist.pdf
│   └── DhakaDrive-BRTA-Guide.pdf
│
├── Picture/                   # Image assets
└── dhakadrive_db.sql          # Full database schema + seed data

Database Schema
The application uses the dhakadrive_db MySQL database with the following tables:
TablePurposeusersStores all users (learners and admins). Fields include name, email, mobile, password (bcrypt), role, account_status, is_verified, otp, document_number, document_pathenrollmentsTracks course enrollments. Fields include user_id, course_name, preferred_location, assigned_location, status, scheduled_slot, payment_method, trx_idmessagesLearner–admin messaging. Fields include user_id, sender_role (user/admin), message, timestampnotificationsIn-app notifications per user. Fields include user_id, message, is_readlogin_historyTracks each login event per user with a timestamp
All foreign keys reference the users table with ON DELETE CASCADE.

Getting Started
Prerequisites

PHP 7.4 or higher
MySQL 5.7 or higher
Apache/Nginx web server (or XAMPP / WAMP / MAMP for local development)

Installation Steps

Clone or download the repository

bash   git clone https://github.com/your-username/dhakadrive.git

Move to your web server's root directory

bash   # For XAMPP on Windows:
   cp -r dhakadrive/ C:/xampp/htdocs/dhakadrive/

   # For Linux Apache:
   cp -r dhakadrive/ /var/www/html/dhakadrive/

Import the database

Open phpMyAdmin (or any MySQL client)
Create a new database named dhakadrive_db
Import the file: dhakadrive/dhakadrive_db.sql


Configure the database connection
Open config.php and update if needed:

php   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');       // update with your MySQL password
   define('DB_NAME', 'dhakadrive_db');

Start your web server and visit:

   http://localhost/dhakadrive/

Default Admin Credentials
After importing the database, a default admin account is seeded:
FieldValueEmailadmin@dhakadrive.comPasswordadmin123 (bcrypt hashed in DB)Roleadmin

⚠️ Change the default admin password immediately in a production environment.


Course Offerings & Pricing
CoursePrice (BDT)Car Driving Course (Manual & Auto)৳5,000Motorcycle Riding Course৳3,000Scooter Riding Lessons৳2,500Bicycle Safety Program৳1,000

System Design
This project was built following a structured system analysis and design methodology. Below is a summary of the key design artefacts documented in the project report:

Use Case Diagram — Covers Learner (new/old) and Admin actors with interactions including Sign-up, OTP verification, course enrollment, payments, messaging, and schedule management.
Activity Diagrams — Detailed flows for Login, Payment, Enrollment, and Learning Material Access.
Class Diagram — Core classes: User, Admin, Learner, Trainer, Course, Enrollment, Payment, Schedule, Message.
Data Flow Diagrams (Level 0 & Level 1) — Showing data exchange between Learner, Admin, Trainer, and the system.
E-R Diagram — Entity relationships across users, enrollments, payments, materials, notifications, messages, conversations, and support_requests.
Sequence Diagram — Full flow from Sign-up → OTP → Login → Enrollment → Payment → Admin notification → Learning material access.


Team Members
NameStudent IDAhsiul Karim2022-3-60-074Sadia Afrin2022-2-60-088Tina Ali2022-1-60-320Jahir Hasan Biddut2022-1-60-096
Instructor: Md. Sabbir Hossain — Lecturer, Dept. of Computer Science and Engineering, East West University
Course: CSE347 — Information System Analysis and Design | Submission Date: 30-08-2025

Limitations

Limited scalability — The system is designed specifically for Dhaka Drive and would require customisation for other institutes.
Internet dependency — Features such as online registration and progress tracking require a stable internet connection.
Hardware constraints — May face performance issues on low-end devices.
Manual data entry errors — Attendance and test result inputs are still manually entered and prone to human error.
No real-time driving feedback — Practical performance evaluation remains instructor-dependent with no automated analysis.


License
This project was developed for academic purposes under East West University. All rights reserved by the project group members.
