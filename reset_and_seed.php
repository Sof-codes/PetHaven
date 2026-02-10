<?php
// Custom connection to ensure DB creation
$host = 'localhost';
$username = 'root';
$password = ''; // Default XAMPP

try {
    // 1. Connect to MySQL Server (no DB)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS pet_adoption_system");
    $pdo->exec("USE pet_adoption_system");
    
    echo "<h1>Database Connected/Created. Starting Reset...</h1>\n";

} catch(PDOException $e) {
    die("Setup Error: " . $e->getMessage());
}

// Disable time limit for downloads
set_time_limit(300);

function downloadImage($url, $savePath) {
    if (file_exists($savePath) && filesize($savePath) > 0) {
        echo "File already exists: " . basename($savePath) . " (Skipping Download)<br>\n";
        return true;
    }

    echo "Downloading $url to $savePath ... ";
    $ch = curl_init($url);
    $fp = fopen($savePath, 'wb');
    if (!$fp) {
        echo "FAILED (File Open Error)<br>\n";
        return false;
    }
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Fix for local SSL issues
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    fclose($fp);
    
    if ($httpCode == 200 && filesize($savePath) > 0) {
        echo "OK<br>\n";
        return true;
    } else {
        echo "FAILED (HTTP $httpCode, $error)<br>\n";
        @unlink($savePath); 
        return false;
    }
}

