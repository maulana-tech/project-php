<?php
require_once '../includes/config.php';
$_SESSION = array();

// Destroy the session
session_destroy();

// Set logout message
session_start();
$_SESSION['message'] = 'You have been successfully logged out.';
$_SESSION['message_type'] = 'success';

// Redirect to login page
redirect(SITE_URL . '/auth/login.php');
?>