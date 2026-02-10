<?php
require_once 'includes/db.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <h1>Find a loving home, <br> <span style="color: var(--color-primary);">Adopt a loving soul.</span></h1>
            <p>Thousands of rescued pets are waiting for a family. Give them a second chance at happiness and find your new best friend today.</p>
            <div style="display: flex; gap: 15px;">
                <a href="pets.php" class="btn btn-primary">Adopt Now</a>
                <a href="#how-it-works" class="btn btn-outline">How it Works</a>
            </div>
        </div>
            <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=800&q=80" alt="Happy Pets" style="width: 100%; height: 400px; object-fit: cover; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
    </div>
</section>

<!-- Categories Section -->
<section class="section-padding" id="categories">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="mb-2">Find Your Perfect Companion</h2>
            <p>Browse pets by category</p>
        </div>

        <div class="categories-grid">
            <?php
            // Fetch categories
            $stmt = $pdo->query("SELECT * FROM categories");
            while($cat = $stmt->fetch(PDO::FETCH_ASSOC)):
                // Icon mapping based on name
                $iconClass = 'fa-paw';
                if(strtolower($cat['name']) == 'dogs') $iconClass = 'fa-dog';
                if(strtolower($cat['name']) == 'cats') $iconClass = 'fa-cat';
                if(strtolower($cat['name']) == 'birds') $iconClass = 'fa-dove';
                if(strtolower($cat['name']) == 'rabbits') $iconClass = 'fa-carrot'; // close enough
                if(strtolower($cat['name']) == 'hamsters') $iconClass = 'fa-cookie'; // Hamster cookie?
            ?>
            <a href="pets.php?category=<?php echo $cat['id']; ?>" style="text-decoration: none; color: inherit;">
                <div class="category-card">
                    <i class="fa-solid <?php echo $iconClass; ?> category-icon" style="color: var(--color-primary);"></i>
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Featured Pets Section -->
<section class="section-padding" style="background-color: var(--color-white);">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="mb-2">Pets Waiting for Adoption</h2>
            <p>Meet some of our newest arrivals looking for a forever home.</p>
        </div>

        <div class="pets-grid">
            <?php
            // Fetch latest 4 pets
            $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM pets p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'available' ORDER BY p.created_at DESC LIMIT 4");
            while($pet = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
            <div class="pet-card">
                <?php if($pet['is_rescued']): ?>
                    <span class="pet-badge">Rescued</span>
                <?php endif; ?>
                
                <!-- Placeholder Image Logic -->
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
            <?php endwhile; ?>
        </div>
        
        <div class="text-center" style="margin-top: 50px;">
            <a href="pets.php" class="btn btn-secondary">View All Pets <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- Why Adopt Section -->
<section class="section-padding" id="why-adopt">
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
            </div>
            <div style="order: 1;">
                 <img src="https://images.unsplash.com/photo-1570018144715-43110363d70a?auto=format&fit=crop&w=800&q=80" alt="Dog Paw" style="width: 100%; height: 400px; object-fit: cover; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
            </div>
        </div>
    </div>
    </div>
</section>

<!-- About Us Section -->
<section class="section-padding" id="about" style="background-color: var(--color-light);">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1599443015574-be5fe8a05783?auto=format&fit=crop&w=800&q=80" alt="Pet Shelter" style="width: 100%; height: 400px; object-fit: cover; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
            </div>
            <div class="hero-content">
                <h2 class="mb-4">About PetHaven Shelter</h2>
                <p class="mb-3">Founded in 2020, PetHaven has been dedicated to rescuing, rehabilitating, and rehoming abandoned and neglected animals. We believe every soul deserves a second chance at happiness.</p>
                <p class="mb-4">Our team of dedicated volunteers works tirelessly to ensure that every pet receives medical care, love, and training before finding their forever families. We are more than just a shelter; we are a community of animal lovers.</p>
                
                <ul style="list-style: none; padding: 0; margin-bottom: 20px;">
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i> Over 500+ successful adoptions</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i> 100% No-kill policy</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i> Post-adoption support</li>
                </ul>
                
                <a href="#contact" class="btn btn-primary">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section-padding" style="background-color: var(--color-white); text-align: center;">
    <div class="container">
        <h2 class="mb-4">Why Choose PetHaven?</h2>
        <div class="categories-grid">
            <div class="category-card" style="cursor: default;">
                <i class="fa-solid fa-check-circle category-icon" style="color: var(--color-success);"></i>
                <h3>Verified Shelters</h3>
                <p>We work with trusted NGOs and shelters.</p>
            </div>
            <div class="category-card" style="cursor: default;">
                <i class="fa-solid fa-lock category-icon" style="color: var(--color-accent);"></i>
                <h3>Secure Process</h3>
                <p>Your data and adoption journey are safe.</p>
            </div>
            <div class="category-card" style="cursor: default;">
                <i class="fa-solid fa-notes-medical category-icon" style="color: var(--color-primary);"></i>
                <h3>Health Checked</h3>
                <p>All pets are vaccinated and checked.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