try {
    echo "<h1>Starting Hard Reset & Seed...</h1>\n";
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. DROP Tables
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS adoption_requests");
    $pdo->exec("DROP TABLE IF EXISTS pets");
    $pdo->exec("DROP TABLE IF EXISTS categories");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Tables Dropped.<br>\n";

    // 2. CREATE Tables
    $sql = "
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        image VARCHAR(255)
    );

    CREATE TABLE pets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        category_id INT,
        breed VARCHAR(100),
        age VARCHAR(20),
        gender ENUM('Male', 'Female'),
        color VARCHAR(50),
        price DECIMAL(10, 2) DEFAULT 0.00,
        description TEXT,
        behavior TEXT,
        care_pattern TEXT,
        is_rescued BOOLEAN DEFAULT FALSE,
        image VARCHAR(255),
        status ENUM('available', 'adopted') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    );

    CREATE TABLE adoption_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        pet_id INT,
        message TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
    );
    ";
    $pdo->exec($sql);
    echo "Tables Created.<br>\n";

    // 3. Admin User
    $pass = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')")
        ->execute(['admin', 'admin@pethaven.com', $pass]);
    echo "Admin Created.<br>\n";

    // 4. Categories & Images
    $imgDir = __DIR__ . '/assets/images/';
    if (!file_exists($imgDir)) mkdir($imgDir, 0777, true);

    $categories = [
        ['Dogs', 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=600&q=80', 'cat_dog.jpg'],
        ['Cats', 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80', 'cat_cat.jpg'],
        ['Birds', 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?auto=format&fit=crop&w=600&q=80', 'cat_bird.jpg'],
        ['Rabbits', 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=600&q=80', 'cat_rabbit.jpg'],
        ['Hamsters', 'https://images.unsplash.com/photo-1425082661705-1834bfd09dca?auto=format&fit=crop&w=600&q=80', 'cat_hamster.jpg']
    ];

    $catMap = [];
    foreach ($categories as $c) {
        $local = 'assets/images/' . $c[2];
        if (!downloadImage($c[1], $imgDir . $c[2])) {
            $local = $c[1]; // Fallback to URL
        }
        $pdo->prepare("INSERT INTO categories (name, image) VALUES (?, ?)")->execute([$c[0], $local]);
        $catMap[strtolower($c[0])] = $pdo->lastInsertId();
    }

    // 5. Pets & Images
    $pets = [
        // DOGS
        ['Bella', 'dogs', 'https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600&q=80', 'pet_bella.jpg', 'Friendly Golden.', 'Golden Retriever', 'Gold', 0, 1, 'Female', '2 years'],
        ['Max', 'dogs', 'https://images.unsplash.com/photo-1589941013453-ec89f33b5e95?auto=format&fit=crop&w=600&q=80', 'pet_max.jpg', 'Loyal German Shepherd.', 'German Shepherd', 'Black/Tan', 8000, 1, 'Male', '3 years'],
        ['Daisy', 'dogs', 'https://images.unsplash.com/photo-1537151625747-768eb6cf92b2?auto=format&fit=crop&w=600&q=80', 'pet_daisy.jpg', 'Sweet Beagle.', 'Beagle', 'Tri-color', 6000, 0, 'Female', '2 years'],
        ['Teddy', 'dogs', 'https://images.unsplash.com/photo-1516934024742-b461fba47600?auto=format&fit=crop&w=600&q=80', 'pet_teddy.jpg', 'Cute Poodle.', 'Toy Poodle', 'Apricot', 12000, 0,  'Male', '1 year'],
        ['Charlie', 'dogs', 'https://images.unsplash.com/photo-1605897472359-8dd3632f7489?auto=format&fit=crop&w=600&q=80', 'pet_charlie.jpg', 'Playful Puppy.', 'Lab Mix', 'Black', 3000, 1, 'Male', '4 months'],
        
        // CATS
        ['Oliver', 'cats', 'https://images.unsplash.com/photo-1513245543132-31f507417b26?auto=format&fit=crop&w=600&q=80', 'pet_oliver.jpg', 'Vocal Siamese.', 'Siamese', 'Cream', 2000, 1,  'Male', '1 year'],
        ['Milo', 'cats', 'https://images.unsplash.com/photo-1519052537078-e6302a4968d4?auto=format&fit=crop&w=600&q=80', 'pet_milo.jpg', 'Tabby Kitten.', 'Tabby', 'Orange', 1500, 0, 'Male', '5 months'],
        ['Simba', 'cats', 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80', 'pet_simba.jpg', 'Fluffy Persian.', 'Persian', 'Ginger', 5000, 0, 'Male', '3 years'],
        ['Luna Cat', 'cats', 'https://images.unsplash.com/photo-1511044568932-338cba0fb803?auto=format&fit=crop&w=600&q=80', 'pet_luna_cat.jpg', 'Sleek Bombay.', 'Bombay', 'Black', 2500, 1, 'Female', '2 years'],
        
        // RABBITS
        ['Oreo', 'rabbits', 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=600&q=80', 'pet_oreo.jpg', 'Dutch Rabbit.', 'Dutch', 'Black/White', 1200, 0, 'Male', '10 months'],
        ['Thumper', 'rabbits', 'https://images.unsplash.com/photo-1518796745738-41048802f99a?auto=format&fit=crop&w=600&q=80', 'pet_thumper.jpg', 'Lop Eared.', 'Holland Lop', 'Grey', 1500, 0, 'Male', '5 months'],
        ['Snowball', 'rabbits', 'https://images.unsplash.com/photo-1535241554-2dff4043020c?auto=format&fit=crop&w=600&q=80', 'pet_snowball.jpg', 'White Angora.', 'Angora', 'White', 2000, 0, 'Female', '1 year'],
        ['BunBun', 'rabbits', 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?auto=format&fit=crop&w=600&q=80', 'pet_bunbun.jpg', 'Rex Rabbit.', 'Rex', 'Brown', 1000, 1, 'Male', '8 months'],
        ['Cinnamon', 'rabbits', 'https://images.unsplash.com/photo-1472648750383-a22ff831fcce?auto=format&fit=crop&w=600&q=80', 'pet_cinnamon.jpg', 'Cute Bunny.', 'Mixed', 'Cinnamon', 800, 0, 'Female', '6 months'], 
        ['Luna', 'rabbits', 'https://images.unsplash.com/photo-1589578228447-e1a4e481c6c8?auto=format&fit=crop&w=600&q=80', 'pet_luna_rabbit.jpg', 'Original Luna.', 'Dwarf', 'Grey', 900, 1, 'Female', '1 year'],

        // BIRDS
        ['Tweety', 'birds', 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?auto=format&fit=crop&w=600&q=80', 'pet_tweety.jpg', 'Yellow Canary.', 'Canary', 'Yellow', 800, 0, 'Female', '2 years'],
        ['Blue', 'birds', 'https://images.unsplash.com/photo-1452570053594-1b985d6ea218?auto=format&fit=crop&w=600&q=80', 'pet_blue.jpg', 'Smart Parakeet.', 'Parakeet', 'Blue', 1200, 0, 'Male', '6 months'],
        ['Kiwi', 'birds', 'https://images.unsplash.com/photo-1555169062-013468b47731?auto=format&fit=crop&w=600&q=80', 'pet_kiwi.jpg', 'Lovebird.', 'Lovebird', 'Green', 1800, 1, 'Female', '1 year'],
        ['Charlie Bird', 'birds', 'https://images.unsplash.com/photo-1610878180933-123728745d22?auto=format&fit=crop&w=600&q=80', 'pet_charlie_bird.jpg', 'Cockatiel.', 'Cockatiel', 'Grey', 2500, 0, 'Male', '2 years'],
        ['Rio', 'birds', 'https://images.unsplash.com/photo-1544377033-69022791409f?auto=format&fit=crop&w=600&q=80', 'pet_rio.jpg', 'Blue Macaw.', 'Macaw', 'Blue', 15000, 1, 'Male', '4 years'],
        ['Sunny', 'birds', 'https://images.unsplash.com/photo-1549608276-5786777e6587?auto=format&fit=crop&w=600&q=80', 'pet_sunny.jpg', 'Conure.', 'Sun Conure', 'Orange/Yellow', 5000, 0, 'Male', '1 year'], 

        // NEW PETS
        ['Barnaby', 'rabbits', 'https://images.unsplash.com/photo-1518796745738-41048802f99a?auto=format&fit=crop&w=600&q=80', 'pet_lop_eared.jpg', 'Floppy Ears.', 'Lop Eared', 'Beige', 1600, 1, 'Male', '1 year'],
        ['Forest', 'rabbits', 'https://images.unsplash.com/photo-1472648750383-a22ff831fcce?auto=format&fit=crop&w=600&q=80', 'pet_wild_rabbit.jpg', 'Wild Rabbit.', 'Wild', 'Brown', 0, 1, 'Female', '2 years'],
        ['Squeaky', 'hamsters', 'https://images.unsplash.com/photo-1425082661705-1834bfd09dca?auto=format&fit=crop&w=600&q=80', 'pet_hamster_grey.jpg', 'Little Runner.', 'Hamster', 'Grey', 500, 0, 'Male', '6 months']
    ];

    $stmt = $pdo->prepare("INSERT INTO pets (name, category_id, breed, age, gender, color, price, description, behavior, care_pattern, is_rescued, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Friendly', 'Normal', ?, ?, 'available')");

    foreach ($pets as $p) {
        $catId = $catMap[strtolower($p[1])];
        $local = 'assets/images/' . $p[3];
        
        if (!downloadImage($p[2], $imgDir . $p[3])) {
            $local = $p[2]; // Fallback
        }
        
        $stmt->execute([
            $p[0], $catId, $p[5], $p[10], $p[9], $p[6], $p[7], $p[4], $p[8], $local
        ]);
        echo "Inserted {$p[0]}<br>\n";
    }
    
    echo "<h1>COMPLETE! Images Downloaded and DB Reset.</h1>";

} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage();
}
