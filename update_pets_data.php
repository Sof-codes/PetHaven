<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Starting Data Update with Verified Images...\n";

    // 1. Ensure All Categories Exist First with Reliable Images
    $categories = [
        'Dogs' => 'https://images.unsplash.com/photo-1534361960057-19889db9621e?auto=format&fit=crop&w=800&q=80',
        'Cats' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=800&q=80',
        'Birds' => 'https://images.unsplash.com/photo-1549608276-5786777e6587?auto=format&fit=crop&w=800&q=80',
        'Rabbits' => 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=800&q=80'
    ];

    $catStmt = $pdo->prepare("INSERT IGNORE INTO categories (name, image) VALUES (?, ?)");
    $catUpdate = $pdo->prepare("UPDATE categories SET image = ? WHERE name = ?");
    
    foreach ($categories as $name => $img) {
        $catStmt->execute([$name, $img]);
        $catUpdate->execute([$img, $name]); 
    }
    
    // 2. Build Category Map (NameLower -> ID)
    $catRows = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    $catMap = [];
    foreach($catRows as $row) {
        $catMap[strtolower($row['name'])] = $row['id'];
    }
    
    echo "Category Map Loaded.\n";

    function getCatId($name, $map) {
        return $map[strtolower($name)] ?? null;
    }

    // 3. Define Pets Data with High Res Images
    $allPets = [
        // DOGS
        [
            'name' => 'Bella',
            'category' => 'Dogs',
            'breed' => 'Golden Retriever',
            'age' => '2 years',
            'gender' => 'Female',
            'color' => 'Golden',
            'price' => 0.00,
            'description' => 'Bella is a ray of sunshine! She loves people and is great with kids.',
            'behavior' => 'Friendly, Playful',
            'care_pattern' => 'Daily walks',
            'is_rescued' => 1,
            'image' => 'https://images.unsplash.com/photo-1633722715463-d30f4f325e27?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Max',
            'category' => 'Dogs',
            'breed' => 'German Shepherd',
            'age' => '3 years',
            'gender' => 'Male',
            'color' => 'Black & Tan',
            'price' => 8000.00,
            'description' => 'Max is a loyal and protective German Shepherd. He is well-trained.',
            'behavior' => 'Loyal, Protective',
            'care_pattern' => 'Active exercise',
            'is_rescued' => 1,
            'image' => 'https://images.unsplash.com/photo-1589941013453-ec89f33b5e95?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Daisy',
            'category' => 'Dogs',
            'breed' => 'Beagle',
            'age' => '2 years',
            'gender' => 'Female',
            'color' => 'Tri-color',
            'price' => 6000.00,
            'description' => 'Daisy is a sweet hound with a great nose.',
            'behavior' => 'Friendly, gentle',
            'care_pattern' => 'Leash walking',
            'is_rescued' => 0,
            'image' => 'https://images.unsplash.com/photo-1505628346881-b72e27fae62b?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Teddy',
            'category' => 'Dogs',
            'breed' => 'Toy Poodle',
            'age' => '1 year',
            'gender' => 'Male',
            'color' => 'Apricot',
            'price' => 12000.00,
            'description' => 'Teddy is a hypoallergenic bundle of joy.',
            'behavior' => 'Smart, Active',
            'care_pattern' => 'Regular grooming',
            'is_rescued' => 0,
            'image' => 'https://images.unsplash.com/photo-1591768575198-88dac53fbd0a?auto=format&fit=crop&w=800&q=80'
        ],

        // CATS
        [
            'name' => 'Oliver',
            'category' => 'Cats',
            'breed' => 'Siamese',
            'age' => '1 year',
            'gender' => 'Male',
            'color' => 'Cream & Brown',
            'price' => 20.00,
            'description' => 'Oliver is a vocal and affectionate cat.',
            'behavior' => 'Chatty',
            'care_pattern' => 'Indoor only',
            'is_rescued' => 1,
            'image' => 'https://images.unsplash.com/photo-1513245543132-31f507417b26?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Milo',
            'category' => 'Cats',
            'breed' => 'Tabby',
            'age' => '5 months',
            'gender' => 'Male',
            'color' => 'Orange',
            'price' => 2000.00,
            'description' => 'Milo is a mischievous orange tabby kitten.',
            'behavior' => 'Playful',
            'care_pattern' => 'Lots of toys',
            'is_rescued' => 0,
            'image' => 'https://images.unsplash.com/photo-1519052537078-e6302a4968d4?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Simba',
            'category' => 'Cats',
            'breed' => 'Persian',
            'age' => '3 years',
            'gender' => 'Male',
            'color' => 'Ginger',
            'price' => 5000.00,
            'description' => 'The king of the house. Loves napping in sunbeams.',
            'behavior' => 'Lazy, Royal',
            'care_pattern' => 'Brushing needed',
            'is_rescued' => 1,
            'image' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Luna Cat',
            'category' => 'Cats',
            'breed' => 'Bombay',
            'age' => '2 years',
            'gender' => 'Female',
            'color' => 'Black',
            'price' => 1500.00,
            'description' => 'A sleek panther in miniature form.',
            'behavior' => 'Mysterious',
            'care_pattern' => 'Indoor only',
            'is_rescued' => 1,
            'image' => 'https://images.unsplash.com/photo-1511044568932-338cba0fb803?auto=format&fit=crop&w=800&q=80'
        ],
        
        // RABBITS
        [
            'name' => 'Oreo',
            'category' => 'Rabbits',
            'breed' => 'Dutch Rabbit',
            'age' => '10 months',
            'gender' => 'Male',
            'color' => 'Black & White',
            'price' => 1200.00,
            'description' => 'Oreo is a cute, small rabbit with classic Dutch markings.',
            'behavior' => 'Playful',
            'care_pattern' => 'Needs space to hop',
            'is_rescued' => 0,
            'image' => 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Thumper',
            'category' => 'Rabbits',
            'breed' => 'Lop Eared',
            'age' => '5 months',
            'gender' => 'Male',
            'color' => 'Grey',
            'price' => 1500.00,
            'description' => 'Thumper has the softest ears and loves carrots.',
            'behavior' => 'Gentle',
            'care_pattern' => 'Quiet environment',
            'is_rescued' => 1,
            'image' => 'https://images.unsplash.com/photo-1518796745738-41048802f99a?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Snowball',
            'category' => 'Rabbits',
            'breed' => 'Angora',
            'age' => '1 year',
            'gender' => 'Female',
            'color' => 'White',
            'price' => 2000.00,
            'description' => 'A fluffy cloud of joy.',
            'behavior' => 'Calm',
            'care_pattern' => 'Daily grooming',
            'is_rescued' => 0,
            'image' => 'https://images.unsplash.com/photo-1535241554-2dff4043020c?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'BunBun',
            'category' => 'Rabbits',
            'breed' => 'Rex',
            'age' => '8 months',
            'gender' => 'Male',
            'color' => 'Brown',
            'price' => 1000.00,
            'description' => 'Super soft velvet fur.',
            'behavior' => 'Curious',
            'care_pattern' => 'Indoor safe space',
            'is_rescued' => 1,
            'image' => 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?auto=format&fit=crop&w=800&q=80'
        ],

        // BIRDS
        [
            'name' => 'Tweety',
            'category' => 'Birds',
            'breed' => 'Canary',
            'age' => '2 years',
            'gender' => 'Female',
            'color' => 'Yellow',
            'price' => 800.00,
            'description' => 'Sings beautiful songs every morning.',
            'behavior' => 'Musical',
            'care_pattern' => 'Clean cage daily',
            'is_rescued' => 0,
            'image' => 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Blue',
            'category' => 'Birds',
            'breed' => 'Parakeet',
            'age' => '6 months',
            'gender' => 'Male',
            'color' => 'Blue',
            'price' => 1200.00,
            'description' => 'A smart little bird that can learn tricks.',
            'behavior' => 'Smart',
            'care_pattern' => 'Toys and interaction',
            'is_rescued' => 0,
            'image' => 'https://images.unsplash.com/photo-1452570053594-1b985d6ea218?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Kiwi',
            'category' => 'Birds',
            'breed' => 'Lovebird',
            'age' => '1 year',
            'gender' => 'Female',
            'color' => 'Green',
            'price' => 1800.00,
            'description' => 'Very affectionate and bonds strongly.',
            'behavior' => 'Loving',
            'care_pattern' => 'Needs a companion',
            'is_rescued' => 1,
            'image' => 'https://images.unsplash.com/photo-1555169062-013468b47731?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Charlie Bird',
            'category' => 'Birds',
            'breed' => 'Cockatiel',
            'age' => '2 years',
            'gender' => 'Male',
            'color' => 'Grey & Yellow',
            'price' => 2500.00,
            'description' => 'Whistles tunes and loves head scratches.',
            'behavior' => 'Friendly',
            'care_pattern' => 'Social interaction',
            'is_rescued' => 0,
            'image' => 'https://images.unsplash.com/photo-1610878180933-123728745d22?auto=format&fit=crop&w=800&q=80'
        ]
    ];

    $checkStmt = $pdo->prepare("SELECT id FROM pets WHERE name = ?");
    $insertStmt = $pdo->prepare("INSERT INTO pets (name, category_id, breed, age, gender, color, price, description, behavior, care_pattern, is_rescued, image, status) 
            VALUES (:name, :category_id, :breed, :age, :gender, :color, :price, :description, :behavior, :care_pattern, :is_rescued, :image, 'available')");
    $updateStmt = $pdo->prepare("UPDATE pets SET category_id = :category_id, image = :image, description = :description WHERE name = :name");

    foreach ($allPets as $pet) {
        $catId = getCatId($pet['category'], $catMap);
        
        if (!$catId) {
            echo "Warning: Category '{$pet['category']}' not found for pet '{$pet['name']}'. Skipping.\n";
            continue;
        }

        $checkStmt->execute([$pet['name']]);
        if ($checkStmt->rowCount() == 0) {
            $insertStmt->execute([
                ':name' => $pet['name'],
                ':category_id' => $catId,
                ':breed' => $pet['breed'],
                ':age' => $pet['age'],
                ':gender' => $pet['gender'],
                ':color' => $pet['color'],
                ':price' => $pet['price'],
                ':description' => $pet['description'],
                ':behavior' => $pet['behavior'],
                ':care_pattern' => $pet['care_pattern'],
                ':is_rescued' => $pet['is_rescued'],
                ':image' => $pet['image']
            ]);
            echo "Added new pet: {$pet['name']}\n";
        } else {
            // Force update category and image to fix previous bugs or broken links
            $updateStmt->execute([
                ':category_id' => $catId,
                ':image' => $pet['image'],
                ':description' => $pet['description'],
                ':name' => $pet['name']
            ]);
            echo "Updated existing pet: {$pet['name']} (Fixed Category/Image)\n";
        }
    }

    echo "Data update complete.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
