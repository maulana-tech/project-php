<?php
// File ini berisi navigasi untuk landing page

// Pastikan file ini hanya diakses melalui include
if (!defined('SITE_URL')) {
    exit('Akses langsung ke file ini tidak diizinkan');
}
?>

<!-- Landing Page Specific Navigation -->
<nav class="bg-white shadow-sm fixed w-full z-20 top-0">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <a href="<?php echo SITE_URL; ?>/" class="flex-shrink-0 flex items-center">
                    <i class="fas fa-coins text-indigo-600 text-2xl mr-2"></i>
                    <span class="font-bold text-xl text-indigo-600">FinancialApp</span>
                </a>
            </div>
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <a href="#features" class="text-gray-500 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Fitur</a>
                    <a href="#how-it-works" class="text-gray-500 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Cara Kerja</a>
                    <a href="<?php echo SITE_URL; ?>/auth/login.php" class="text-gray-500 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Masuk</a>
                    <a href="<?php echo SITE_URL; ?>/auth/register.php" class="ml-4 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Daftar Gratis
                    </a>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <!-- Mobile menu button -->
                <button type="button" id="mobile-landing-menu-button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-landing-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="md:hidden hidden" id="mobile-landing-menu">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="#features" class="text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 block px-3 py-2 rounded-md text-base font-medium">Fitur</a>
            <a href="#how-it-works" class="text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 block px-3 py-2 rounded-md text-base font-medium">Cara Kerja</a>
            <a href="<?php echo SITE_URL; ?>/auth/login.php" class="text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 block px-3 py-2 rounded-md text-base font-medium">Masuk</a>
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="px-2">
                <a href="<?php echo SITE_URL; ?>/auth/register.php" class="block w-full px-4 py-2 text-center text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md shadow-sm">
                    Daftar Gratis
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- JavaScript for mobile menu toggle for landing page -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuButton = document.getElementById('mobile-landing-menu-button');
        const mobileMenu = document.getElementById('mobile-landing-menu');

        if (menuButton && mobileMenu) {
            menuButton.addEventListener('click', function () {
                const expanded = this.getAttribute('aria-expanded') === 'true' || false;
                this.setAttribute('aria-expanded', !expanded);
                mobileMenu.classList.toggle('hidden');
                // Toggle icon
                const icon = this.querySelector('i');
                if (icon.classList.contains('fa-bars')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    // Calculate offset for fixed header
                    const headerOffset = document.querySelector('nav').offsetHeight || 64; // 64px is h-16
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });
                    // Close mobile menu if open
                    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                         menuButton.click(); // Simulate click to close
                    }
                }
            });
        });
    });
</script>