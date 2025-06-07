<?php
require_once 'includes/config.php';

if (is_logged_in()) {
    redirect(SITE_URL . '/dashboard.php');
} else {
    redirect(SITE_URL . '/landing.php');
}