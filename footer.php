<?php
/**
 * WEBSITE FOOTER
 * Contains the branding, useful links, and copyright information.
 */
?>
<?php if (isset($section) && $section === 'home'): ?>
<footer>
    <div class="container">
        <div class="footer-grid" style="grid-template-columns: 2fr 1fr 1fr;">
            <div class="footer-brand">
                <a href="index.php" class="logo mb-2">
                    <i class="fa-solid fa-paw"></i> PetHaven
                </a>
                <p>Connecting loving families with pets in need of a forever home. Join our community of animal lovers today.</p>
            </div>
            
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li style="margin-bottom: 10px;"><a href="index.php?section=home">Home</a></li>
                    <li style="margin-bottom: 10px;"><a href="index.php?section=find-pet">Find a Pet</a></li>
                    <li style="margin-bottom: 10px;"><a href="register.php">Register</a></li>
                    <li style="margin-bottom: 10px;"><a href="login.php">User Login</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Support</h4>
                <ul>
                    <li style="margin-bottom: 10px;"><a href="index.php?section=about">About Us</a></li>
                    <li style="margin-bottom: 10px;"><a href="index.php?section=why-adopt">Why Adopt?</a></li>
                    <li style="margin-bottom: 10px;"><a href="admin/dashboard.php">Admin Panel</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> PetHaven Adoption System. All rights reserved.</p>
        </div>
    </div>
</footer>
<?php endif; ?>

</body>
</html>
