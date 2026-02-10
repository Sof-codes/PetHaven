<?php
require_once __DIR__ . '/includes/db.php';

// Helper to forcefuly download with unique filenames
function downloadUnique($url, $savePath) {
    if (file_exists($savePath) && filesize($savePath) > 5000) {
        return true; 
    }

    $ch = curl_init($url);
    $fp = fopen($savePath, 'wb');
    if (!$fp) return false;

    // Standard headers
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    
    if ($code == 200 && filesize($savePath) > 1000) {
        return true;
    }
    @unlink($savePath);
    return false;
}

try {
    echo "<h1>Fixing Duplicate/Unrelated Images...</h1>";

    // Defines Specific, Unique URLs for pets that might have been duplicates
    $uniqueImages = [
        // RABBITS
        'Oreo' => 'https://images.unsplash.com/photo-1585110396065-852a202d02a6?fit=crop&w=600&q=80', // Dutch
        'Thumper' => 'https://images.unsplash.com/photo-1518796745738-41048802f99a?fit=crop&w=600&q=80', // Lop
        'Snowball' => 'https://images.unsplash.com/photo-1591382386627-4493e36e05d0?fit=crop&w=600&q=80', // White
        'Cinnamon' => 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?fit=crop&w=600&q=80', // Brown
        'Peter' => 'https://images.unsplash.com/photo-1589578228447-e1a4e481c6c8?fit=crop&w=600&q=80', // Wild
        'Luna' => 'https://images.unsplash.com/photo-1535241554-2dff4043020c?fit=crop&w=600&q=80', // Grey Dwarf
        'BunBun' => 'https://images.unsplash.com/photo-1559214369-a6b1d7919865?fit=crop&w=600&q=80', // Rex
        
        // BIRDS
        'Tweety' => 'https://images.unsplash.com/photo-1620697970845-a488e00ee47c?fit=crop&w=600&q=80', // Yellow
        'Blue' => 'https://images.unsplash.com/photo-1615053930810-724d27f8d689?fit=crop&w=600&q=80', // Blue Parakeet
        'Kiwi' => 'https://images.unsplash.com/photo-1610878180933-123728745d22?fit=crop&w=600&q=80', // Green Lovebird
        'Charlie Bird' => 'https://images.unsplash.com/photo-1620694582662-799408668102?fit=crop&w=600&q=80', // Cockatiel (Updated)
        'Rio' => 'https://images.unsplash.com/photo-1452570053594-1b985d6ea218?fit=crop&w=600&q=80', // Macaw
        'Polly' => 'https://images.unsplash.com/photo-1552728089-57bdde30ebd1?fit=crop&w=600&q=80', // Green Parrot
        'Sunny' => 'https://images.unsplash.com/photo-1596711467406-8c46d31f24d7?fit=crop&w=600&q=80', // Sun Conure

        // HAMSTERS
        'Nibbles' => 'https://images.unsplash.com/photo-1548767797-d8c844163c4b?fit=crop&w=600&q=80', // Golden
        'Peanut' => 'https://images.unsplash.com/photo-1425082661705-1834bfd09dca?fit=crop&w=600&q=80', // Dwarf

        // CATS
        'Whiskers' => 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?fit=crop&w=600&q=80', // Maine Coon
        'Luna Cat' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?fit=crop&w=600&q=80', // Cat

        // DOGS
        'Buster' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?fit=crop&w=600&q=80', // Bulldog
        'Charlie' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?fit=crop&w=600&q=80', // Lab (Reusing Bella img as Lab for now if needed, but finding specific)
    ];

    $stmt = $pdo->prepare("UPDATE pets SET image = ? WHERE name = ?");

    foreach ($uniqueImages as $name => $url) {
        // Unique filename to prevent overwriting shared files
        $cleanName = strtolower(str_replace(' ', '_', $name));
        $filename = 'pet_' . $cleanName . '_unique.jpg';
        $fullPath = __DIR__ . '/assets/images/' . $filename;
        $localPath = 'assets/images/' . $filename;

        echo "Updating $name... ";
        
        // Always try to download fresh to ensure uniqueness
        if (file_exists($fullPath)) unlink($fullPath); 

        if (downloadUnique($url, $fullPath)) {
            $stmt->execute([$localPath, $name]);
            echo "UPDATED with unique image.<br>";
        } else {
            echo "FAILED to download unique image. Keeping existing.<br>";
        }
    }
    
    echo "<h2>DONE! Images should be distinct now.</h2>";

} catch (Exception $e) {
    echo $e->getMessage();
}
