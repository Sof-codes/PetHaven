<?php
/**
 * WEBSITE HEADER
 * This file contains the session start, HTML head section, and the main navigation bar.
 * It is included at the top of every public-facing page.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven - Find Your Perfect Companion</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">
            <i class="fa-solid fa-paw"></i> PetHaven
        </a>
        
        <div class="nav-links">
            <a href="index.php?section=home">Home</a>
            <a href="index.php?section=find-pet">Find a Pet</a>
            <a href="index.php?section=why-adopt">Why Adopt?</a>
            <a href="index.php?section=about">About</a>
        </div>

        <div class="auth-buttons">
            <?php if(isset($_SESSION['username'])): ?>
                <a href="logout.php" class="btn btn-outline">Logout</a>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin/dashboard.php" class="btn btn-primary">Admin Panel</a>
                <?php else: ?>
                    <a href="profile.php" class="btn btn-primary"><i class="fa-regular fa-user"></i> My Profile</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary">Login</a>
                <a href="register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
