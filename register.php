<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

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
        // Check availability
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if($stmt->rowCount() > 0) {
            $error = "Username or Email already exists.";
        } else {
            // Insert
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

<div class="auth-container">
    <div class="text-center mb-4">
        <h2>Join PetHaven</h2>
        <p>Create an account to adopt a pet</p>
    </div>

    <?php if($error): ?>
        <div style="background: var(--color-danger); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div style="background: var(--color-success); padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo $success; ?> <a href="login.php">Login here</a>.
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Sign Up</button>
    </form>

    <div class="text-center" style="margin-top: 20px;">
        <p>Already have an account? <a href="login.php" style="color: var(--color-primary); font-weight: 600;">Login</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
