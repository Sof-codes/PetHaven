<?php
require_once __DIR__ . '/includes/db.php';

echo "Database: " . $db_name . "\n";
$stmt = $pdo->query("SELECT id, name, image FROM pets WHERE name='Charlie'");
$pet = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Charlie Image: [" . $pet['image'] . "]\n";
echo "Type: " . gettype($pet['image']) . "\n";
echo "Length: " . strlen($pet['image']) . "\n";
echo "Is HTTP? " . (strpos($pet['image'], 'http') === 0 ? "YES" : "NO") . "\n";
echo "Strpos: " . var_export(strpos($pet['image'], 'http'), true) . "\n";

// Run update directly here
$pdo->prepare("UPDATE pets SET image='assets/images/pet_charlie_bird.jpg' WHERE name='Charlie'")->execute();
echo "Updated Charlie directly.\n";

$stmt = $pdo->query("SELECT id, name, image FROM pets WHERE name='Charlie'");
$pet = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Charlie Image After: [" . $pet['image'] . "]\n";
