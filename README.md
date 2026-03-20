# <U>ATTENDANCE MANAGEMENT SYSTEM</U>
## 📝 Project Overview
This is a PHP & MySQL based web application designed to automate student attendance tracking for the Computer Engineering department. It replaces manual registers with a digital database, ensuring data integrity and easy report generation.

Note: This project was developed as part of the DBMS Coursework (2026).

## 🚀 Key Features
### 1. Secure Teacher Login
* Department-specific access (e.g., BCT).
* Session-based security to prevent unauthorized URL access.

### 2. Smart Attendance Filter
* Year-Only Logic: Quickly filter 100+ students and 50+ subjects.
* AJAX Powered: Save attendance and load data without refreshing the page.

### 3. Automated PDF Reports
* Highlights students with shortage ( < 70% ) in red.
* Clean, professional print layout for official documentation.

## 🛠️ Technical Stack
* Language: PHP 
* Database: MySQL (Structured in 3rd Normal Form)
* Frontend: HTML5, CSS3, jQuery (AJAX)
* Server: XAMPP (Apache)

## 💻 How to Set Up Locally
### Step 1: Clone the Project
Bash
* git clone https://github.com/Sabin-Basnet/Attendance-Management-System.git
### Step 2: Database Setup
* Open phpMyAdmin (localhost/phpmyadmin).
* Create a new database named attendance_db.
* Run the createtables.php file in your browser:
localhost/your_folder_name/createtables.php
(This will automatically create all tables and insert 100 students).

### Step 3: Run the App
* Open your browser and go to:
localhost/your_folder_name/login.php

## 📊 Entity Relationship (ER) Logic
Our database design follows strict relational principles:

* Entities: Teacher, Student, Course
* Relation: Attendance (Associative Entity)
* Normalization: 3NF