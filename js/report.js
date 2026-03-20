$("#generate_report_btn").click(function() {
    let subId = $("#subject_select").val();
    if(!subId) return alert("Please select a subject first.");

    $.ajax({
        url: "ajaxhandler/attendanceAjax.php",
        type: "POST",
        data: { action: "get_report", course_id: subId },
        dataType: "json",
        success: function(data) {
            let html = '';
            data.forEach(row => {
                // Total classes held for this subject
                let total = parseInt(row.total_classes) || 0;
                let present = parseInt(row.present_count) || 0;
                
                // Calculate Percentage
                let per = total > 0 ? ((present / total) * 100).toFixed(1) : 0;
                
                // 75% is the usual requirement for IOE
                let isShortage = per < 75; 
                let statusColor = isShortage ? "#ef4444" : "#10b981";
                let statusText = isShortage ? "Shortage" : "Eligible";
                let rowBg = isShortage ? "#fff1f2" : "transparent"; // Light red background for shortage

                html += `<tr style="background-color: ${rowBg}">
                    <td>${row.roll_no}</td>
                    <td>${row.name}</td>
                    <td style="text-align:center">${present}</td>
                    <td style="text-align:center">${total}</td>
                    <td style="font-weight:bold; color:${statusColor}">${per}%</td>
                    <td>
                        <span style="padding:4px 8px; border-radius:4px; background:${statusColor}; color:white; font-size:0.8rem;">
                            ${statusText}
                        </span>
                    </td>
                </tr>`;
            });
            $("#report_data").html(html);
            $("#report_area").show();
            $("#report_title").text($("#subject_select option:selected").text());
        }
    });
});