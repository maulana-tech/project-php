# Project PHP
**233210013 - Muhammad Maulana Firdaussyah**
## Responsi Pemograman Web Server Side 

Aplikasi manajemen keuangan berbasis web yang dibuat dengan PHP, dirancang untuk membantu pengguna dalam melacak, menganalisis, dan membuat laporan keuangan pribadi. Project ini menyediakan dashboard, manajemen transaksi, serta pelaporan fleksibel dengan fitur ekspor data.

## Fitur Utama

- **Autentikasi Pengguna**: Sistem login yang aman untuk akun pengguna.
- **Dashboard**: Ringkasan pemasukan, pengeluaran, dan saldo dengan grafik visual.
- **Pencatatan Transaksi**: Tambah, lihat, dan kategorikan transaksi pemasukan maupun pengeluaran.
- **Laporan Keuangan**: Buat laporan keuangan berdasarkan tanggal, kategori, dan jenis transaksi. Laporan dapat diekspor ke format CSV, XLS, atau PDF.
- **Grafik Interaktif**: Visualisasi tren bulanan dan rincian kategori menggunakan Chart.js.
- **Desain Responsif**: Antarmuka modern dan bersih yang responsif untuk berbagai perangkat.

## Cara Penggunaan

- **Tambah Transaksi:** Catat pemasukan/pengeluaran, atur kategori.
- **Lihat Dashboard:** Pantau ringkasan bulanan, grafik, dan transaksi terbaru.
- **Buat Laporan:** Filter berdasarkan tanggal, jenis, dan kategori; ekspor untuk kebutuhan offline.
- **Ekspor Data:** Unduh laporan ke format CSV, XLS, atau PDF.

## Screenshot

![Tampilan Dashboard](#)
![Tampilan Laporan](#)

## Teknologi yang Digunakan

- **PHP**: Bahasa pemrograman utama server-side
- **MySQL**: Database untuk menyimpan data pengguna dan transaksi
- **HTML/CSS/JS**: Struktur dan interaktifitas front-end
- **Chart.js**: Visualisasi data pada dashboard dan laporan
- **Tailwind CSS** (opsional): Untuk desain responsif berbasis utility (bila digunakan pada UI)

## Struktur Folder

- `/index.php` - Dashboard utama dengan ringkasan keuangan dan grafik
- `/reports/` - Fitur pelaporan keuangan dan ekspor data
- `/includes/` - File konfigurasi, header, dan footer
- `/auth/` - Komponen autentikasi (login, registrasi)
- `/transactions/` - Manajemen transaksi (tambah, edit, lihat)

## Cara Instalasi

1. **Clone repository:**
   ```sh
   git clone https://github.com/maulana-tech/project-php.git
   ```

2. **Siapkan database:**
   - Import file SQL (jika tersedia) ke server MySQL Anda.
   - Update kredensial database pada `includes/config.php`.

3. **Konfigurasi aplikasi:**
   - Pastikan `SITE_URL` pada config mengarah ke URL dasar aplikasi Anda.
   - Periksa dan sesuaikan konfigurasi lainnya bila perlu.

4. **Jalankan aplikasi:**
   - Deploy di server lokal/web yang mendukung PHP dan MySQL.
   - Akses aplikasi via browser.


## Lisensi

[MIT License](LICENSE)

---

© 2025 [maulana-tech](https://github.com/maulana-tech)

> **Catatan:**  
> README ini disusun untuk kebutuhan Ujian Responsi Pemograman Web Server Side. Silakan sesuaikan dengan instruksi dosen bila diperlukan.
