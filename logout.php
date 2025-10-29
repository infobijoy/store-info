<?php
session_start();

// Destroy session
session_destroy();

// Clear auto-login cookie
setcookie('auto_login', '', time() - 3600, '/');

// Redirect to login page
header('Location: log-in.php');
exit;
?>