<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$requests = $pdo->prepare("
    SELECT r.*, p.name, p.image 
    FROM adoption_requests r 
    JOIN pets p ON r.pet_id = p.id 
    WHERE r.user_id = ? 
    ORDER BY r.request_date DESC
");
$requests->execute([$user_id]);
?>

<div class="container section-padding">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">My Adoption Requests</h2>
            
            <?php if($requests->rowCount() > 0): ?>
                <div style="display: grid; gap: 20px;">
                    <?php while($req = $requests->fetch(PDO::FETCH_ASSOC)): ?>
                    <div style="background: white; padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-card); display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <div style="width: 80px; height: 80px; border-radius: 10px; overflow: hidden; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <!-- <img src="<?php echo $req['image']; ?>" style="width: 100%; height: 100%; object-fit: cover;"> -->
                                <i class="fa-solid fa-paw fa-2x" style="color: #ddd;"></i>
                            </div>
                            <div>
                                <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($req['name']); ?></h3>
                                <p style="color: var(--color-text-light);">Requested on: <?php echo date('M d, Y', strtotime($req['request_date'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <span class="tag" style="padding: 8px 15px; background: <?php 
                                echo $req['status'] == 'approved' ? '#d4edda' : ($req['status'] == 'rejected' ? '#f8d7da' : '#fff3cd'); 
                            ?>; color: var(--color-text-main);">
                                <?php echo ucfirst($req['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center" style="padding: 40px; background: white; border-radius: var(--radius-md);">
                    <i class="fa-regular fa-folder-open fa-3x mb-3" style="color: var(--color-primary);"></i>
                    <h3>No requests yet</h3>
                    <p>You haven't submitted any adoption requests.</p>
                    <a href="pets.php" class="btn btn-primary mt-3">Find a Pet</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
