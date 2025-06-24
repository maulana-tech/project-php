<?php
require_once 'includes/config.php';

if (is_logged_in()) {
    redirect(SITE_URL . '/dashboard.php');
}

$page_title = 'Selamat Datang di Analisis Keuangan Pribadi Anda';
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
    </style>
</head>
<body>
    <?php
    include_once 'includes/landing_navigation.php';
    ?>

    <!-- Hero Section -->
    <section class="pt-16 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white min-h-screen flex items-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-center md:text-left">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight">
                        Kelola Keuangan <span class="block">Jadi Lebih Cerdas.</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-indigo-100 mb-10 max-w-xl mx-auto md:mx-0">
                        Lacak pemasukan, pantau pengeluaran, dan capai tujuan finansial Anda dengan aplikasi intuitif kami.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center md:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="<?php echo SITE_URL; ?>/auth/register.php" class="bg-white text-indigo-600 hover:bg-opacity-90 font-semibold py-3 px-8 rounded-lg text-lg shadow-lg transform hover:scale-105 transition duration-300 ease-in-out">
                            Mulai <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#features" class="bg-transparent border-2 border-white hover:bg-white hover:text-indigo-600 font-semibold py-3 px-8 rounded-lg text-lg shadow-lg transform hover:scale-105 transition duration-300 ease-in-out">
                            Pelajari Fitur
                        </a>
                    </div>
                </div>
                <div class="hidden md:block transform transition-transform duration-500 hover:scale-105" id="image-container">
                    <img id="interactive-image" 
                        src="<?php echo SITE_URL; ?>/assets/images/dashboard.png" 
                        alt="Ilustrasi Fitur Aplikasi" 
                        class="rounded-xl shadow-2xl transition-opacity duration-500 ease-in-out"
                        onerror="this.src='https://placehold.co/600x450/ffffff/6366f1?text=Manajemen+Keuangan'">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-indigo-600 font-semibold uppercase tracking-wider">Kemampuan Unggul</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-2 mb-4">Fitur Lengkap untuk Anda</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Semua yang Anda butuhkan untuk mengelola keuangan pribadi secara efektif dan efisien.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out transform hover:-translate-y-1">
                    <div class="flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full w-16 h-16 mb-6">
                        <i class="fas fa-chart-pie text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Dashboard Interaktif</h3>
                    <p class="text-gray-600 leading-relaxed">Ringkasan visual pemasukan, pengeluaran, dan saldo. Pantau kesehatan finansial Anda sekilas.</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out transform hover:-translate-y-1">
                    <div class="flex items-center justify-center bg-green-100 text-green-600 rounded-full w-16 h-16 mb-6">
                        <i class="fas fa-exchange-alt text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Manajemen Transaksi</h3>
                    <p class="text-gray-600 leading-relaxed">Catat, kategorikan, dan kelola semua transaksi Anda dengan mudah. Tidak ada lagi kebingungan.</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out transform hover:-translate-y-1">
                    <div class="flex items-center justify-center bg-blue-100 text-blue-600 rounded-full w-16 h-16 mb-6">
                        <i class="fas fa-file-invoice-dollar text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Laporan Fleksibel</h3>
                    <p class="text-gray-600 leading-relaxed">Buat laporan keuangan mendalam berdasarkan periode, kategori, atau jenis. Ekspor data ke CSV/XLS.</p>
                </div>
                 <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out transform hover:-translate-y-1">
                    <div class="flex items-center justify-center bg-yellow-100 text-yellow-600 rounded-full w-16 h-16 mb-6">
                        <i class="fas fa-shield-alt text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Keamanan Terjamin</h3>
                    <p class="text-gray-600 leading-relaxed">Data keuangan Anda aman dengan sistem autentikasi modern dan enkripsi.</p>
                </div>
                 <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out transform hover:-translate-y-1">
                    <div class="flex items-center justify-center bg-purple-100 text-purple-600 rounded-full w-16 h-16 mb-6">
                        <i class="fas fa-cogs text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Kustomisasi Mudah</h3>
                    <p class="text-gray-600 leading-relaxed">Sesuaikan kategori pendapatan dan pengeluaran agar sesuai dengan gaya hidup Anda.</p>
                </div>
                 <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out transform hover:-translate-y-1">
                    <div class="flex items-center justify-center bg-pink-100 text-pink-600 rounded-full w-16 h-16 mb-6">
                        <i class="fas fa-mobile-alt text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Desain Responsif</h3>
                    <p class="text-gray-600 leading-relaxed">Akses data keuangan Anda kapan saja, di mana saja, dari perangkat apa pun.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-indigo-600 font-semibold uppercase tracking-wider">Mulai Dengan Mudah</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-2 mb-4">3 Langkah Sederhana</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kelola keuangan Anda tanpa ribet. Ikuti langkah mudah berikut untuk memulai.
                </p>
            </div>
            
            <div class="relative">
                <!-- Connecting line (for desktop) -->
                <div class="hidden lg:block absolute top-1/2 left-0 right-0 h-1 bg-indigo-100 transform -translate-y-1/2" style="z-index: -1;"></div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-16 relative">
                    <!-- Step 1 -->
                    <div class="text-center p-6 bg-gray-50 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300">
                        <div class="relative inline-block">
                            <div class="bg-indigo-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg">1</div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Buat Akun Anda</h3>
                        <p class="text-gray-600">Daftar gratis dalam hitungan menit. Amankan data finansial Anda.</p>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="text-center p-6 bg-gray-50 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300">
                         <div class="relative inline-block">
                            <div class="bg-indigo-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg">2</div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Catat Transaksi</h3>
                        <p class="text-gray-600">Mulai masukkan data pemasukan dan pengeluaran harian Anda dengan mudah.</p>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="text-center p-6 bg-gray-50 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300">
                         <div class="relative inline-block">
                            <div class="bg-indigo-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg">3</div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Analisis & Laporkan</h3>
                        <p class="text-gray-600">Lihat perkembangan finansial Anda melalui dashboard dan laporan detail.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-indigo-700 to-purple-700 text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold mb-6">
                Siap Mengambil Kendali Keuangan Anda?
            </h2>
            <p class="text-xl text-indigo-100 mb-10 max-w-2xl mx-auto">
                Bergabunglah dengan ribuan pengguna yang telah merasakan kemudahan mengelola finansial pribadi.
            </p>
            <a href="<?php echo SITE_URL; ?>/auth/register.php" class="bg-white text-indigo-700 hover:bg-opacity-90 font-bold py-4 px-10 rounded-lg text-lg shadow-xl transform hover:scale-105 transition duration-300 ease-in-out">
                Coba Sekarang <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">FinancialApp</h3>
                    <p class="text-sm">Kelola keuangan pribadi Anda dengan cerdas, mudah, dan aman. Capai kebebasan finansial Anda mulai hari ini.</p>
                     <div class="mt-6 flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-indigo-400 transition-colors duration-300"><i class="fab fa-facebook-f text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-indigo-400 transition-colors duration-300"><i class="fab fa-instagram text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-indigo-400 transition-colors duration-300"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-indigo-400 transition-colors duration-300"><i class="fab fa-linkedin-in text-xl"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Navigasi Cepat</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo SITE_URL; ?>/" class="hover:text-indigo-400 transition-colors duration-300">Beranda</a></li>
                        <li><a href="#features" class="hover:text-indigo-400 transition-colors duration-300">Fitur</a></li>
                        <li><a href="#how-it-works" class="hover:text-indigo-400 transition-colors duration-300">Cara Kerja</a></li>
                         <li><a href="<?php echo SITE_URL; ?>/auth/login.php" class="hover:text-indigo-400 transition-colors duration-300">Masuk Aplikasi</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-indigo-400 transition-colors duration-300">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors duration-300">Syarat & Ketentuan</a></li>
                         <li><a href="#" class="hover:text-indigo-400 transition-colors duration-300">Lisensi</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Hubungi Kami</h3>
                    <ul class="space-y-2 text-sm">
                        <li><i class="fas fa-envelope mr-2 text-indigo-400"></i> firdaussyah03@gmail.com</li>
                        <li><i class="fas fa-phone mr-2 text-indigo-400"></i> +62 21 1234 5678</li>
                        <li><i class="fas fa-map-marker-alt mr-2 text-indigo-400"></i> Yogyakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-8 text-center text-sm">
                <p>&copy; <?php echo date('Y'); ?> FinancialApp by Muhammad Maulana Firdaussyah. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Bagian 1: Logika untuk Mobile Menu & Smooth Scroll

    const menuButton = document.getElementById('mobile-landing-menu-button');
    const mobileMenu = document.getElementById('mobile-landing-menu');

    if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', function () {
            const expanded = this.getAttribute('aria-expanded') === 'true' || false;
            this.setAttribute('aria-expanded', !expanded);
            mobileMenu.classList.toggle('hidden');
            
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

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerOffset = document.querySelector('nav').offsetHeight || 64;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });

                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                     menuButton.click(); // Menutup menu mobile jika terbuka
                }
            }
        });
    });

    // Bagian 2: Logika untuk Galeri Gambar Interaktif

    const imageContainer = document.getElementById('image-container');
    const imageElement = document.getElementById('interactive-image');
    
    // memastikan bahwa elemen ada sebelum melanjutkan
    if (imageContainer && imageElement) {
        const imagePaths = [
            '<?php echo SITE_URL; ?>/assets/images/dashboard.png',
            '<?php echo SITE_URL; ?>/assets/images/transactions.png',
            '<?php echo SITE_URL; ?>/assets/images/reports.png'
        ];
        
        let currentIndex = 0;
        let intervalId = null;

        function changeImage() {
            imageElement.style.opacity = '0';
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % imagePaths.length;
                imageElement.src = imagePaths[currentIndex];
                imageElement.style.opacity = '1';
            }, 500);
        }

        imageContainer.addEventListener('mouseover', () => {
            if (intervalId) clearInterval(intervalId);
            intervalId = setInterval(changeImage, 2000);
        });

        imageContainer.addEventListener('mouseout', () => {
            clearInterval(intervalId);
            intervalId = null;
            
            imageElement.style.opacity = '0';
            setTimeout(() => {
                currentIndex = 0;
                imageElement.src = imagePaths[0];
                imageElement.style.opacity = '1';
            }, 500);
        });
    }
});
</script>
</body>
</html>
