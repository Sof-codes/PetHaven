<?php
/**
 * MAIN ROUTER (index.php)
 * This handles the display for different sections like Home, Find Pet, Why Adopt, and About Us.
 */
require_once 'includes/db.php';     // Database connection
require_once 'includes/header.php'; // Header navigation

// Determine which section to show (default: home)
$section = isset($_GET['section']) ? $_GET['section'] : 'home';

// Content Logic based on section
if ($section === 'home'): ?>
    <!-- HERO SECTION -->
    <header class="hero">
        <div class="container hero-grid">
            <div class="hero-content">
                <h1>Find a loving home, <br> <span style="color: var(--color-primary);">Adopt a loving soul.</span></h1>
                <p>Join our mission to provide every pet with the love and care they deserve. Your new best friend is just a click away.</p>
                <div style="display: flex; gap: 15px;">
                    <a href="index.php?section=find-pet" class="btn btn-primary">Find a Pet</a>
                    <a href="index.php?section=why-adopt" class="btn btn-outline">Why Adopt?</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="assets/images/pet_max.jpg" alt="Happy Dog" style="width: 100%; height: 400px; object-fit: cover; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
            </div>
        </div>
    </header>

    <!-- CATEGORIES SECTION -->
    <section class="section-padding container" id="categories">
        <div class="text-center mb-4">
            <h2 class="mb-2">Find Your Perfect Companion</h2>
            <p>Select a pet type to see who's available.</p>
        </div>
        <div class="categories-grid">
            <?php
            $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
            foreach($cats as $cat): 
                $iconClass = 'fa-paw';
                if(strtolower($cat['name']) == 'dogs') $iconClass = 'fa-dog';
                if(strtolower($cat['name']) == 'cats') $iconClass = 'fa-cat';
                if(strtolower($cat['name']) == 'birds') $iconClass = 'fa-dove';
                if(strtolower($cat['name']) == 'rabbits') $iconClass = 'fa-carrot';
            ?>
                <a href="index.php?section=find-pet&category=<?php echo urlencode($cat['name']); ?>" style="text-decoration: none; color: inherit;">
                    <div class="category-card">
                        <i class="fa-solid <?php echo $iconClass; ?> category-icon" style="color: var(--color-primary);"></i>
                        <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- WHY CHOOSE US SECTION -->
    <section class="section-padding" style="background: white;">
        <div class="container hero-grid" style="grid-template-columns: 1fr 1.2fr;">
            <div style="background: var(--color-warm-bg); padding: 40px; border-radius: var(--radius-lg);">
                <h2 class="mb-2">Why Choose PetHaven?</h2>
                <p class="mb-4">We are more than just a shelter; we are a community dedicated to animal welfare.</p>
                <div style="display: grid; gap: 20px;">
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <i class="fa-solid fa-heart-pulse fa-2x" style="color: var(--color-primary);"></i>
                        <div>
                            <h4>Health Guaranteed</h4>
                            <p style="font-size: 0.9rem; color: var(--color-text-light);">All our pets undergo thorough health checks and vaccinations before adoption.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <i class="fa-solid fa-hand-holding-heart fa-2x" style="color: var(--color-primary);"></i>
                        <div>
                            <h4>Support Team</h4>
                            <p style="font-size: 0.9rem; color: var(--color-text-light);">We provide 24/7 post-adoption support to help you and your pet settle in.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <i class="fa-solid fa-shield-cat fa-2x" style="color: var(--color-primary);"></i>
                        <div>
                            <h4>Safe Process</h4>
                            <p style="font-size: 0.9rem; color: var(--color-text-light);">Our adoption legalities are transparent and designed for the pet's safety.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding-left: 40px;">
                <img src="assets/images/cat_cats.jpg" style="width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft);">
            </div>
        </div>
    </section>

