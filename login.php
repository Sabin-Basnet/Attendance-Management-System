<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IOE Faculty Login</title>
    <style>
        <?php include('css/login.css'); ?>
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body style='background-color:blue'>
    <div class="viewport-wrapper">
        <div class="login-card">
            <div class="campus-logo">
                <div class="logo-text">IOE</div>
            </div>
            
            <h2 class="title">Faculty Login</h2>
            <p class="subtitle">Please enter your credentials to access the attendance system.</p>

            <div class="form-container">
                <div class="input-block">
                    <label for="un">Username</label>
                    <input type="text" id="un" autocomplete="off" placeholder="Enter Username">
                </div>

                <div class="input-block">
                    <label for="pw">Password</label>
                    <input type="password" id="pw" placeholder="Enter Password">
                </div>

                <button id="loginbtn" class="login-button">Sign In</button>
            </div>

            <div class="footer-note">
                © 2026 Engineering Campus Attendance
            </div>
        </div>
    </div>

    <script src="js/jquery.js"></script>
    <script>
        <?php include('js/login.js'); ?>
    </script>
</body>
</html>
</body>
</html>