<?php
$required_role = 'customer';
require_once __DIR__ . '/../includes/auth_check.php';

$error = "";
$success = "";

// fetch service categories for dropdown
$services = $conn->query("SELECT ServiceID, ServiceName, Price FROM ServiceCategory ORDER BY ServiceName");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer_id = $_SESSION['customer_id'];
    $service_id = $_POST['service_id'];
    $address = trim($_POST['address']);
    $description = trim($_POST['description']);
    $preferred_date = $_POST['preferred_date'];
    $preferred_time = $_POST['preferred_time'];
    $is_emergency = isset($_POST['is_emergency']) ? 1 : 0;
    $photo_path = null;

    if ($address === "" || $preferred_date === "") {
        $error = "Please fill in address and preferred date.";
    } else {
        // handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $newname = uniqid('req_') . '.' . $ext;
                $target = __DIR__ . '/../assets/uploads/' . $newname;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                    $photo_path = 'assets/uploads/' . $newname;
                }
            } else {
                $error = "Only JPG and PNG photos are allowed.";
            }
        }

        if ($error === "") {
            $stmt = $conn->prepare("INSERT INTO ServiceRequest 
                (CustomerID, ServiceID, PreferredDate, PreferredTime, Address, Description, PhotoPath, IsEmergency, Status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->bind_param("iisssssi", $customer_id, $service_id, $preferred_date, $preferred_time, $address, $description, $photo_path, $is_emergency);

            if ($stmt->execute()) {
                $success = "Service request submitted! You can track its status in 'My Requests'.";
            } else {
                $error = "Failed to submit request. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Service - HSMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="form-container">
        <h2>Book a Service</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <label>Service Category</label>
            <select name="service_id" required>
                <option value="">-- Select a service --</option>
                <?php while ($row = $services->fetch_assoc()): ?>
                    <option value="<?= $row['ServiceID'] ?>">
                        <?= htmlspecialchars($row['ServiceName']) ?> (Rs. <?= number_format($row['Price'], 2) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Your Address</label>
            <textarea name="address" rows="2" required></textarea>

            <label>Describe the Problem</label>
            <textarea name="description" rows="4" placeholder="e.g. TV screen flickering, no sound"></textarea>

            <label>Upload a Photo (optional)</label>
            <input type="file" name="photo" accept=".jpg,.jpeg,.png">

            <label>Preferred Date</label>
            <input type="date" name="preferred_date" required min="<?= date('Y-m-d') ?>">

            <label>Preferred Time</label>
            <input type="time" name="preferred_time">

            <label class="checkbox-label">
                <input type="checkbox" name="is_emergency" value="1">
                This is an emergency / urgent request
            </label>

            <button type="submit" class="btn-large">Submit Request</button>
        </form>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