<?php 
// FIND A PET SECTION
elseif ($section === 'find-pet'): ?>
    <section class="section-padding container">
        <div class="text-center mb-4">
            <h2 class="mb-2">Find Your New Best Friend</h2>
            <p>Browse our available pets looking for a loving home.</p>
        </div>

        <?php
        $where = "p.status = 'available'";
        $params = [];
        if(isset($_GET['category']) && !empty($_GET['category'])) {
            $where .= " AND p.category_name = ?";
            $params[] = $_GET['category'];
        }
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $where .= " AND (p.name LIKE ? OR p.breed LIKE ?)";
            $srch = "%".$_GET['search']."%";
            $params[] = $srch; $params[] = $srch;
        }

        $sql = "SELECT p.*, c.name as category_name FROM pets p LEFT JOIN categories c ON p.category_name = c.name WHERE $where ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $all_pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <!-- Filter Bar -->
        <div style="background: var(--color-warm-bg); padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-card); margin-bottom: 40px;">
            <form method="GET" action="index.php" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <input type="hidden" name="section" value="find-pet">
                <input type="text" name="search" placeholder="Search by name or breed..." class="form-control" style="flex: 2; background: white;" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                
                <select name="category" class="form-control" style="flex: 1; background: white;" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php
                    $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
                    foreach($cats as $c) {
                        $selected = (isset($_GET['category']) && $_GET['category'] == $c['name']) ? 'selected' : '';
                        echo "<option value='{$c['name']}' $selected>{$c['name']}</option>";
                    }
                    ?>
                </select>
                
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="index.php?section=find-pet" class="btn btn-outline" style="text-decoration: none; display: flex; align-items: center;">Reset</a>
            </form>
        </div>

        <?php if(count($all_pets) > 0): ?>
            <div class="pets-grid">
                <?php foreach($all_pets as $pet): ?>
                <div class="pet-card">
                    <?php if($pet['is_rescued']): ?>
                        <span class="pet-badge">Rescued</span>
                    <?php endif; ?>
                    
                    <div class="pet-image">
                        <img src="<?php echo $pet['image']; ?>" alt="<?php echo $pet['name']; ?>" style="width: 100%; height: 250px; object-fit: cover;">
                    </div>

                    <div class="pet-info">
                        <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>
                        <p class="pet-breed"><?php echo htmlspecialchars($pet['breed']); ?></p>
                        
                        <div style="margin-bottom: 15px;">
                            <span class="tag"><i class="fa-solid fa-venus-mars"></i> <?php echo $pet['gender']; ?></span>
                            <span class="tag"><i class="fa-regular fa-clock"></i> <?php echo $pet['age']; ?></span>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; color: var(--color-primary); font-size: 1.2rem;">
                                <?php echo $pet['price'] > 0 ? '₹'.number_format($pet['price'], 2) : 'Free Adoption'; ?>
                            </span>
                            <a href="pet_details.php?name=<?php echo urlencode($pet['name']); ?>" class="btn btn-primary" style="padding: 10px 20px;">Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center section-padding">
                <i class="fa-solid fa-magnifying-glass fa-3x mb-3" style="color: #ddd;"></i>
                <h3>No pets found</h3>
                <p>Try searching for something else or browse all categories.</p>
                <a href="index.php?section=find-pet" class="btn btn-primary mt-3">Reset Search</a>
            </div>
        <?php endif; ?>
    </section>

<?php 
// WHY ADOPT & ABOUT CONTENT
elseif ($section === 'why-adopt'): ?>
<section class="section-padding" id="why-adopt" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="hero-grid">
            <div style="order: 2;">
                <h2 class="mb-4">Why Adopt a Pet?</h2>
                
                <div style="margin-bottom: 30px;">
                    <h4 style="color: var(--color-primary); margin-bottom: 10px;"><i class="fa-solid fa-heart"></i> Save a Life</h4>
                    <p>When you adopt, you save a loving animal by making them part of your family and open up shelter space for another animal who might need it.</p>
                </div>

                <div style="margin-bottom: 30px;">
                     <h4 style="color: var(--color-primary); margin-bottom: 10px;"><i class="fa-solid fa-hand-holding-heart"></i> Unconditional Love</h4>
                    <p>Rescued pets are often the most grateful and loyal companions. They seem to know you saved them.</p>
                </div>

                <div>
                     <h4 style="color: var(--color-primary); margin-bottom: 10px;"><i class="fa-solid fa-ban"></i> Stop Puppy Mills</h4>
                    <p>Adoption is a way to fight against cruel breeding facilities. Be part of the solution, not the problem.</p>
                </div>
                
                <div class="mt-4">
                    <a href="index.php?section=find-pet" class="btn btn-primary">Browse available pets</a>
                </div>
            </div>
            <div style="order: 1;">
                 <img src="https://images.unsplash.com/photo-1570018144715-43110363d70a?auto=format&fit=crop&w=800&q=80" alt="Dog Paw" style="width: 100%; height: 500px; object-fit: cover; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
            </div>
        </div>
    </div>
</section>

<?php elseif ($section === 'about'): ?>
<section class="section-padding" id="about" style="min-height: 80vh; display: flex; align-items: center; background-color: var(--color-light);">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1599443015574-be5fe8a05783?auto=format&fit=crop&w=800&q=80" alt="Pet Shelter" style="width: 100%; height: 500px; object-fit: cover; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
            </div>
            <div class="hero-content">
                <h2 class="mb-4">About PetHaven Shelter</h2>
                <p class="mb-3">Founded in 2026, PetHaven has been dedicated to rescuing, rehabilitating, and rehoming abandoned and neglected animals. We believe every soul deserves a second chance at happiness.</p>
                <p class="mb-4">Our team of dedicated volunteers works tirelessly to ensure that every pet receives medical care, love, and training before finding their forever families. We are more than just a shelter; we are a community of animal lovers.</p>
                
                <ul style="list-style: none; padding: 0; margin-bottom: 30px;">
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i> Over 500+ successful adoptions</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i> 100% No-kill policy</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i> Post-adoption support</li>
                </ul>
                
                <a href="register.php" class="btn btn-primary">Join our community</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
