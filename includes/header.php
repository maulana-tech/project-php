<?php
/**
 * Header Component
 * 
 * This file contains the main HTML structure and common includes.
 * Handles both logged-in and non-logged-in states.
 * 
 * @package FinancialAnalysis
 */

// Check for SITE_URL constant
if (!defined('SITE_URL')) {
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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        .active-nav-link {
            @apply bg-indigo-50 text-indigo-600 border-l-4 border-indigo-600;
        }
    </style>
</head>
<body>
    <?php if (is_logged_in()): ?>
        <div class="flex h-screen bg-gray-100">
            <?php include_once 'navigation.php'; ?>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="m-4 bg-<?php echo $_SESSION['message_type']; ?>-100 border-l-4 border-<?php echo $_SESSION['message_type']; ?>-500 text-<?php echo $_SESSION['message_type']; ?>-700 p-4" role="alert">
                        <p><?php echo $_SESSION['message']; ?></p>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>
    <?php else: ?>
        <div class="min-h-screen bg-gray-100">
            <?php include_once 'landing_navigation.php'; ?>
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="container mx-auto px-4 py-8">
                    <div class="bg-<?php echo $_SESSION['message_type']; ?>-100 border-l-4 border-<?php echo $_SESSION['message_type']; ?>-500 text-<?php echo $_SESSION['message_type']; ?>-700 p-4 mb-4" role="alert">
                        <p><?php echo $_SESSION['message']; ?></p>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                </div>
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