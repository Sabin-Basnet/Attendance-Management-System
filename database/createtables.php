<?php
require_once "database.php";

$db = new Database();
$conn = $db->getConnection();

try {
    // 1. Drop existing tables to start fresh (Careful: This deletes old data)
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $conn->exec("DROP TABLE IF EXISTS attendance_details, course_details, student_details, teacher_details, session_details;");
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 2. Create Teacher Table
    $conn->exec("CREATE TABLE teacher_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        user_name VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        department VARCHAR(10) NOT NULL
    )");

    // 3. Create Student Table
    $conn->exec("CREATE TABLE student_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        roll_no VARCHAR(20) UNIQUE NOT NULL
    )");

    // 4. Create Course Table (Year-Only)
    $conn->exec("CREATE TABLE course_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_name VARCHAR(100) NOT NULL,
        department VARCHAR(10) NOT NULL,
        year INT(1) NOT NULL
    )");

    // 5. Create Attendance Table
    $conn->exec("CREATE TABLE attendance_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        course_id INT NOT NULL,
        on_date DATE NOT NULL,
        status VARCHAR(1) NOT NULL,
        FOREIGN KEY (student_id) REFERENCES student_details(id),
        FOREIGN KEY (course_id) REFERENCES course_details(id)
    )");

    echo "Tables created successfully.<br>";

    // --- entering DATA ---

    // Insert Teachers (Password is '123' for both)
    $stmt = $conn->prepare("INSERT INTO teacher_details (name, user_name, password, department) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Ram Chandra Bhatta', 'rcb', '123', 'BCT']);
    $stmt->execute(['Civil Dept Head', 'civil_hod', '123', 'BCE']);

    // Insert 3rd Year BCT Courses
    $stmt = $conn->prepare("INSERT INTO course_details (course_name, department, year) VALUES (?, ?, ?)");
    $stmt->execute(['Microprocessors', 'BCT', 3]);
    $stmt->execute(['Computer Graphics', 'BCT', 3]);
    $stmt->execute(['Instrumentation II', 'BCT', 3]);
    $stmt->execute(['Computer Network', 'BCT', 3]);
    
    // Insert 2nd Year BCT Courses
    $stmt->execute(['Data Structure & Algorithms', 'BCT', 2]);
    $stmt->execute(['Discrete Structure', 'BCT', 2]);

    // Insert BCE Courses
    $stmt->execute(['Structural Analysis', 'BCE', 3]);
    $stmt->execute(['Hydrology', 'BCE', 3]);

    // Insert Students (BCT and BCE mix)
    $stmt = $conn->prepare("INSERT INTO student_details (name, roll_no) VALUES (?, ?)");
    
    // BCT Students (Batch 079 & 080)
    $stmt->execute(['Aayush Shrestha', '079BCT001']);
    $stmt->execute(['Bikram Dahal', '079BCT002']);
    $stmt->execute(['Chitra Kumar', '079BCT003']);
    $stmt->execute(['Deepa Rai', '080BCT001']);
    $stmt->execute(['Erica Subedi', '080BCT002']);
    $stmt->execute(['Firoz Khan', '080BCT003']);
    $stmt->execute(['Gaurav Thapa', '081BCT010']);

    // BCE Students
    $stmt->execute(['Hema Malini', '079BCE001']);
    $stmt->execute(['Ishwor Pokhrel', '080BCE005']);
    $stmt->execute(['Jeevan Luitel', '081BCE012']);

    echo "Data entered successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>