<?php
require_once 'includes/db.php';

try {
    $stmt = $pdo->query("SELECT id, name, image FROM pets");
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $output = "Total Pets: " . count($pets) . "\n\n";
    foreach($pets as $pet) {
        $output .= "ID: {$pet['id']}\nName: {$pet['name']}\nImage: {$pet['image']}\n-----------------------------------\n";
    }
    file_put_contents('pets_dump.txt', $output);
    echo "Dumped to pets_dump.txt";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
