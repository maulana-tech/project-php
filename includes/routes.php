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
 * Check if current page matches a route pattern
 * 
 * @param string $routeName The route constant name to check against
 * @return bool True if current page matches the route
 */
function is_route_active($routeName) {
    $route = constant($routeName);
    $currentPath = $_SERVER['PHP_SELF'];
    
    // Handle special cases for index/dashboard
    if ($routeName === 'ROUTE_DASHBOARD' && (basename($currentPath) === 'dashboard.php' || basename($currentPath) === 'index.php')) {
        return true;
    }
    
    // Check if the route path matches the current path
    if (strpos($currentPath, parse_url($route, PHP_URL_PATH)) !== false) {
        return true;
    }
    
    // Check for section matches (e.g., /transactions/, /reports/, etc.)
    $routeSection = explode('/', parse_url($route, PHP_URL_PATH))[1] ?? '';
    $currentSection = explode('/', $currentPath)[1] ?? '';
    
    return $routeSection && $routeSection === $currentSection;
}

/**
 * Get the active class if the current route matches
 * 
 * @param string $routeName The route constant name to check
 * @param string $className The class to return if route is active
 * @return string The active class name or empty string
 */
function active_class($routeName, $className = 'active-nav-link') {
    return is_route_active($routeName) ? $className : '';
}
