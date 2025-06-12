<?php
require_once 'includes/config.php';

if (is_logged_in()) {
    redirect_to('ROUTE_DASHBOARD');
} else {
    redirect_to('ROUTE_HOME');
}