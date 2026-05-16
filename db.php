<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// FILL THESE IN BEFORE UPLOADING THIS FILE TO INFINITYFREE!
$host = 'sql103.infinityfree.com'; // Your official Host Name!
$db_name = 'if0_41937729_if0_41937729'; // Your official Database Name!
$username = 'if0_41937729'; // Your InfinityFree MySQL Username
$password = 'YOUR_VPANEL_PASSWORD'; // <-- TYPE YOUR PASSWORD HERE!

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    if ($e->getCode() == 1049) {
        die("Error: Database '$db_name' not found. Please run setup script.");
    }
    die("Connection failed: " . $e->getMessage());
}
?>
