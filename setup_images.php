<?php
require_once __DIR__ . '/includes/db.php';

// Helper function to download image
function downloadImage($url, $savePath) {
    if (file_exists($savePath) && filesize($savePath) > 0) {
        return true; // Already downloaded
    }
    
    $ch = curl_init($url);
    $fp = fopen($savePath, 'wb');
    if (!$fp) {
        echo "Error: Cannot open file for writing: $savePath<br>";
        return false;
    }

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Add timeout
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL errors in dev
    
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    fclose($fp);
    
    if ($httpCode == 200 && filesize($savePath) > 0) {
        return true;
    } else {
        echo "Download failed code: $httpCode, error: $error <br>";
        @unlink($savePath); // Delete empty/failed file
        return false;
    }
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h1>Setting up Local Images & Adding More Pets...</h1>";

    // Create directory if not exists
    $imgDir = __DIR__ . '/assets/images/';
    if (!file_exists($imgDir)) {
        if (!mkdir($imgDir, 0777, true)) {
            die("Failed to create directory: $imgDir");
        }
    }

    // 1. Categories
    $categories = [
        ['Dogs', 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=600&q=80', 'cat_dog.jpg'],
        ['Cats', 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80', 'cat_cat.jpg'],
        ['Birds', 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?auto=format&fit=crop&w=600&q=80', 'cat_bird.jpg'],
        ['Rabbits', 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=600&q=80', 'cat_rabbit.jpg'],
        ['Hamsters', 'https://images.unsplash.com/photo-1425082661705-1834bfd09dca?auto=format&fit=crop&w=600&q=80', 'cat_hamster.jpg'] // NEW Category
    ];

    echo "<h3>Processing Categories...</h3>";
    $catMap = [];
    $catStmt = $pdo->prepare("INSERT INTO categories (name, image) VALUES (?, ?) ON DUPLICATE KEY UPDATE image = ?");
    $idStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");

    foreach ($categories as $c) {
        $localPath = 'assets/images/' . $c[2];
        $fullPath = $imgDir . $c[2];
        
        // Attempt download, but proceed even if it fails (using remote URL or placeholder if needed, logic here assumes local)
        if (downloadImage($c[1], $fullPath)) {
            $catStmt->execute([$c[0], $localPath, $localPath]);
            echo "Category: {$c[0]} -> Image Downloaded.<br>";
        } else {
             // If download fails, maybe use remote URL? Or just keep going.
             echo "Failed to download category image for {$c[0]}<br>";
             // Insert with remote URL as fallback? 
             $catStmt->execute([$c[0], $c[1], $c[1]]);
        }

        $idStmt->execute([$c[0]]);
        $catMap[strtolower($c[0])] = $idStmt->fetchColumn();
    }

    // 2. Pets
    // Structure: [Name, Category, ImageURL, Filename, Description, Breed, Color, Price, IsRescued]
    $pets = [
        // DOGS
        ['Bella', 'dogs', 'https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600&q=80', 'pet_bella.jpg', 'Friendly Golden.', 'Golden Retriever', 'Gold', 0, 1],
        ['Max', 'dogs', 'https://images.unsplash.com/photo-1589941013453-ec89f33b5e95?auto=format&fit=crop&w=600&q=80', 'pet_max.jpg', 'Loyal German Shepherd.', 'German Shepherd', 'Black/Tan', 8000, 1],
        ['Daisy', 'dogs', 'https://images.unsplash.com/photo-1537151625747-768eb6cf92b2?auto=format&fit=crop&w=600&q=80', 'pet_daisy.jpg', 'Sweet Beagle.', 'Beagle', 'Tri-color', 6000, 0],
        ['Teddy', 'dogs', 'https://images.unsplash.com/photo-1516934024742-b461fba47600?auto=format&fit=crop&w=600&q=80', 'pet_teddy.jpg', 'Cute Poodle.', 'Toy Poodle', 'Apricot', 12000, 0],
        ['Charlie', 'dogs', 'https://images.unsplash.com/photo-1605897472359-8dd3632f7489?auto=format&fit=crop&w=600&q=80', 'pet_charlie.jpg', 'Playful Puppy.', 'Lab Mix', 'Black', 3000, 1],
        ['Buster', 'dogs', 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&w=600&q=80', 'pet_buster.jpg', 'Strong Bulldog.', 'English Bulldog', 'White/Brown', 15000, 0], // NEW
        
        // CATS
        ['Oliver', 'cats', 'https://images.unsplash.com/photo-1513245543132-31f507417b26?auto=format&fit=crop&w=600&q=80', 'pet_oliver.jpg', 'Vocal Siamese.', 'Siamese', 'Cream', 2000, 1],
        ['Milo', 'cats', 'https://images.unsplash.com/photo-1519052537078-e6302a4968d4?auto=format&fit=crop&w=600&q=80', 'pet_milo.jpg', 'Tabby Kitten.', 'Tabby', 'Orange', 1500, 0],
        ['Simba', 'cats', 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80', 'pet_simba.jpg', 'Fluffy Persian.', 'Persian', 'Ginger', 5000, 0],
        ['Luna Cat', 'cats', 'https://images.unsplash.com/photo-1511044568932-338cba0fb803?auto=format&fit=crop&w=600&q=80', 'pet_luna_cat.jpg', 'Sleek Bombay.', 'Bombay', 'Black', 2500, 1],
        ['Whiskers', 'cats', 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?auto=format&fit=crop&w=600&q=80', 'pet_whiskers.jpg', 'Giant Coon.', 'Maine Coon', 'Grey Tabby', 8000, 0], // NEW
        
        // RABBITS
        ['Oreo', 'rabbits', 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=600&q=80', 'pet_oreo.jpg', 'Dutch Rabbit.', 'Dutch', 'Black/White', 1200, 0],
        ['Thumper', 'rabbits', 'https://images.unsplash.com/photo-1518796745738-41048802f99a?auto=format&fit=crop&w=600&q=80', 'pet_thumper.jpg', 'Lop Eared.', 'Holland Lop', 'Grey', 1500, 0],
        ['Snowball', 'rabbits', 'https://images.unsplash.com/photo-1535241554-2dff4043020c?auto=format&fit=crop&w=600&q=80', 'pet_snowball.jpg', 'White Angora.', 'Angora', 'White', 2000, 0],
        ['BunBun', 'rabbits', 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?auto=format&fit=crop&w=600&q=80', 'pet_bunbun.jpg', 'Rex Rabbit.', 'Rex', 'Brown', 1000, 1],
        ['Cinnamon', 'rabbits', 'https://images.unsplash.com/photo-1472648750383-a22ff831fcce?auto=format&fit=crop&w=600&q=80', 'pet_cinnamon.jpg', 'Cute Bunny.', 'Mixed', 'Cinnamon', 800, 0],
        ['Peter', 'rabbits', 'https://images.unsplash.com/photo-1589578228447-e1a4e481c6c8?auto=format&fit=crop&w=600&q=80', 'pet_peter.jpg', 'Wild Spirit.', 'Wild Rabbit', 'Brown', 0, 1], // NEW

        // BIRDS
        ['Tweety', 'birds', 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?auto=format&fit=crop&w=600&q=80', 'pet_tweety.jpg', 'Yellow Canary.', 'Canary', 'Yellow', 800, 0],
        ['Blue', 'birds', 'https://images.unsplash.com/photo-1452570053594-1b985d6ea218?auto=format&fit=crop&w=600&q=80', 'pet_blue.jpg', 'Smart Parakeet.', 'Parakeet', 'Blue', 1200, 0],
        ['Kiwi', 'birds', 'https://images.unsplash.com/photo-1555169062-013468b47731?auto=format&fit=crop&w=600&q=80', 'pet_kiwi.jpg', 'Lovebird.', 'Lovebird', 'Green', 1800, 1],
        ['Charlie Bird', 'birds', 'https://images.unsplash.com/photo-1610878180933-123728745d22?auto=format&fit=crop&w=600&q=80', 'pet_charlie_bird.jpg', 'Cockatiel.', 'Cockatiel', 'Grey', 2500, 0],
        ['Rio', 'birds', 'https://images.unsplash.com/photo-1544377033-69022791409f?auto=format&fit=crop&w=600&q=80', 'pet_rio.jpg', 'Blue Macaw.', 'Macaw', 'Blue', 15000, 1],
        ['Polly', 'birds', 'https://images.unsplash.com/photo-1522858547137-f1dcec554f55?auto=format&fit=crop&w=600&q=80', 'pet_polly.jpg', 'Vibrant Parrot.', 'Amazon Parrot', 'Green', 4000, 0], // NEW
        
        // HAMSTERS - NEW
        ['Nibbles', 'hamsters', 'https://images.unsplash.com/photo-1548767797-d8c844163c4b?auto=format&fit=crop&w=600&q=80', 'pet_nibbles.jpg', 'Tiny runner.', 'Syrian Hamster', 'Golden', 500, 0], // NEW
        ['Peanut', 'hamsters', 'https://images.unsplash.com/photo-1425082661705-1834bfd09dca?auto=format&fit=crop&w=600&q=80', 'pet_peanut.jpg', 'Pocket sized.', 'Dwarf Hamster', 'Grey', 400, 0], // NEW
    ];

    echo "<h3>Processing Pets...</h3>";
    
    $check = $pdo->prepare("SELECT id FROM pets WHERE name = ?");
    $insert = $pdo->prepare("INSERT INTO pets (name, category_id, breed, age, gender, color, price, description, behavior, care_pattern, is_rescued, image, status) VALUES (?, ?, ?, '1 year', 'Male', ?, ?, ?, 'Friendly', 'Standard care', ?, ?, 'available')");
    $update = $pdo->prepare("UPDATE pets SET image = ?, category_id = ?, description = ?, status = 'available' WHERE name = ?");

    foreach ($pets as $p) {
        $localPath = 'assets/images/' . $p[3];
        $fullPath = $imgDir . $p[3];
        $catId = $catMap[strtolower($p[1])] ?? 1;

        if (downloadImage($p[2], $fullPath)) {
             $check->execute([$p[0]]);
             if ($check->rowCount() > 0) {
                 $update->execute([$localPath, $catId, $p[4], $p[0]]);
                 echo "Updated pet: {$p[0]}<br>";
             } else {
                 $insert->execute([$p[0], $catId, $p[5], $p[6], $p[7], $p[4], $p[8], $localPath]);
                 echo "Created pet: {$p[0]}<br>";
             }
        } else {
            echo "Failed to download image for {$p[0]}. Using remote URL fallback.<br>";
            // Fallback to remote URL if download fails
             $check->execute([$p[0]]);
             if ($check->rowCount() > 0) {
                 $update->execute([$p[2], $catId, $p[4], $p[0]]);
             } else {
                 $insert->execute([$p[0], $catId, $p[5], $p[6], $p[7], $p[4], $p[8], $p[2]]);
             }
        }
    }

    echo "<h2>DONE! All images are processed. Go to <a href='index.php'>Home Page</a></h2>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
