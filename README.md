# KopiKita — Sistem Informasi Manajemen Kafe

Website contoh/prototipe untuk proposal **Perancangan Sistem Informasi Manajemen Kafe Berbasis Website**.

## Teknologi
- PHP 8.0+
- MySQL / MariaDB
- HTML, CSS, JavaScript
- Chart.js (grafik dasbor melalui CDN)

## Fitur
- Login Admin dan Kasir dengan password hash
- Dasbor pendapatan harian/bulanan, transaksi, jumlah menu, stok menipis
- Manajemen menu: harga pokok, harga jual, kategori, stok, nonaktifkan menu
- POS: keranjang, hitung total, pembayaran, kembalian, validasi stok
- Cetak struk
- Manajemen stok masuk/keluar dan riwayat pergerakan
- Laporan transaksi berdasarkan tanggal dan cetak laporan

## Cara menjalankan dengan XAMPP
1. Pastikan Apache dan MySQL berjalan di XAMPP.
2. Buat folder `kafe_management_website` di `C:\\xampp\\htdocs\\`.
3. Salin seluruh isi folder proyek ini ke folder tersebut.
4. Buka `http://localhost/phpmyadmin`, pilih menu **Import**, lalu impor file `database.sql`.
5. Periksa `config.php`. Pengaturan bawaan cocok untuk XAMPP standar: host `localhost`, database `kafe_management`, user `root`, password kosong.
6. Buka `http://localhost/kafe_management_website/setup.php` satu kali untuk membuat akun demo.
7. Hapus file `setup.php` setelah akun dibuat.
8. Buka `http://localhost/kafe_management_website/login.php`.

## Akun demo
- Admin: `admin` / `admin123`
- Kasir: `kasir` / `kasir123`

Ganti password demo sebelum penggunaan nyata.

## Catatan penting
- Ini adalah prototipe akademik/lokal. Belum terhubung ke payment gateway/QRIS, printer fisik otomatis, atau layanan hosting.
- Modul stok mencatat stok menu/menu sebagai contoh sederhana. Untuk implementasi kafe sungguhan, stok bahan baku sebaiknya dipisahkan ke tabel bahan baku dan resep/BOM agar penjualan menu otomatis mengurangi bahan sesuai resep.
- Tambahkan HTTPS, backup terjadwal, pembatasan akses server, audit log, dan pengujian keamanan sebelum dipakai pada bisnis sungguhan.
- Chart.js dan Google Fonts dimuat dari internet; tanpa koneksi, fungsi utama tetap dapat dipakai, tetapi grafik/font dapat kembali ke tampilan standar.
