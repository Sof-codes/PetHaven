<?php
/**
 * REGISTRATION PAGE
 */
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';

/**
 * Handle Registration Form Submission
 */
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT username FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if($stmt->rowCount() > 0) {
            $error = "Username or Email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if($stmt->execute([$username, $email, $hash])) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join PetHaven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-container" style="max-width: 450px;">
            <div class="text-center" style="margin-bottom: 25px;">
                <a href="index.php" style="font-size: 1.8rem; font-weight: 800; color: var(--color-primary); font-family: var(--font-heading); display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fa-solid fa-paw"></i> PetHaven
                </a>
            </div>
            <div class="text-center mb-4">
                <h2 style="font-size: 1.8rem; margin-bottom: 8px;">Create Account</h2>
                <p style="color: var(--color-text-light);">Start your adoption journey today</p>
            </div>

            <?php if($error): ?>
                <div style="background: var(--color-danger); color: white; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.9rem; text-align: center;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div style="background: var(--color-success); padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.9rem; text-align: center;">
                    <i class="fa-solid fa-circle-check"></i> <?php echo $success; ?> <a href="login.php" style="font-weight: 700; color: var(--color-text-main);">Login now</a>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Choose a unique username" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Repeat your password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1rem; margin-top: 10px;">Join PetHaven</button>
            </form>

            <div class="text-center" style="margin-top: 25px; border-top: 1px solid #f0f0f0; padding-top: 20px;">
                <p style="font-size: 0.95rem;">Already have an account? <a href="login.php" style="color: var(--color-primary); font-weight: 700;">Login</a></p>
                <a href="index.php" style="display: inline-block; margin-top: 15px; font-size: 0.85rem; color: var(--color-text-light); text-transform: uppercase; letter-spacing: 1px;">&larr; Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
