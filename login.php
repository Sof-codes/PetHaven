<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if(empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Allow login by email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role or previous intent
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

<div class="auth-container">
    <div class="text-center mb-4">
        <h2>Welcome Back</h2>
        <p>Login to continue your adoption journey</p>
    </div>

    <?php if($error): ?>
        <div style="background: var(--color-danger); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username or Email</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
    </form>

    <div class="text-center" style="margin-top: 20px;">
        <p>Don't have an account? <a href="register.php" style="color: var(--color-primary); font-weight: 600;">Sign Up</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
