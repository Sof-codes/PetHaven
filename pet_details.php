<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

if(!isset($_GET['id'])) {
    echo "<div class='container section-padding'><h3>Pet not found!</h3></div>";
    require_once 'includes/footer.php';
    exit;
}

$pet_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM pets p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$pet_id]);
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
    if(!isset($_SESSION['user_id'])) {
        header("Location: login.php?redirect=pet_details.php?id=$pet_id");
        exit;
    }
    
    $message = trim($_POST['message']);
    $user_id = $_SESSION['user_id'];
    
    // Check if already requested
    $check = $pdo->prepare("SELECT id FROM adoption_requests WHERE user_id = ? AND pet_id = ?");
    $check->execute([$user_id, $pet_id]);
    
    if($check->rowCount() > 0) {
        $err = "You have already submitted a request for this pet.";
    } else {
        $ins = $pdo->prepare("INSERT INTO adoption_requests (user_id, pet_id, message) VALUES (?, ?, ?)");
        if($ins->execute([$user_id, $pet_id, $message])) {
            $msg = "Adoption request submitted successfully! The shelter will contact you soon.";
        } else {
            $err = "Something went wrong. Please try again.";
        }
    }
}
?>

<div class="container section-padding">
    <div class="hero-grid" style="align-items: flex-start;">
        <!-- Left: Image -->
        <div>
            <div style="background-color: #f8f9fa; border-radius: var(--radius-lg); overflow: hidden; display: flex; align-items: center; justify-content: center; height: 400px; box-shadow: var(--shadow-soft);">
                  <img src="<?php echo $pet['image']; ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo $pet['name']; ?>">
            </div>
        </div>

        <!-- Right: Details -->
        <div>
            <h1 style="margin-bottom: 10px;"><?php echo htmlspecialchars($pet['name']); ?></h1>
            <p class="pet-breed" style="font-size: 1.2rem; margin-bottom: 20px;"><?php echo htmlspecialchars($pet['breed']); ?></p>

            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 30px;">
                <span class="tag" style="padding: 8px 15px; font-size: 1rem;"><i class="fa-solid fa-venus-mars"></i> <?php echo $pet['gender']; ?></span>
                <span class="tag" style="padding: 8px 15px; font-size: 1rem;"><i class="fa-regular fa-clock"></i> <?php echo $pet['age']; ?></span>
                <span class="tag" style="padding: 8px 15px; font-size: 1rem;"><i class="fa-solid fa-palette"></i> <?php echo $pet['color']; ?></span>
                <?php if($pet['is_rescued']): ?>
                    <span class="tag" style="background: var(--color-secondary); color: var(--color-text-main); padding: 8px 15px; font-size: 1rem;">Rescued</span>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 20px;">
                <h3 style="margin-bottom: 10px;">About <?php echo htmlspecialchars($pet['name']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                     <h4 style="font-size: 1.1rem; color: var(--color-primary); margin-bottom: 5px;">Behavior</h4>
                     <p><?php echo htmlspecialchars($pet['behavior']); ?></p>
                </div>
                <div>
                     <h4 style="font-size: 1.1rem; color: var(--color-primary); margin-bottom: 5px;">Care Needs</h4>
                     <p><?php echo htmlspecialchars($pet['care_pattern']); ?></p>
                </div>
                <div>
                     <h4 style="font-size: 1.1rem; color: var(--color-primary); margin-bottom: 5px;">Cost</h4>
                     <p><?php echo $pet['price'] > 0 ? '₹'.number_format($pet['price'], 2) : 'Free Adoption'; ?></p>
                </div>
            </div>

            <?php if($msg): ?>
                <div style="background: var(--color-success); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>
            
            <?php if($err): ?>
                <div style="background: var(--color-danger); color: white; padding: 15px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <?php echo $err; ?>
                </div>
            <?php endif; ?>

            <?php if($pet['status'] == 'available'): ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="message">Why do you want to adopt <?php echo htmlspecialchars($pet['name']); ?>?</label>
                        <textarea name="message" id="message" rows="3" class="form-control" placeholder="Share a bit about your home and experience..." required></textarea>
                    </div>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <button type="submit" name="adopt_request" class="btn btn-primary" style="width: 100%;">Submit Adoption Request</button>
                    <?php else: ?>
                        <a href="login.php?redirect=pet_details.php?id=<?php echo $pet_id; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">Login to Adopt</a>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary" disabled style="width: 100%;">Already Adopted</button>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
