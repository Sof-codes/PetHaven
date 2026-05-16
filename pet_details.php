<?php
/**
 * PET DETAILS PAGE
 * Displays detailed information about a specific pet.
 * Allows logged-in users to submit an adoption request.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';     // Database connection
require_once 'includes/header.php'; // Website header

/**
 * Validating the Pet Name from URL
 */
if(!isset($_GET['name'])) {
    echo "<div class='container section-padding'><h3>Pet not found!</h3></div>";
    require_once 'includes/footer.php';
    exit;
}

$pet_name = $_GET['name'];
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM pets p LEFT JOIN categories c ON p.category_name = c.name WHERE p.name = ?");
$stmt->execute([$pet_name]);
$pet = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$pet) {
    echo "<div class='container section-padding'><h3>Pet not found!</h3></div>";
    require_once 'includes/footer.php';
    exit;
}

// Handle Adoption Request
$msg = '';
$err = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adopt_request'])) {
    if(!isset($_SESSION['username'])) {
        header("Location: login.php?redirect=pet_details.php?name=" . urlencode($pet_name));
        exit;
    }
    
    $message = trim($_POST['message']);
    $username = $_SESSION['username'];
    
    // Check if already requested
    $check = $pdo->prepare("SELECT pet_name FROM adoption_requests WHERE user_username = ? AND pet_name = ?");
    $check->execute([$username, $pet_name]);
    
    if($check->rowCount() > 0) {
        $err = "You have already submitted a request for this pet.";
    } else {
        $ins = $pdo->prepare("INSERT INTO adoption_requests (user_username, pet_name, message) VALUES (?, ?, ?)");
        if($ins->execute([$username, $pet_name, $message])) {
            $msg = "Adoption request submitted successfully! The shelter will contact you soon.";
        } else {
            $err = "Something went wrong. Please try again.";
        }
    }
}
?>

<div class="container section-padding">
    <div class="hero-grid" style="grid-template-columns: 1fr 1.2fr; gap: 40px; align-items: flex-start;">
        <!-- Left: Image -->
        <div>
            <div style="position: relative; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); height: 500px;">
                  <img src="<?php echo $pet['image']; ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo $pet['name']; ?>">
                  <?php if($pet['status'] !== 'available'): ?>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold;">
                        <?php echo strtoupper($pet['status']); ?>
                    </div>
                  <?php endif; ?>
            </div>
        </div>

        <!-- Right: Details -->
        <div class="pet-details-content">
            <h1 style="font-size: 3rem; color: var(--color-primary); margin-bottom: 5px;"><?php echo htmlspecialchars($pet['name']); ?></h1>
            <p style="font-size: 1.2rem; color: #666; margin-bottom: 25px;"><?php echo htmlspecialchars($pet['breed']); ?> • <?php echo htmlspecialchars($pet['age']); ?> • <?php echo htmlspecialchars($pet['gender']); ?></p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div style="background: var(--color-warm-bg); padding: 20px; border-radius: var(--radius-md);">
                     <h4 style="font-size: 1.1rem; color: var(--color-primary); margin-bottom: 5px;">Personality</h4>
                     <p><?php echo htmlspecialchars($pet['behavior']); ?></p>
                </div>
                <div style="background: var(--color-warm-bg); padding: 20px; border-radius: var(--radius-md);">
                     <h4 style="font-size: 1.1rem; color: var(--color-primary); margin-bottom: 5px;">Care Level</h4>
                     <p><?php echo htmlspecialchars($pet['care_pattern']); ?></p>
                </div>
                <div style="background: var(--color-warm-bg); padding: 20px; border-radius: var(--radius-md);">
                      <h4 style="font-size: 1.1rem; color: var(--color-primary); margin-bottom: 5px;">Adoption Fee</h4>
                      <p><?php echo $pet['price'] > 0 ? '₹'.number_format($pet['price'], 2) : 'Free Adoption'; ?></p>
                 </div>
                <div style="background: var(--color-warm-bg); padding: 20px; border-radius: var(--radius-md);">
                      <h4 style="font-size: 1.1rem; color: var(--color-primary); margin-bottom: 5px;">Color</h4>
                      <p><?php echo htmlspecialchars($pet['color']); ?></p>
                 </div>
            </div>

            <div style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 10px;">About <?php echo htmlspecialchars($pet['name']); ?></h3>
                <p style="line-height: 1.6;"><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>
            </div>

            <?php if($msg): ?>
                <div style="background: var(--color-success); color: white; padding: 15px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>
            
            <?php if($err): ?>
                <div style="background: var(--color-danger); color: white; padding: 15px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <?php echo $err; ?>
                </div>
            <?php endif; ?>

            <?php if($pet['status'] == 'available'): ?>
                <div style="background: white; padding: 30px; border-radius: var(--radius-md); box-shadow: var(--shadow-card);">
                    <h3 class="mb-3">Start Adoption Journey</h3>
                    <form method="POST">
                        <div class="form-group mb-3">
                            <label style="display: block; margin-bottom: 8px;">Why do you want to adopt <?php echo htmlspecialchars($pet['name']); ?>?</label>
                            <textarea name="message" rows="4" class="form-control" placeholder="Tell us about yourself and your home..." required></textarea>
                        </div>
                        <button type="submit" name="adopt_request" class="btn btn-primary" style="width: 100%;">Submit Interest Request</button>
                    </form>
                </div>
            <?php else: ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: var(--radius-md); text-align: center;">
                    <p style="color: #888;">This pet has found a new home!</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
