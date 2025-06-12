<?php
// Pastikan file ini hanya diakses melalui include
if (!defined('SITE_URL')) {
    exit('Akses langsung ke file ini tidak diizinkan');
}
?>

<style>
    /* Transisi untuk header saat scroll */
    #landing-header {
        transition: background-color 0.4s ease, box-shadow 0.4s ease, padding 0.4s ease;
    }
    #landing-header.scrolled {
        color: rgb(59, 30, 218);
        background-color: rgba(255, 255, 255, 0.15);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);


        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }
    #landing-header.scrolled .nav-link {
        color: #374151; /* Gray-700 */
    }
    #landing-header.scrolled .nav-link:hover {
        color: #4f46e5; /* Indigo-600 */
    }

    .nav-link {
        position: relative;
        padding-bottom: 8px;
    }
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color:rgb(62, 53, 232); /* Indigo-600 */
        transition: width 0.3s ease-out;
    }
    .nav-link:hover::after, .nav-link.active::after {
        width: 100%;
    }
    #landing-header.scrolled .nav-link:hover {
        color:rgb(39, 72, 233);
    }

    /* Styling untuk menu mobile slide-in */
    #mobile-landing-menu {
        position: fixed;
        top: 0;
        right: -100%;
        width: 80%;
        max-width: 320px;
        height: 100%;
        background-color: white;
        box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        transition: right 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        z-index: 100;
    }
    #mobile-landing-menu.open {
        right: 0;
    }

    #mobile-menu-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.4s ease, visibility 0.4s ease;
        z-index: 99;
    }
    #mobile-menu-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    .hamburger-icon .line {
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .hamburger-icon.open .line-1 {
        transform: translateY(7px) rotate(45deg);
    }
    .hamburger-icon.open .line-2 {
        opacity: 0;
    }
    .hamburger-icon.open .line-3 {
        transform: translateY(-7px) rotate(-45deg);
    }
    #landing-header.scrolled .hamburger-icon .line {
        background-color:rgb(59, 30, 218);
    }
</style>

<header id="landing-header" class="fixed w-full z-20 top-0 text-white py-6">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <a href="<?php echo SITE_URL; ?>/" class="flex-shrink-0 flex items-center">
                <i class="fas fa-coins text-2xl mr-2 transition-colors duration-300"></i>
                <span class="font-bold text-xl transition-colors duration-300">FinancialApp</span>
            </a>

            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="nav-link font-medium">Fitur</a>
                <a href="#how-it-works" class="nav-link font-medium">Cara Kerja</a>
                <a href="<?php echo SITE_URL; ?>/auth/login.php" class="nav-link font-medium">Masuk</a>
                <a href="<?php echo SITE_URL; ?>/auth/register.php" class="ml-4 inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-bold text-indigo-600 bg-white shadow-sm hover:bg-gray-100 transition-transform transform hover:scale-105">
                    Daftar Gratis
                </a>
            </div>

            <div class="md:hidden">
                <button type="button" id="mobile-menu-button" class="hamburger-icon inline-flex items-center justify-center p-2 rounded-md focus:outline-none" aria-controls="mobile-landing-menu" aria-expanded="false">
                    <span class="sr-only">Buka menu</span>
                    <div class="space-y-1.5">
                        <span class="line line-1 block w-6 h-0.5 bg-white"></span>
                        <span class="line line-2 block w-6 h-0.5 bg-white"></span>
                        <span class="line line-3 block w-6 h-0.5 bg-white"></span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</header>

<div id="mobile-menu-overlay"></div>
<div id="mobile-landing-menu" class="flex flex-col p-6">
    <h3 class="font-bold text-2xl text-indigo-600 mb-8">Menu</h3>
    <a href="#features" class="mobile-nav-link block py-3 text-lg text-gray-700 hover:text-indigo-600">Fitur</a>
    <a href="#how-it-works" class="mobile-nav-link block py-3 text-lg text-gray-700 hover:text-indigo-600">Cara Kerja</a>
    <a href="<?php echo SITE_URL; ?>/auth/login.php" class="mobile-nav-link block py-3 text-lg text-gray-700 hover:text-indigo-600">Masuk</a>
    <div class="mt-auto">
        <a href="<?php echo SITE_URL; ?>/auth/register.php" class="block w-full px-4 py-3 text-center text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">
            Daftar Gratis
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // === Efek Header saat Scroll ===
    const header = document.getElementById('landing-header');
    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // === Logika Menu Mobile ===
    const menuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-landing-menu');
    const overlay = document.getElementById('mobile-menu-overlay');
    const mobileLinks = document.querySelectorAll('.mobile-nav-link');

    function toggleMenu() {
        menuButton.classList.toggle('open');
        mobileMenu.classList.toggle('open');
        overlay.classList.toggle('open');
    }

    menuButton.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);

    // Menutup menu saat tautan di dalamnya diklik
    mobileLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Cek jika menu terbuka sebelum menutupnya
            if (mobileMenu.classList.contains('open')) {
                toggleMenu();
            }
        });
    });
    
    // === Smooth scroll untuk anchor links ===
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerOffset = header.classList.contains('scrolled') ? header.offsetHeight : 0;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        });
    });
});
</script>