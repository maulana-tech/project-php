<?php
// Prevent direct access to this file
if (!defined('SITE_URL')) {
    exit('Direct access to this file is not allowed');
}

/**
 * Route Definitions
 * Organized by feature/module
 */

// Main routes
define('ROUTE_HOME', SITE_URL . '/landing.php');
define('ROUTE_DASHBOARD', SITE_URL . '/dashboard.php');
define('ROUTE_INDEX', SITE_URL . '/index.php');

// Authentication routes
define('ROUTE_LOGIN', SITE_URL . '/auth/login.php');
define('ROUTE_REGISTER', SITE_URL . '/auth/register.php');
define('ROUTE_LOGOUT', SITE_URL . '/auth/logout.php');


// Admin routes
define('ROUTE_ADMIN', SITE_URL . '/admin/index.php');

// Import routes
define('ROUTE_IMPORT', SITE_URL . '/import/index.php');

// Transaction routes
define('ROUTE_TRANSACTIONS', SITE_URL . '/transactions/index.php');
define('ROUTE_TRANSACTION_CREATE', SITE_URL . '/transactions/create.php');
define('ROUTE_TRANSACTION_EDIT', SITE_URL . '/transactions/edit.php');

// Report routes
define('ROUTE_REPORTS', SITE_URL . '/reports/index.php');
define('ROUTE_REPORT_SAVE', SITE_URL . '/reports/save_report.php');
define('ROUTE_SAVED_REPORTS', SITE_URL . '/reports/saved_reports.php');

// Profile routes
define('ROUTE_PROFILE', SITE_URL . '/profiles/index.php');

// Helper function to get route with parameters
function route($routeName, $params = []) {
    $route = constant($routeName);
    if (!empty($params)) {
        $queryString = http_build_query($params);
        $route .= '?' . $queryString;
    }
    return $route;
}

// Function to redirect to a route
function redirect_to($routeName, $params = []) {
    $route = route($routeName, $params);
    redirect($route);
}

/**
 * Check if current page matches a route pattern (REVISED FUNCTION)
 * * @param string $routeName The route constant name to check against
 * @return bool True if current page matches the route
 */
function is_route_active($routeName) {
    // Get the path part of the SITE_URL (e.g., /project-php)
    $site_path = rtrim(parse_url(SITE_URL, PHP_URL_PATH), '/');

    // Get the current script's path relative to the domain root (e.g., /project-php/dashboard.php)
    $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Get the path part of the route we are checking
    $route_path = parse_url(constant($routeName), PHP_URL_PATH);

    // Special case for Dashboard: active for both dashboard.php and index.php (when logged in)
    if ($routeName === 'ROUTE_DASHBOARD') {
        $dashboard_path = parse_url(ROUTE_DASHBOARD, PHP_URL_PATH);
        $index_path = parse_url(ROUTE_INDEX, PHP_URL_PATH);
        return in_array($current_path, [$dashboard_path, $index_path]);
    }

    // For other routes, check if the current path is within the route's "directory"
    // e.g., current path '/project-php/transactions/create.php' should match route for '/project-php/transactions/index.php'
    $route_dir = dirname($route_path);
    
    // Check if the current path starts with the route's directory path.
    // The `+1` is to include the trailing slash for an exact directory match.
    if (substr($current_path, 0, strlen($route_dir) + 1) === $route_dir . '/') {
        return true;
    }

    return false;
}


/**
 * Get the active class if the current route matches
 * * @param string $routeName The route constant name to check
 * @param string $className The class to return if route is active
 * @return string The active class name or empty string
 */
function active_class($routeName, $className = 'active-nav-link') {
    return is_route_active($routeName) ? $className : '';
}