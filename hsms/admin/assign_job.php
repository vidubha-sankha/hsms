<?php
$required_role = 'admin';
require_once __DIR__ . '/../includes/auth_check.php';

$message = "";

// Handle assignment submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request_id = $_POST['request_id'];
    $employee_id = $_POST['employee_id'];

    $stmt = $conn->prepare("UPDATE ServiceRequest SET EmployeeID = ?, Status = 'Assigned' WHERE RequestID = ?");
    $stmt->bind_param("ii", $employee_id, $request_id);
    if ($stmt->execute()) {
        $message = "Technician assigned successfully.";
        // In a full build: trigger SMS notification here
    } else {
        $message = "Failed to assign technician.";
    }
}

// Pending requests that need a technician
$pending = $conn->query("
    SELECT sr.RequestID, c.Name AS CustomerName, c.Phone, sc.ServiceName, sr.PreferredDate, sr.Address, sr.IsEmergency
    FROM ServiceRequest sr
    JOIN Customer c ON sr.CustomerID = c.CustomerID
    JOIN ServiceCategory sc ON sr.ServiceID = sc.ServiceID
    WHERE sr.Status = 'Pending'
    ORDER BY sr.IsEmergency DESC, sr.RequestDate ASC
");

// Available technicians grouped for the dropdown
$employees = $conn->query("SELECT EmployeeID, Name, Specialization, Availability, Rating FROM Employee ORDER BY Specialization");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Jobs - HSMS Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="page-container">
        <h2>Assign Technicians to Pending Requests</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Request</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Address</th>
                    <th>Assign Technician</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pending->num_rows === 0): ?>
                    <tr><td colspan="6">No pending requests.</td></tr>
                <?php else: ?>
                    <?php while ($req = $pending->fetch_assoc()): ?>
                        <tr class="<?= $req['IsEmergency'] ? 'row-emergency' : '' ?>">
                            <td>#<?= $req['RequestID'] ?> <?= $req['IsEmergency'] ? '🔴' : '' ?></td>
                            <td><?= htmlspecialchars($req['CustomerName']) ?><br><small><?= htmlspecialchars($req['Phone']) ?></small></td>
                            <td><?= htmlspecialchars($req['ServiceName']) ?></td>
                            <td><?= htmlspecialchars($req['PreferredDate']) ?></td>
                            <td><?= htmlspecialchars($req['Address']) ?></td>
                            <td>
                                <form method="POST" action="" class="inline-form">
                                    <input type="hidden" name="request_id" value="<?= $req['RequestID'] ?>">
                                    <select name="employee_id" required>
                                        <option value="">-- Select --</option>
                                        <?php
                                        $employees->data_seek(0);
                                        while ($emp = $employees->fetch_assoc()):
                                        ?>
                                            <option value="<?= $emp['EmployeeID'] ?>">
                                                <?= htmlspecialchars($emp['Name']) ?> - <?= htmlspecialchars($emp['Specialization']) ?>
                                                (<?= $emp['Availability'] ?>, ★<?= $emp['Rating'] ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <button type="submit" class="btn-small">Assign</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
