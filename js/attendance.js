$(document).ready(function() {
    
    // 1. Load Subjects based on Year
    $("#year_select").change(function() {
        let yr = $(this).val();
        if(!yr) {
            $("#subject_select").html('<option value="">Select Year First</option>');
            return;
        }

        $.ajax({
            url: "ajaxhandler/attendanceAjax.php",
            type: "POST",
            data: { action: "get_subjects", year: yr },
            dataType: "json",
            success: function(data) {
                let html = '<option value="">Select Subject</option>';
                if(data.length > 0) {
                    data.forEach(sub => {
                        html += `<option value="${sub.id}">${sub.course_name}</option>`;
                    });
                } else {
                    html = '<option value="">No subjects found</option>';
                }
                $("#subject_select").html(html);
            }
        });
    });

    // 2. Refresh list automatically if Date changes while table is visible
    $("#attendance_date").change(function() {
        if($("#student_list_area").is(":visible")) {
            $("#load_students_btn").click();
        }
    });

    // 3. Load Student List (With historical data check)
    $("#load_students_btn").click(function() {
        let subId = $("#subject_select").val();
        let dateVal = $("#attendance_date").val();

        if(!subId) return alert("Please select a subject first.");

        $.ajax({
            url: "ajaxhandler/attendanceAjax.php",
            type: "POST",
            data: { 
                action: "get_students", 
                date: dateVal, 
                course_id: subId 
            },
            dataType: "json",
            success: function(data) {
                let html = '';
                data.forEach((stu, i) => {
                    // If status is 'P' in database, check the box
                    let isChecked = (stu.status === 'P') ? 'checked' : '';
                    
                    html += `<tr>
                        <td>${i + 1}</td>
                        <td>${stu.roll_no}</td>
                        <td>${stu.name}</td>
                        <td>
                            <input type="checkbox" class="status-chk" data-id="${stu.id}" ${isChecked}> 
                            <span class="status-label">Present</span>
                        </td>
                    </tr>`;
                });
                
                $("#student_data").html(html);
                $("#student_list_area").show();
                $("#display_title").text($("#subject_select option:selected").text() + " (" + dateVal + ")");
                
                // Reset the "Select All" checkbox when new data loads
                $("#select_all_btn").prop('checked', false);
            }
        });
    });

    // 4. Save Attendance (Updated with Toast Notification)
    $("#save_attendance_btn").click(function() {
        let dateVal = $("#attendance_date").val();
        let subId = $("#subject_select").val();

        let attendanceData = [];
        $(".status-chk").each(function() {
            attendanceData.push({
                student_id: $(this).data("id"),
                status: $(this).is(":checked") ? "P" : "A"
            });
        });

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
                if(res.status == "OK") {
                    showToast(res.message); // Call the professional toast!
                } else {
                    alert("Error: " + res.message); // Keep alert only for errors
                }
            }
        });
    });

    // Helper function to show the Toast
    function showToast(msg) {
    let toast = $("#toast_message");
    
    // If it's already showing, don't restart the animation
    if (toast.hasClass("show")) return;

    toast.text(msg);
    toast.addClass("show");
    
    // Auto-hide after 3 seconds
    setTimeout(function() { 
        toast.removeClass("show"); 
    }, 3000);
    }

    // 5. THE SELECT ALL FEATURE
    // This allows the teacher to toggle all 100 students at once
    $(document).on("change", "#select_all_btn", function() {
        let isChecked = $(this).prop('checked');
        $(".status-chk").prop('checked', isChecked);
    });

});