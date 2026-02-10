<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h1>Starting Comprehensive Database Fix...</h1>";

    // 1. RE-CREATE CATEGORIES WITH VERIFIED IDs AND IMAGES
    echo "<h3>1. Fixing Categories...</h3>";
    $cats = [
        ['id' => 1, 'name' => 'Dogs', 'image' => 'https://images.unsplash.com/photo-1534361960057-19889db9621e?auto=format&fit=crop&w=400&q=80'],
        ['id' => 2, 'name' => 'Cats', 'image' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=400&q=80'],
        ['id' => 3, 'name' => 'Birds', 'image' => 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?auto=format&fit=crop&w=400&q=80'],
        ['id' => 4, 'name' => 'Rabbits', 'image' => 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=400&q=80']
    ];

    foreach ($cats as $c) {
        // Try to update existing first (by ID or Name)
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$c['name']]);
        
        if ($stmt->rowCount() > 0) {
            $catId = $stmt->fetchColumn();
            $upd = $pdo->prepare("UPDATE categories SET image = ? WHERE id = ?");
            $upd->execute([$c['image'], $catId]);
            echo "Updated category {$c['name']} (ID: $catId).<br>";
        } else {
            // Insert
             $ins = $pdo->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
             $ins->execute([$c['name'], $c['image']]);
             echo "Created category {$c['name']}.<br>";
        }
    }

    // Map Names to IDs
    $catMap = [];
    $stmt = $pdo->query("SELECT id, name FROM categories");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $catMap[strtolower($row['name'])] = $row['id'];
    }

    // 2. DEFINE MASTER LIST OF PETS
    // This includes EVERY pet mentioned by the user to ensure images are fixed.
    echo "<h3>2. Fixing Pet Images and Data...</h3>";
    
    $pets = [
        // DOGS
        ['name' => 'Bella', 'cat' => 'dogs', 'img' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600&q=80', 'desc' => 'Ray of sunshine.'],
        ['name' => 'Max', 'cat' => 'dogs', 'img' => 'https://images.unsplash.com/photo-1589941013453-ec89f33b5e95?auto=format&fit=crop&w=600&q=80', 'desc' => 'Loyal German Shepherd.'],
        ['name' => 'Daisy', 'cat' => 'dogs', 'img' => 'https://images.unsplash.com/photo-1537151625747-768eb6cf92b2?auto=format&fit=crop&w=600&q=80', 'desc' => 'Sweet Beagle.'],
        ['name' => 'Teddy', 'cat' => 'dogs', 'img' => 'https://images.unsplash.com/photo-1516934024742-b461fba47600?auto=format&fit=crop&w=600&q=80', 'desc' => 'Hypoallergenic Poodle.'],
        ['name' => 'Charlie', 'cat' => 'dogs', 'img' => 'https://images.unsplash.com/photo-1605897472359-8dd3632f7489?auto=format&fit=crop&w=600&q=80', 'desc' => 'Playful Lab Mix.'], 
        
        // CATS
        ['name' => 'Oliver', 'cat' => 'cats', 'img' => 'https://images.unsplash.com/photo-1513245543132-31f507417b26?auto=format&fit=crop&w=600&q=80', 'desc' => 'Vocal Siamese.'],
        ['name' => 'Milo', 'cat' => 'cats', 'img' => 'https://images.unsplash.com/photo-1519052537078-e6302a4968d4?auto=format&fit=crop&w=600&q=80', 'desc' => 'Mischievous Tabby.'],
        ['name' => 'Simba', 'cat' => 'cats', 'img' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80', 'desc' => 'Royal Persian.'],
        ['name' => 'Luna Cat', 'cat' => 'cats', 'img' => 'https://images.unsplash.com/photo-1511044568932-338cba0fb803?auto=format&fit=crop&w=600&q=80', 'desc' => 'Sleek Bombay.'],
        ['name' => 'Luna', 'cat' => 'cats', 'img' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80', 'desc' => 'Sweet cat.'], // Handle "Luna" ambiguity, let's assume cat or rabbit. Original seed had Luna as Rabbit. Let's fix Luna Rabbit too.

        // RABBITS
        ['name' => 'Oreo', 'cat' => 'rabbits', 'img' => 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=600&q=80', 'desc' => 'Dutch Rabbit.'],
        ['name' => 'Thumper', 'cat' => 'rabbits', 'img' => 'https://images.unsplash.com/photo-1518796745738-41048802f99a?auto=format&fit=crop&w=600&q=80', 'desc' => 'Lop Eared Sweetheart.'],
        ['name' => 'Snowball', 'cat' => 'rabbits', 'img' => 'https://images.unsplash.com/photo-1535241554-2dff4043020c?auto=format&fit=crop&w=600&q=80', 'desc' => 'Fluffy Angora.'],
        ['name' => 'BunBun', 'cat' => 'rabbits', 'img' => 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?auto=format&fit=crop&w=600&q=80', 'desc' => 'Rex Rabbit.'],
        ['name' => 'Luna', 'cat' => 'rabbits', 'img' => 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=600&q=80', 'desc' => 'Our original Luna Rabbit.'], // Explicitly handling Luna Rabbit

        // BIRDS
        ['name' => 'Tweety', 'cat' => 'birds', 'img' => 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?auto=format&fit=crop&w=600&q=80', 'desc' => 'Singing Canary.'],
        ['name' => 'Blue', 'cat' => 'birds', 'img' => 'https://images.unsplash.com/photo-1452570053594-1b985d6ea218?auto=format&fit=crop&w=600&q=80', 'desc' => 'Smart Parakeet.'],
        ['name' => 'Kiwi', 'cat' => 'birds', 'img' => 'https://images.unsplash.com/photo-1555169062-013468b47731?auto=format&fit=crop&w=600&q=80', 'desc' => 'Loving Lovebird.'],
        ['name' => 'Charlie Bird', 'cat' => 'birds', 'img' => 'https://images.unsplash.com/photo-1610878180933-123728745d22?auto=format&fit=crop&w=600&q=80', 'desc' => 'Whistling Cockatiel.'],
        ['name' => 'Rio', 'cat' => 'birds', 'img' => 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?auto=format&fit=crop&w=600&q=80', 'desc' => 'Magnificent Macaw.']
    ];

    $check = $pdo->prepare("SELECT id FROM pets WHERE name = ?");
    $update = $pdo->prepare("UPDATE pets SET image = ?, category_id = ? WHERE name = ?");

    foreach ($pets as $p) {
        $catId = $catMap[$p['cat']] ?? 1;
        
        // 1. Check if exists
        $check->execute([$p['name']]);
        if ($check->rowCount() > 0) {
            // Update
            $update->execute([$p['img'], $catId, $p['name']]);
            echo "Fixed/Updated pet: <b>{$p['name']}</b> (Category: {$p['cat']}, ID: $catId)<br>";
        } else {
            // If missing but requested, we could insert, but let's stick to fixing existing first or assume main seed script did inserts.
            // Let's Insert if missing to be safe!
            $ins = $pdo->prepare("INSERT INTO pets (name, category_id, breed, age, gender, color, price, description, behavior, care_pattern, is_rescued, image, status) VALUES (?, ?, 'Unknown Mix', '1 year', 'Male', 'Mixed', 0, ?, 'Friendly', 'Normal care', 0, ?, 'available')");
            $ins->execute([$p['name'], $catId, $p['desc'], $p['img']]);
            echo "Refilled missing pet: <b>{$p['name']}</b><br>";
        }
    }

    echo "<h3>3. Clean Up Data</h3>";
    // Ensure no broken categories
    $pdo->query("UPDATE pets SET category_id = 1 WHERE category_id IS NULL");
    echo "Ensured no null categories.<br>";

    echo "<h2>DONE! Go to <a href='index.php'>Home Page</a></h2>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
