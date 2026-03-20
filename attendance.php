<?php
session_start();
if(!isset($_SESSION['teacher_id'])) { header("Location: login.php"); exit(); }
$teacher_name = $_SESSION['teacher_name'];
$dept = $_SESSION['teacher_dept'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance | IOE Portal</title>
    <link rel="stylesheet" href="css/attendance.css">
</head>
<body>

    <div id="toast_message" class="toast-popup">
        Attendance Saved Successfully!
    </div>

    <div class="nav-bar">
        <div class="nav-logo">IOE Attendance Portal</div>
        <div class="nav-user">
            <a href="report.php" class="nav-link-btn" style="background: #4f46e5; padding: 8px 15px; border-radius: 6px; color: white; text-decoration: none; margin-right: 15px; font-size: 0.85rem;"> 📊 View Reports</a>
            <button id="open_add_modal" class="btn-primary" style="background: #0ea5e9; padding: 8px 15px; border-radius: 6px; color: white; text-decoration: none; margin-right: 15px; font-size: 0.85rem;">+ Add Student</button>
            <span>Welcome, <strong><?php echo $teacher_name . " (" . $dept . ")"; ?></strong></span>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="card filter-section">
            <div class="filter-grid">
                <div class="input-group">
                    <label>Year</label>
                    <select id="year_select">
                        <option value="">Select Year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Subject</label>
                    <select id="subject_select">
                        <option value="">Select Year First</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Date</label>
                    <input type="date" id="attendance_date" 
                    value="<?php echo date('Y-m-d'); ?>" 
                    max="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <button id="load_students_btn" class="btn-primary" style="width:100%">View Students</button>
        </div>

        <div id="student_list_area" class="card" style="display:none;">
            <div class="list-controls">
                <label class="checkbox-label">
                    <input type="checkbox" id="select_all_btn"> Mark All Present
                </label>
                <div class="counter-text">
                    Checked: <span id="present_count">0</span> Students
                </div>
            </div>

            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="student_data">
                    </tbody>
            </table>
            
            <button id="save_attendance_btn" class="btn-success">Save Attendance</button>
        </div>
    </div>
    <div id="add_student_modal" class="modal-overlay" style="display:none;">
    <!-- this is for the add student button -->
    <div class="modal-content card">
        <h3>Register New Student</h3>
        <form id="add_student_form">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="stu_name" required placeholder="Ex: John Doe">
            </div>
            <div class="input-group">
                <label>Roll Number</label>
                <input type="text" name="stu_roll" required placeholder="Ex: 078BCT001">
            </div>
            <div class="input-group">
                <label>Year</label>
                <select name="stu_year" required>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-success" style="margin:0;">Register Student</button>
                <button type="button" id="close_modal" class="btn-primary" style="background:#64748b; margin:0;">Cancel</button>
            </div>
        </form>
    </div>
</div>
    <script src="js/jquery.js"></script>
    <script src="js/attendance.js"></script>
</body>
</html>