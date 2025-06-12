<?php
/**
 * Navigation Component
 * 
 * This file contains the navigation menu for logged-in users.
 * It includes both desktop sidebar and mobile responsive menu.
 * 
 * @package FinancialAnalysis
 */

// Prevent direct file access
if (!defined('SITE_URL')) {
    exit('Direct access to this file is not allowed');
}
?>

<!-- Sidebar -->
<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 bg-white border-r">
        <div class="flex items-center justify-center h-16 border-b bg-indigo-600">
            <h1 class="text-xl font-semibold text-white">Financial Analysis</h1>
        </div>
        <div class="flex flex-col flex-grow p-4 overflow-y-auto">
            <nav class="flex-1 space-y-2">
                <a href="<?php echo route('ROUTE_DASHBOARD'); ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo active_class('ROUTE_DASHBOARD'); ?>">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo route('ROUTE_TRANSACTIONS'); ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo active_class('ROUTE_TRANSACTIONS'); ?>">
                    <i class="fas fa-exchange-alt mr-3"></i>
                    <span>Transactions</span>
                </a>
                <a href="<?php echo route('ROUTE_REPORTS'); ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo active_class('ROUTE_REPORTS'); ?>">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Reports</span>
                </a>
                <a href="<?php echo route('ROUTE_IMPORT'); ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo active_class('ROUTE_IMPORT'); ?>">
                    <i class="fas fa-file-import mr-3"></i>
                    <span>Import Data</span>
                </a>
                <?php if (is_admin()): ?>
                <a href="<?php echo route('ROUTE_ADMIN'); ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'active-nav-link' : ''; ?>">
                    <i class="fas fa-user-shield mr-3"></i>
                    <span>Admin Panel</span>
                </a>
                <?php endif; ?>
            </nav>
            <div class="mt-auto">
            <div class="pt-2 border-t border-gray-200">
                <a href="<?php echo route('ROUTE_PROFILE'); ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo active_class('ROUTE_PROFILE'); ?>">
                    <i class="fas fa-user-circle mr-3"></i>
                    <span>Profile</span>
                </a>
                <a href="<?php echo route('ROUTE_LOGOUT'); ?>?redirect=landing" class="flex items-center px-4 py-2 mt-1 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md">
                    <i class="fas fa-globe mr-3"></i>
                    <span>Kembali ke Landing</span>
                </a>
                <a href="<?php echo route('ROUTE_LOGOUT'); ?>" class="flex items-center px-4 py-2 mt-1 text-gray-700 hover:bg-gray-100 hover:text-red-600 rounded-md">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Mobile menu button -->
<div class="md:hidden fixed top-0 left-0 z-50 w-full bg-white border-b">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center space-x-3">
            <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="text-lg font-semibold text-indigo-600">Financial Analysis</h1>
        </div>
        <div>
            <a href="<?php echo route('ROUTE_LOGOUT'); ?>" class="text-red-500 hover:text-red-600">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>

<!-- Mobile menu -->
<div id="mobile-menu" class="md:hidden fixed inset-0 z-40 hidden">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" id="mobile-menu-overlay"></div>
    <div class="fixed inset-y-0 left-0 max-w-xs w-full bg-white">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between h-16 px-4 border-b bg-indigo-600">
                <h1 class="text-xl font-semibold text-white">Financial Analysis</h1>
                <button id="close-mobile-menu" class="text-white hover:text-gray-200 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4">
                <nav class="space-y-2">
                    <a href="<?php echo SITE_URL; ?>/landing.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md">
                        <i class="fas fa-house mr-3"></i>
                        <span>Home</span>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active-nav-link' : ''; ?>">
                        <i class="fas fa-home mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/transactions/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/transactions/') !== false ? 'active-nav-link' : ''; ?>">
                        <i class="fas fa-exchange-alt mr-3"></i>
                        <span>Transactions</span>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/reports/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/reports/') !== false ? 'active-nav-link' : ''; ?>">
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span>Reports</span>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/import/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/import/') !== false ? 'active-nav-link' : ''; ?>">
                        <i class="fas fa-file-import mr-3"></i>
                        <span>Import Data</span>
                    <?php if (is_admin()): ?>
                    <a href="<?php echo SITE_URL; ?>/admin/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'active-nav-link' : ''; ?>">
                        <i class="fas fa-user-shield mr-3"></i>
                        <span>Admin Panel</span>
                    </a>
                    <?php endif; ?>

                    <a href="<?php echo route('ROUTE_LOGOUT'); ?>?redirect=landing" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md">
                        <i class="fas fa-globe mr-3"></i>
                        <span>Kembali ke Landing</span>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/profile/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/profile/') !== false ? 'active-nav-link' : ''; ?>">
                        <i class="fas fa-user-circle mr-3"></i>
                        <span>Profile</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>