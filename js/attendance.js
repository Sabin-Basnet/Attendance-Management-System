$(document).ready(function() {
    
    // 1. Load Subjects when Year changes
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
                data.forEach(sub => { 
                    html += `<option value="${sub.id}">${sub.course_name}</option>`; 
                });
                $("#subject_select").html(html);
                $("#student_list_area").hide(); // Hide list if year/subject changes
            }
        });
    });

    // 2. Manual Trigger: View Students
    $("#load_students_btn").click(function() {
        if(!$("#subject_select").val()) return alert("Please select a subject");
        fetchList();
    });

    // 3. Auto-refresh on Date change (only if list is already open)
    $("#attendance_date").change(function() {
        if($("#student_list_area").is(":visible")) {
            fetchList();
        }
    });

    // 4. Fetch Function: Now handles "Remembering" saved data
    function fetchList() {
        let yr = $("#year_select").val();
        let sub = $("#subject_select").val();
        let dt = $("#attendance_date").val();

        $.ajax({
            url: "ajaxhandler/attendanceAjax.php",
            type: "POST",
            data: { 
                action: "get_students", 
                year: yr,
                date: dt,
                course_id: sub
            },
            dataType: "json",
            success: function(data) {
                let html = '';
                data.forEach((stu, i) => {
                    let isChecked = (stu.status === 'P') ? 'checked' : '';
    
                    html += `<tr>
                        <td>${i+1}</td>
                        <td>${stu.roll_no}</td>
                        <td>${stu.name}</td>
                        <td style="display: flex; align-items: center; gap: 15px;">
                            <label><input type="checkbox" class="status-chk" data-id="${stu.id}" ${isChecked}> Present</label>
                            <button class="delete-student-btn" data-id="${stu.id}" data-name="${stu.name}" 
                                    style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:1.2rem;" title="Delete Student">
                                🗑
                            </button>
                        </td>
                    </tr>`;
                });
                $("#student_data").html(html);
                $("#student_list_area").show();
                
                // Re-sync the "Select All" checkbox state
                updateSelectAllState();
                updateCount();
            }
        });
    }

    // 5. Save Logic (Silent with Toast)
    $(document).on("click", "#save_attendance_btn", function() {
        let attendanceData = [];
        let subId = $("#subject_select").val();
        let dateVal = $("#attendance_date").val();

        $(".status-chk").each(function() {
            attendanceData.push({
                student_id: $(this).data("id"),
                status: $(this).is(":checked") ? "P" : "A"
            });
        });

        const btn = $(this);
        btn.prop('disabled', true).text("Saving...");

        $.ajax({
            url: "ajaxhandler/attendanceAjax.php",
            type: "POST",
            data: { 
                action: "save_attendance", 
                date: dateVal,
                course_id: subId,
                attendance: attendanceData 
            },
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).text("Save Attendance");
                if(res.status === "OK") {
                    // Show toast notification
                    $("#toast_message").stop(true, true).fadeIn().delay(2000).fadeOut();
                } else {
                    alert("Error: " + res.message);
                }
            },
            error: function() {
                btn.prop('disabled', false).text("Save Attendance");
                alert("Critical error occurred while saving.");
            }
        });
    });

    // 6. Helpers: Checkbox & Counter Logic
    $(document).on("change", "#select_all_btn", function() {
        $(".status-chk").prop('checked', $(this).is(':checked'));
        updateCount();
    });

    $(document).on("change", ".status-chk", function() {
        updateCount();
        updateSelectAllState();
    });

    function updateCount() {
        $("#present_count").text($(".status-chk:checked").length);
    }

    // Automatically untick "Select All" if one student is unchecked
    function updateSelectAllState() {
        let total = $(".status-chk").length;
        let checked = $(".status-chk:checked").length;
        if(total > 0 && total === checked) {
            $("#select_all_btn").prop('checked', true);
        } else {
            $("#select_all_btn").prop('checked', false);
        }
    }

    //this is to add student
    // Open Modal
    $(document).on("click", "#open_add_modal", function(e) {
        e.preventDefault();
        $("#add_student_modal").css("display", "flex").hide().fadeIn(200);
    });

    // Close Modal (Clicking Cancel)
    $(document).on("click", "#close_modal", function() {
        $("#add_student_modal").fadeOut(200);
    });

    // Close Modal (Clicking outside the white box)
    $(window).on("click", function(e) {
        if ($(e.target).is("#add_student_modal")) {
            $("#add_student_modal").fadeOut(200);
        }
    });

    // Handle Form Submission
    $("#add_student_form").on("submit", function(e) {
        e.preventDefault();
        
        // Collect data
        let name = $("input[name='stu_name']").val();
        let roll = $("input[name='stu_roll']").val();
        let year = $("select[name='stu_year']").val();

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text("Adding...");

        $.ajax({
            url: "ajaxhandler/attendanceAjax.php",
            type: "POST",
            data: {
                action: "add_student",
                stu_name: name,
                stu_roll: roll,
                stu_year: year
            },
            dataType: "json",
            success: function(res) {
                submitBtn.prop('disabled', false).text("Register Student");
                if(res.status === "OK") {
                    $("#add_student_modal").fadeOut();
                    $("#add_student_form")[0].reset();
                    
                    // Show Success Toast
                    $("#toast_message").text(res.message).stop(true, true).fadeIn().delay(2000).fadeOut();
                    
                    // Refresh student list if it's currently open
                    if($("#student_list_area").is(":visible")) {
                        fetchList(); 
                    }
                } else {
                    alert(res.message); // Show error (e.g., "Roll No exists")
                }
            },
            error: function() {
                submitBtn.prop('disabled', false).text("Register Student");
                alert("Server error. Check AJAX handler.");
            }
        });
    });

    // --- DELETE STUDENT LOGIC ---
    $(document).on("click", ".delete-student-btn", function() {
        let studentId = $(this).data("id");
        let studentName = $(this).data("name");
    
        if (confirm(`Are you sure you want to delete ${studentName}? This will also remove all their attendance records.`)) {
            $.ajax({
                url: "ajaxhandler/attendanceAjax.php",
                type: "POST",
                data: {
                    action: "delete_student",
                    stu_id: studentId
                },
                dataType: "json",
                success: function(res) {
                    if(res.status === "OK") {
                        $("#toast_message").text(res.message).stop(true, true).fadeIn().delay(2000).fadeOut();
                        fetchList(); // Refresh the list immediately
                    } else {
                        alert("Error: " + res.message);
                    }
                }
            });
        }
    });
});