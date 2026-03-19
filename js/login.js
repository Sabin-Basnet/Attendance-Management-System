$(document).ready(function() {
    console.log("JQuery Ready. Button ID check: ", $("#loginbtn").length);

    $(document).on("click", "#loginbtn", function(e) {
        e.preventDefault();
        alert("Click Detected!"); // If you see this, the JS is working!

        let un = $("#un").val();
        let pw = $("#pw").val();

        if (un != "" && pw != "") {
            $.ajax({
                url: "ajaxhandler/loginAjax.php",
                type: "POST",
                data: { user_name: un, password: pw },
                dataType: "json",
                success: function(response) {
                    if (response.status == "ALL OK") {
                        window.location.replace("attendance.php");
                    } else {
                        alert("Invalid Username or Password");
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert("Ajax Error: Check if ajaxhandler/loginAjax.php exists.");
                }
            });
        } else {
            alert("Please enter username and password");
        }
    });
});