<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Service Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="logo">🛠️ HSMS</div>
    </header>

    <div class="page-container" style="text-align:center;">
        <h2>Welcome to Home Service Management System</h2>
        <p>Book trusted technicians for TV, Plumbing, Electrical, AC, RO, Carpenter services and more.</p>

        <div style="display:flex; gap:16px; justify-content:center; margin-top:30px; flex-wrap:wrap;">
            <a href="customer/login.php" class="btn-large" style="text-decoration:none; display:inline-block; width:auto; padding:16px 32px;">I'm a Customer</a>
            <a href="admin/login.php" class="btn-large" style="text-decoration:none; display:inline-block; width:auto; padding:16px 32px; background:#444;">Admin Login</a>
            <a href="technician/login.php" class="btn-large" style="text-decoration:none; display:inline-block; width:auto; padding:16px 32px; background:#0f766e;">Technician Login</a>
        </div>
    </div>
</body>
</html>
