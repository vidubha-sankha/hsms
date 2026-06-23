<?php
require_once __DIR__ . '/../config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT CustomerID, Name, Password FROM Customer WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $customer = $result->fetch_assoc();
        if (password_verify($password, $customer['Password'])) {
            $_SESSION['customer_id'] = $customer['CustomerID'];
            $_SESSION['customer_name'] = $customer['Name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - HSMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Customer Login</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn-large">Login</button>
        </form>

        <p><a href="forgot_password.php">Forgot password?</a></p>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
