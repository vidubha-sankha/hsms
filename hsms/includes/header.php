<?php
// includes/header.php
?>
<header class="site-header">
    <div class="logo">🛠️ HSMS</div>
    <nav>
        <?php if (isset($_SESSION['customer_id'])): ?>
            <a href="/hsms/customer/dashboard.php">Dashboard</a>
            <a href="/hsms/customer/book_service.php">Book Service</a>
            <a href="/hsms/customer/track_service.php">My Requests</a>
            <a href="/hsms/logout.php">Logout (<?= htmlspecialchars($_SESSION['customer_name']) ?>)</a>
        <?php elseif (isset($_SESSION['admin_id'])): ?>
            <a href="/hsms/admin/dashboard.php">Dashboard</a>
            <a href="/hsms/admin/manage_customers.php">Customers</a>
            <a href="/hsms/admin/manage_employees.php">Employees</a>
            <a href="/hsms/admin/manage_services.php">Services</a>
            <a href="/hsms/admin/assign_job.php">Assign Jobs</a>
            <a href="/hsms/admin/reports.php">Reports</a>
            <a href="/hsms/logout.php">Logout</a>
        <?php elseif (isset($_SESSION['employee_id'])): ?>
            <a href="/hsms/technician/dashboard.php">My Jobs</a>
            <a href="/hsms/logout.php">Logout (<?= htmlspecialchars($_SESSION['employee_name']) ?>)</a>
        <?php endif; ?>
    </nav>
</header>
