<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Updating Category Images...\n";

    $catUpdates = [
        'Dogs' => 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=500&q=80',
        'Cats' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=500&q=80',
        'Birds' => 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?auto=format&fit=crop&w=500&q=80',
        'Rabbits' => 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?auto=format&fit=crop&w=500&q=80',
    ];

    $stmt = $pdo->prepare("UPDATE categories SET image = ? WHERE name = ?");
    foreach ($catUpdates as $name => $url) {
        $stmt->execute([$url, $name]);
        if ($stmt->rowCount() > 0) echo "Updated image for $name.\n";
    }

    echo "Category update complete.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
