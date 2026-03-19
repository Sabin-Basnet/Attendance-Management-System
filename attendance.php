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
    <title>Attendance Dashboard | <?php echo $dept; ?></title>
    <style><?php include('css/attendance.css'); ?></style>
</head>
<body>
    <div id="toast_message" class="toast">Attendance Saved Successfully!</div>

    <div class="nav-bar">
        <div class="nav-logo">IOE Attendance Portal</div>
        <div class="nav-user">
            <a href="report.php" style="color: #6366f1; background: white; padding: 5px 12px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-right: 20px; font-size: 14px;">View Reports</a>
            
            <span>Welcome, <strong><?php echo $teacher_name; ?></strong> (<?php echo $dept; ?>)</span>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="card filter-section">
            <h3 class="section-title">Class Attendance</h3>
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
                    <label>Attendance Date</label>
                    <input type="date" id="attendance_date" 
                           value="<?php echo date('Y-m-d'); ?>" 
                           max="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <button id="load_students_btn" class="btn-primary">View Student List</button>
        </div>

        <div id="student_list_area" class="card list-section" style="display:none;">
            <input type="text" id="student_search" placeholder="Quick Search (Roll No or Name)..." 
                   style="width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ddd; font-size: 14px;">

            <div class="list-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h2 id="display_title" style="margin: 0; font-size: 18px; color: #1e293b;">Subject Name</h2>
                
                <div class="select-all-container" style="background: #f1f5f9; padding: 8px 15px; border-radius: 20px;">
                    <input type="checkbox" id="select_all_btn" style="cursor: pointer;"> 
                    <label for="select_all_btn" style="font-weight: bold; cursor: pointer; color: #475569;"> Mark All Present</label>
                </div>
            </div>

            <table class="attendance-table">
                <thead>
                    <tr>
                        <th width="10%">S.N.</th>
                        <th width="30%">Roll Number</th>
                        <th width="40%">Student Name</th>
                        <th width="20%">Status</th>
                    </tr>
                </thead>
                <tbody id="student_data">
                    </tbody>
            </table>

            <div class="action-footer" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                <div style="font-weight: bold; color: #64748b;">
                    Checked: <span id="present_count" style="color: #10b981;">0</span> Students
                </div>
                <button id="save_attendance_btn" class="btn-success">Confirm & Save Attendance</button>
            </div>
        </div>
    </div>

    <script src="js/jquery.js"></script>
    <script><?php include('js/attendance.js'); ?></script>
</body>
</html>