<?php
// Pastikan SITE_URL sudah didefinisikan di config.php
if (!defined('SITE_URL')) {
    // Jika belum, definisikan di sini sebagai fallback
    define('SITE_URL', '/project-php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Financial Analysis' : 'Financial Analysis'; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        .active-nav-link {
            background-color: #f3f4f6;
            color: #4f46e5;
            border-left: 4px solid #4f46e5;
        }
    </style>
</head>
<body>
    <?php if (is_logged_in()): ?>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-white border-r">
                <div class="flex items-center justify-center h-16 border-b bg-indigo-600">
                    <h1 class="text-xl font-semibold text-white">Financial Analysis</h1>
                </div>
                <div class="flex flex-col flex-grow p-4 overflow-y-auto">
                    <nav class="flex-1 space-y-2">
                        <a href="<?php echo SITE_URL; ?>/landing.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md">
                            <i class="fas fa-house mr-3"></i>
                            <span>Home</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active-nav-link' : ''; ?>">
                            <i class="fas fa-tachometer mr-3" aria-hidden="true" ></i>
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
                        </a>
                        <?php if (is_admin()): ?>
                        <a href="<?php echo SITE_URL; ?>/admin/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'active-nav-link' : ''; ?>">
                            <i class="fas fa-user-shield mr-3"></i>
                            <span>Admin Panel</span>
                        </a>
                        <?php endif; ?>
                    </nav>
                    <div class="mt-auto">
                        <div class="pt-2 border-t border-gray-200">
                            <a href="<?php echo SITE_URL; ?>/profiles/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/profile/') !== false ? 'active-nav-link' : ''; ?>">
                                <i class="fas fa-user-circle mr-3"></i>
                                <span>Profile</span>
                            </a>
                            <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="flex items-center px-4 py-2 mt-1 text-gray-700 hover:bg-gray-100 hover:text-red-600 rounded-md">
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
            <div class="flex items-center justify-between px-4 py-3"> <!-- Sudah tidak ada max-width di sini -->
                <div class="flex items-center space-x-4">
                    <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="<?php echo SITE_URL; ?>/landing.php" class="text-indigo-600 hover:text-indigo-700">
                        <i class="fas fa-home mr-1"></i>
                        Home
                    </a>
                    <h1 class="text-lg font-semibold text-indigo-600">Financial Analysis</h1>
                </div>
                <div>
                    <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="text-red-500 hover:text-red-600">
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
                            </a>
                            <?php if (is_admin()): ?>
                            <a href="<?php echo SITE_URL; ?>/admin/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'active-nav-link' : ''; ?>">
                                <i class="fas fa-user-shield mr-3"></i>
                                <span>Admin Panel</span>
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo SITE_URL; ?>/profile/index.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md <?php echo strpos($_SERVER['PHP_SELF'], '/profile/') !== false ? 'active-nav-link' : ''; ?>">
                                <i class="fas fa-user-circle mr-3"></i>
                                <span>Profile</span>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-y-auto">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="bg-<?php echo $_SESSION['message_type']; ?>-100 border-l-4 border-<?php echo $_SESSION['message_type']; ?>-500 text-<?php echo $_SESSION['message_type']; ?>-700 p-4 mb-4 mx-4 mt-4" role="alert">
                    <p><?php echo $_SESSION['message']; ?></p>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
            
            <!-- Page content -->
            <main class="flex-1 md:pt-0 pt-16">
<?php else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Financial Analysis' : 'Financial Analysis'; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <!-- For non-logged in users, we don't include navigation here -->
    <!-- Landing page will include landing_navigation.php secara terpisah -->
    <div class="min-h-screen bg-gray-100 flex flex-col justify-center">
        <div class="container mx-auto px-4 py-8">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="bg-<?php echo $_SESSION['message_type']; ?>-100 border-l-4 border-<?php echo $_SESSION['message_type']; ?>-500 text-<?php echo $_SESSION['message_type']; ?>-700 p-4 mb-4" role="alert">
                    <p><?php echo $_SESSION['message']; ?></p>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
<?php endif; ?>

<!-- JavaScript for mobile menu -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMobileMenuButton = document.getElementById('close-mobile-menu');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        if (closeMobileMenuButton && mobileMenu) {
            closeMobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
            });
        }
        
        if (mobileMenuOverlay && mobileMenu) {
            mobileMenuOverlay.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
            });
        }
    });
</script>