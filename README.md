# Attendance-Management-System
📋 IOE Attendance Management System
A robust, web-based attendance tracking solution designed for engineering departments. This system allows professors to manage student attendance for multiple batches (years) and subjects, with automated report generation.

🚀 Key Features
Department-Based Login: Secure access for professors, filtering data by their specific department (BCT, BCE, etc.).

Dynamic Year Filtering: "Year-Only" logic to manage 50+ subjects and 100+ students efficiently.

Real-Time Search: Instant AJAX-powered search bar to find students by Name or Roll Number.

Official Reports: Automated calculation of attendance percentages with "Attendance Shortage" (below 70%) highlighting.

Print to PDF: Clean, professional CSS styles for printing official reports for department heads.

🛠️ Tech Stack
Frontend: HTML5, CSS3, JavaScript (jQuery)

Backend: PHP 8.x

Database: MySQL

Architecture: AJAX-based asynchronous data handling (No page refreshes)
⚙️ Local Setup Instructions
Clone the Repository:

Bash
git clone https://github.com/your-username/your-repo-name.git
Move to XAMPP:
Place the folder inside your C:\xampp\htdocs\ directory.

Database Configuration:

Open XAMPP and start Apache and MySQL.

Go to localhost/phpmyadmin and create a database named attendance_db.

Open database/database.php and ensure your credentials match your local setup.

Run Setup Script:
Run localhost/your-project-folder/createtables.php in your browser. This will automatically create the 4 tables and insert the demo data (100 students).

📊 Database Schema (3NF)
The project follows Third Normal Form (3NF) to ensure data integrity and zero redundancy.
