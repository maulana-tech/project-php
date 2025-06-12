<?php
require_once '../includes/config.php';

$redirect_url = SITE_URL . '/auth/login.php';

if (isset($_GET['redirect']) && $_GET['redirect'] === 'landing') {
    $redirect_url = SITE_URL . '/landing.php';
}

$_SESSION = array();
session_destroy();

// Mulai session baru untuk menampilkan pesan
session_start();
$_SESSION['message'] = 'Anda telah berhasil logout.';
$_SESSION['message_type'] = 'success';

redirect($redirect_url);
?>