# Panduan Setup & Cara Menjalankan Aplikasi - Alfatindo Persediaan

Aplikasi ini adalah Sistem Manajemen Persediaan Barang (Inventory) berbasis Laravel. Berikut adalah panduan langkah demi langkah untuk menginstal dan menjalankan aplikasi ini.

---

## Prasyarat (Prerequisites)
Sebelum memulai, pastikan komputer Anda sudah terinstal:
- **PHP** (minimal versi 8.0)
- **Composer** (untuk dependensi PHP)
- **Node.js & NPM** (untuk frontend assets)
- **MySQL / MariaDB** (melalui XAMPP, Laragon, atau standalone)

---

## Langkah Instalasi & Pengaturan

### 1. Buat Database Baru
Buka phpMyAdmin atau MySQL client Anda, lalu buat database baru dengan nama:
`laravel_alfatindo`

*(File `.env` bawaan proyek ini sudah otomatis terkonfigurasi ke database `laravel_alfatindo` dengan username `root` tanpa password).*

### 2. Instal Dependensi Composer & NPM
Jalankan perintah berikut di terminal/command prompt pada direktori root proyek untuk mengunduh modul PHP dan frontend assets:
```bash
composer install
npm install
npm run dev
```

### 3. Buat Symbolic Link Storage (Penting untuk Gambar Barang!)
Jalankan perintah berikut agar file gambar barang yang diunggah dapat diakses dari browser:
```bash
php artisan storage:link
```

### 4. Jalankan Migrasi & Seeding Database (Penting!)
Untuk membuat semua tabel database baru dan mengisi data awal (seperti Role, Menu, Akses Hak, serta Akun Bawaan), jalankan perintah berikut:
```bash
php artisan migrate:fresh --seed
```

### 5. Jalankan Server Lokal
Setelah migrasi selesai, jalankan server pengembangan lokal Laravel dengan perintah:
```bash
php artisan serve
```
Aplikasi sekarang dapat diakses melalui browser di alamat: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Informasi Akun Bawaan (Default Login)
Setelah proses seeding database di atas selesai, Anda dapat login menggunakan kredensial bawaan berikut sesuai role hak akses masing-masing:

### 1. Akun Owner (Akses Penuh & User Management)
- **Email**: `owner@alfatindo.com`
- **Password**: `12345678`

### 2. Akun Admin Gudang (Manajemen Barang, Transaksi, & Laporan)
- **Email**: `admingudang@alfatindo.com`
- **Password**: `12345678`

### 3. Akun Pegawai Teknisi (Akses Terbatas & Form Pengajuan Pinjam Barang)
- **Email**: `teknisi1@alfatindo.com`
- **Password**: `12345678`
