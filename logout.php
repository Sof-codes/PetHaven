<?php
/**
 * LOGOUT SCRIPT
 * Clears the session and redirects the user back to the home page.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_destroy();
header("Location: index.php");
exit;
?>
