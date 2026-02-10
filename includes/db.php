<?php
$host = '127.0.0.1';
$db_name = 'pet_adoption_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // For development, we might want to see the error, but in production hide it.
    // If the database doesn't exist, we might need to run the setup.sql
    if ($e->getCode() == 1049) {
        die("Connection failed: Database '$db_name' not found. Please create it or run setup.sql.");
    }
    die("Connection failed: " . $e->getMessage());
}
?>
