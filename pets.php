<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Build Query
$where = "p.status = 'available'";
$params = [];

if(isset($_GET['category']) && !empty($_GET['category'])) {
    $where .= " AND p.category_id = ?";
    $params[] = $_GET['category'];
}

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND (p.name LIKE ? OR p.breed LIKE ?)";
    $srch = "%".$_GET['search']."%";
    $params[] = $srch;
    $params[] = $srch;
}

$sql = "SELECT p.*, c.name as category_name FROM pets p LEFT JOIN categories c ON p.category_id = c.id WHERE $where ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container section-padding">
    <div class="text-center mb-4">
        <h2>Find Your New Best Friend</h2>
        <p>Browse our available pets looking for a loving home.</p>
    </div>

    <!-- Search/Filter Bar -->
    <div style="background: var(--color-white); padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-card); margin-bottom: 40px;">
        <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Search by name or breed..." class="form-control" style="flex: 2;" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            
            <select name="category" class="form-control" style="flex: 1;" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php
                $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
                foreach($cats as $c) {
                    $selected = (isset($_GET['category']) && $_GET['category'] == $c['id']) ? 'selected' : '';
                    echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                }
                ?>
            </select>
            
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="pets.php" class="btn btn-outline" style="text-decoration: none; display: flex; align-items: center;">Reset</a>
        </form>
    </div>

    <?php if(count($pets) > 0): ?>
        <div class="pets-grid">
            <?php foreach($pets as $pet): ?>
            <div class="pet-card">
                <?php if($pet['is_rescued']): ?>
                    <span class="pet-badge">Rescued</span>
                <?php endif; ?>
                
                 <div class="pet-image">
                    <img src="<?php echo $pet['image']; ?>?v=2" alt="<?php echo $pet['name']; ?>" style="width: 100%; height: 250px; object-fit: cover;">
                </div>
                
                <div class="pet-info">
                    <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>
                    <p class="pet-breed"><?php echo htmlspecialchars($pet['breed']); ?></p>
                    
                    <div style="margin-bottom: 15px;">
                        <span class="tag"><i class="fa-solid fa-venus-mars"></i> <?php echo $pet['gender']; ?></span>
                        <span class="tag"><i class="fa-regular fa-clock"></i> <?php echo $pet['age']; ?></span>
                    </div>
                    
                    <a href="pet_details.php?id=<?php echo $pet['id']; ?>" class="btn btn-outline" style="width: 100%; text-align: center;">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center" style="padding: 50px;">
            <h3>No pets found matching your criteria.</h3>
            <p>Try clearing filters or check back later!</p>
            <a href="pets.php" class="btn btn-secondary mt-3">Clear Filters</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
