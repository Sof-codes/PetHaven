<?php
require_once 'includes/db.php';

echo "<h2>Categories</h2>";
$cats = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Image</th></tr>";
foreach($cats as $cat) {
    echo "<tr><td>{$cat['id']}</td><td>{$cat['name']}</td><td>{$cat['image']}</td></tr>";
}
echo "</table>";

echo "<h2>Pets (Tweety, Charlie Bird, Rio, Blue, Cinnamon)</h2>";
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM pets p LEFT JOIN categories c ON p.category_id = c.id WHERE p.name IN ('Tweety', 'Charlie Bird', 'Rio', 'Blue', 'Cinnamon') ORDER BY p.name");
$stmt->execute();
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Breed</th><th>Category ID</th><th>Category Name</th><th>Image Path</th></tr>";
foreach($pets as $pet) {
    echo "<tr><td>{$pet['id']}</td><td>{$pet['name']}</td><td>{$pet['breed']}</td><td>{$pet['category_id']}</td><td>{$pet['category_name']}</td><td>{$pet['image']}</td></tr>";
}
echo "</table>";
?>
