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
    <title>Attendance Report | IOE Portal</title>
    <style>
        <?php include('css/attendance.css'); ?>
        
        /* Print-specific Styling */
        @media print {
            .nav-bar, .filter-section, .btn-success, .btn-primary, .logout-link {
                display: none !important;
            }
            .main-content { padding: 0 !important; margin: 0 !important; }
            .card { box-shadow: none !important; border: none !important; padding: 0 !important; }
            body { background: white !important; }
            .attendance-table th { background: #f1f5f9 !important; color: black !important; -webkit-print-color-adjust: exact; }
            .status-badge { border: 1px solid #ccc !important; color: black !important; }
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
        }
        .bg-shortage { background-color: #ef4444; }
        .bg-eligible { background-color: #10b981; }
        .row-shortage { background-color: #fff1f2; }
    </style>
</head>
<body>
    <div class="nav-bar">
        <div class="nav-logo">IOE Report Portal</div>
        <div class="nav-user">
            <a href="attendance.php" style="color: #94a3b8; margin-right: 20px; text-decoration: none;">← Back to Attendance</a>
            <span>Welcome, <strong><?php echo $teacher_name . " (" . $dept . ")"; ?></strong></span>
        </div>
    </div>

    <div class="main-content">
        <div class="card filter-section">
            <h3 style="margin-top: 0; color: #1e293b;">Generate Subject Report</h3>
            <div class="filter-grid" style="grid-template-columns: 1fr 1fr auto;">
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
                <button id="generate_report_btn" class="btn-primary" style="align-self: flex-end; height: 45px; padding: 0 30px;">View Analysis</button>
            </div>
        </div>

        <div id="report_area" class="card" style="display:none;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
                <div>
                    <h2 id="report_title" style="margin:0; color: #0f172a; text-transform: uppercase;">Subject Report</h2>
                    <p style="color: #64748b; margin: 5px 0 0 0;">Department of <?php echo $dept; ?> | Academic Year 2026</p>
                </div>
                <button onclick="window.print()" class="btn-success" style="width: auto; padding: 10px 20px; background: #6366f1;">
                    ⎙ Print Official PDF
                </button>
            </div>

            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th style="text-align:center;">Present</th>
                        <th style="text-align:center;">Total Classes</th>
                        <th style="text-align:center;">Percentage</th>
                        <th>Eligibility</th>
                    </tr>
                </thead>
                <tbody id="report_data">
                    </tbody>
            </table>
            
            <div style="margin-top: 25px; font-size: 0.9rem; color: #94a3b8; font-style: italic;">
                * Minimum 75% attendance is required for exam eligibility.
            </div>
        </div>
    </div>

    <script src="js/jquery.js"></script>
    <script>
        $(document).ready(function() {
            // 1. Load Subjects based on Year
            $("#year_select").change(function() {
                let yr = $(this).val();
                if(!yr) return;
                $.ajax({
                    url: "ajaxhandler/attendanceAjax.php",
                    type: "POST",
                    data: { action: "get_subjects", year: yr },
                    dataType: "json",
                    success: function(data) {
                        let html = '<option value="">Select Subject</option>';
                        data.forEach(sub => { html += `<option value="${sub.id}">${sub.course_name}</option>`; });
                        $("#subject_select").html(html);
                    }
                });
            });

            // 2. Generate Report Logic
            $("#generate_report_btn").click(function() {
                let subId = $("#subject_select").val();
                if(!subId) return alert("Please select a subject.");

                $(this).text("Processing...").prop('disabled', true);

                $.ajax({
                    url: "ajaxhandler/attendanceAjax.php",
                    type: "POST",
                    data: { action: "get_report", course_id: subId },
                    dataType: "json",
                    success: function(data) {
                        $("#generate_report_btn").text("View Analysis").prop('disabled', false);
                        let html = '';
                        
                        data.forEach(row => {
                            let total = parseInt(row.total_classes) || 0;
                            let present = parseInt(row.present_count) || 0;
                            let per = total > 0 ? ((present / total) * 100).toFixed(1) : 0;
                            
                            // 75% Eligibility Logic
                            let isShortage = per < 75;
                            let statusClass = isShortage ? "bg-shortage" : "bg-eligible";
                            let statusText = isShortage ? "Shortage" : "Eligible";
                            let rowClass = isShortage ? "row-shortage" : "";

                            html += `<tr class="${rowClass}">
                                <td style="font-weight:600;">${row.roll_no}</td>
                                <td>${row.name}</td>
                                <td style="text-align:center;">${present}</td>
                                <td style="text-align:center;">${total}</td>
                                <td style="text-align:center; font-weight:bold; color:${isShortage ? '#ef4444' : '#10b981'};">${per}%</td>
                                <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                            </tr>`;
                        });

                        $("#report_data").html(html);
                        $("#report_area").show();
                        $("#report_title").text($("#subject_select option:selected").text() + " - Attendance Analysis");
                    },
                    error: function() {
                        $("#generate_report_btn").text("View Analysis").prop('disabled', false);
                        alert("Error generating report. Check database connection.");
                    }
                });
            });
        });
    </script>
</body>
</html>