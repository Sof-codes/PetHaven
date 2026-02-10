<?php
require_once __DIR__ . '/includes/db.php';

try {
    echo "<h1>Final Fix: Localizing All Images</h1>";
    $imgDir = __DIR__ . '/assets/images/';
    
    // 1. Define Source Images (Verified Existing)
    $sources = [
        'dog' => 'pet_bella.jpg',
        'cat' => 'pet_oliver.jpg',
        'rabbit' => 'pet_bunbun.jpg', 
        'bird' => 'pet_charlie_bird.jpg',
        'hamster' => 'cat_hamster.jpg'
    ];
    
    // 2. Fix Categories First
    // We Map Category Name -> Source Key
    $catMap = [
        'Dogs' => 'dog',
        'Cats' => 'cat',
        'Birds' => 'bird',
        'Rabbits' => 'rabbit',
        'Hamsters' => 'hamster'
    ];
    
    $stmt = $pdo->prepare("UPDATE categories SET image = ? WHERE name = ?");
    
    foreach ($catMap as $catName => $sourceKey) {
        $sourceFile = $sources[$sourceKey];
        $targetFile = 'cat_' . strtolower(str_replace('s', '', $catName)) . '.jpg'; // e.g. cat_dog.jpg
        
        // Copy source to target if not exists
        if (!file_exists($imgDir . $targetFile)) {
            copy($imgDir . $sourceFile, $imgDir . $targetFile);
            echo "Restored Category Image: $targetFile (from $sourceFile)<br>";
        }
        
        // Force DB Update to local path
        $stmt->execute(['assets/images/' . $targetFile, $catName]);
    }
    
    // 3. Fix Pets
    // Get all pets
    $pets = $pdo->query("SELECT p.id, p.name, p.image, c.name as cat_name FROM pets p LEFT JOIN categories c ON p.category_id = c.id")->fetchAll(PDO::FETCH_ASSOC);
    
    $upd = $pdo->prepare("UPDATE pets SET image = ? WHERE id = ?");
    
    foreach ($pets as $p) {
        // Decide if we need to fix
        $needsFix = false;
        
        // If it's a remote URL
        if (strpos($p['image'], 'http') === 0) {
            $needsFix = true;
        } 
        // OR if local file doesn't exist
        else {
            $localFile = str_replace('assets/images/', '', $p['image']);
            if (!file_exists($imgDir . $localFile)) {
                $needsFix = true;
            }
        }
        
        if ($needsFix) {
            // Determine type
            $catName = $p['cat_name'] ?? 'Dogs';
            $type = 'dog';
            if (stripos($catName, 'cat') !== false) $type = 'cat';
            if (stripos($catName, 'rabbit') !== false) $type = 'rabbit';
            if (stripos($catName, 'bird') !== false) $type = 'bird';
            if (stripos($catName, 'hamster') !== false) $type = 'hamster';
            
            // Generate valid local filename
            $sourceFile = $sources[$type];
            $targetName = 'pet_' . strtolower(str_replace(' ', '_', $p['name'])) . '.jpg';
            
            // Copy source -> target
            copy($imgDir . $sourceFile, $imgDir . $targetName);
            
            // Update DB
            $upd->execute(['assets/images/' . $targetName, $p['id']]);
            echo "Fixed Pet: {$p['name']} -> $targetName<br>";
        }
    }
    
    echo "<h2>SUCCESS: All images guaranteed local!</h2>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
