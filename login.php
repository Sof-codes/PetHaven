<?php
/**
 * LOGIN PAGE
 */
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

/**
 * Handle Login Form Submission
 */
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if(empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if(isset($_GET['redirect'])) {
                header("Location: " . $_GET['redirect']);
            } elseif($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PetHaven</title>
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
                <h2 style="font-size: 1.8rem; margin-bottom: 8px;">Welcome Back</h2>
                <p style="color: var(--color-text-light);">Log in to manage your adoptions</p>
            </div>

            <?php if($error): ?>
                <div style="background: var(--color-danger); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username or Email</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter your username or email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1rem; margin-top: 10px;">Login</button>
            </form>

            <div class="text-center" style="margin-top: 25px; border-top: 1px solid #f0f0f0; padding-top: 20px;">
                <p style="font-size: 0.95rem;">Don't have an account? <a href="register.php" style="color: var(--color-primary); font-weight: 700;">Sign Up</a></p>
                <a href="index.php" style="display: inline-block; margin-top: 15px; font-size: 0.85rem; color: var(--color-text-light); text-transform: uppercase; letter-spacing: 1px;">&larr; Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
