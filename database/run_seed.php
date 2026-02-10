<?php
// We need to connect to the server first. 
// If includes/db.php connects to a specific DB, it works IF the DB exists.
// The user's error says "Table pet_adoption_system.pets doesn't exist", so the DB exists.
require_once __DIR__ . '/../includes/db.php'; 

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>Setting up Database...</h3>";

    // 1. Create Tables
    $sql_tables = "
    -- Users Table
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Categories Table
    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        image VARCHAR(255)
    );

    -- Pets Table
    CREATE TABLE IF NOT EXISTS pets (
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
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    );

    -- Adoption Requests Table
    CREATE TABLE IF NOT EXISTS adoption_requests (
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

    $pdo->exec($sql_tables);
    echo "Tables created successfully.<br>";

    // 2. Insert Data (Seeding)

    // Categories
    $categories = [
        ['Dogs', 'assets/images/category-dog.png'],
        ['Cats', 'assets/images/category-cat.png'],
        ['Birds', 'assets/images/category-bird.png'],
        ['Rabbits', 'assets/images/category-rabbit.png']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, image) VALUES (?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "Categories seeded.<br>";

    // Get Category IDs
    $stmt = $pdo->query("SELECT id, name FROM categories");
    $cats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Pets
    $pets = [
        [
            'name' => 'Bella',
            'category_id' => $cats['Dogs'] ?? 1,
            'breed' => 'Golden Retriever',
            'age' => '2 years',
            'gender' => 'Female',
            'color' => 'Golden',
            'price' => 0.00,
            'description' => 'Bella is a ray of sunshine! She loves people and is great with kids. She was found wandering but is clearly house-trained.',
            'behavior' => 'Friendly, Playful, Gentle',
            'care_pattern' => 'Needs daily walks and brushing twice a week. Love high quality kibble.',
            'is_rescued' => 1,
            'image' => 'assets/images/pet-bella.jpg',
            'status' => 'available'
        ],
        [
            'name' => 'Oliver',
            'category_id' => $cats['Cats'] ?? 2,
            'breed' => 'Siamese',
            'age' => '1 year',
            'gender' => 'Male',
            'color' => 'Cream & Brown',
            'price' => 20.00,
            'description' => 'Oliver is a vocal and affectionate cat who loves to curl up in laps.',
            'behavior' => 'Chatty, Affectionate, Indoor-only',
            'care_pattern' => 'Litter box trained. Needs interactive play.',
            'is_rescued' => 1,
            'image' => 'assets/images/pet-oliver.jpg',
            'status' => 'available'
        ],
        [
            'name' => 'Charlie',
            'category_id' => $cats['Dogs'] ?? 1,
            'breed' => 'Labrador Mix',
            'age' => '3 months',
            'gender' => 'Male',
            'color' => 'Black',
            'price' => 50.00,
            'description' => 'A playful puppy full of energy. Needs a family with patience for puppy training.',
            'behavior' => 'Energetic, Curious, Teething',
            'care_pattern' => 'Puppy food, frequent potty breaks, chew toys.',
            'is_rescued' => 1,
            'image' => 'assets/images/pet-charlie.jpg',
            'status' => 'available'
        ],
        [
            'name' => 'Luna',
            'category_id' => $cats['Rabbits'] ?? 4,
            'breed' => 'Holland Lop',
            'age' => '8 months',
            'gender' => 'Female',
            'color' => 'Gray',
            'price' => 15.00,
            'description' => 'Luna is a sweet bunny who loves veggies and head scratches.',
            'behavior' => 'Quiet, Skittish at first, Sweet',
            'care_pattern' => 'Fresh hay daily, fresh veggies, indoor enclosure.',
            'is_rescued' => 0,
            'image' => 'assets/images/pet-luna.jpg',
            'status' => 'available'
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO pets (name, category_id, breed, age, gender, color, price, description, behavior, care_pattern, is_rescued, image, status) 
            VALUES (:name, :category_id, :breed, :age, :gender, :color, :price, :description, :behavior, :care_pattern, :is_rescued, :image, :status)");

    foreach ($pets as $pet) {
        // Simple duplicate check
        $check = $pdo->prepare("SELECT id FROM pets WHERE name = ?");
        $check->execute([$pet['name']]);
        if ($check->rowCount() == 0) {
            $stmt->execute($pet);
            echo "Added pet: " . $pet['name'] . "<br>";
        }
    }

    // Admin User
    $adminUser = 'admin';
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$adminUser]);
    if ($stmt->rowCount() == 0) {
        $pass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')")
            ->execute(['admin', 'admin@pethaven.com', $pass]);
        echo "Admin user created (admin / admin123)<br>";
    }

    echo "<h3>Setup Complete! <a href='../index.php'>Go to Home</a></h3>";

} catch (PDOException $e) {
    echo "<h1>Error</h1>";
    echo "Message: " . $e->getMessage();
}
?>
