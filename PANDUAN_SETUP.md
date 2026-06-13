# Panduan Setup Project Laravel Persediaan (Alfatindo)

Dokumen ini berisi langkah-langkah untuk menyiapkan dan menjalankan project ini di komputer Anda setelah melakukan import / clone.

---

## 📋 Persyaratan Sistem
Sebelum memulai, pastikan komputer Anda sudah terinstal:
1. **PHP** (minimal versi 8.0)
2. **Composer** (untuk mengelola dependency PHP)
3. **Node.js & NPM** (untuk mengelola dependency Javascript & CSS)
4. **Local Web Server & Database** (Disarankan menggunakan **Laragon** atau **XAMPP** dengan MySQL)

---

## 🚀 Langkah-Langkah Setup Awal

Ikuti langkah-langkah di bawah ini secara berurutan:

### 1. File Konfigurasi Environment (`.env`)
* **Jika di-import dari folder/ZIP**: File `.env` asli sudah disertakan di dalam project, jadi Anda **tidak perlu menyalin apa-apa**. Anda bisa langsung lanjut ke langkah berikutnya.
* **Jika di-clone dari Git**: File `.env` sengaja diabaikan oleh Git demi keamanan. Anda harus menyalin file `.env.example` terlebih dahulu:
  ```bash
  cp .env.example .env
  ```

### 2. Konfigurasi Database di `.env`
1. Buka database manager Anda (phpMyAdmin atau HeidiSQL) dan **buat database baru** bernama:
   `laravel_alfatindo`.
2. Buka file `.env` di text editor (VS Code).
3. Sesuaikan konfigurasi database berikut dengan server lokal Anda (misal username/password database lokal Anda):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_alfatindo
   DB_USERNAME=root
   DB_PASSWORD=            # Isi jika MySQL lokal Anda menggunakan password
   ```

### 3. Install Dependency PHP (Composer)
Jalankan perintah ini di terminal project untuk mengunduh semua package PHP yang dibutuhkan:
```bash
composer install
```

### 4. Generate Application Key
Jalankan perintah berikut untuk membuat kunci pengaman aplikasi unik pada file `.env` Anda:
```bash
php artisan key:generate
```

### 5. Install Dependency Javascript (NPM)
Jalankan perintah berikut untuk mengunduh package frontend:
```bash
npm install
```

### 6. Jalankan Migrasi & Seeding Database
Untuk membuat struktur tabel database beserta data awal (seperti hak akses, menu, dan akun demo), jalankan perintah berikut:
```bash
php artisan migrate --seed
```

### 7. Jalankan Project
Buka dua tab terminal terpisah di folder project:
* **Terminal 1** (Untuk menjalankan server PHP):
  ```bash
  php artisan serve
  ```
  Aplikasi Anda akan berjalan di [http://127.0.0.1:8000](http://127.0.0.1:8000).

* **Terminal 2** (Untuk mengompilasi aset CSS & JS secara real-time / hot reload):
  ```bash
  npm run dev
  ```

---

## 🔑 Akun Demo Default untuk Login
Setelah masuk ke halaman login [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login), Anda bisa masuk menggunakan akun default berikut:

### 1. Akun Owner
* **Username**: `owner`
* **Password**: `12345678`

### 2. Akun Admin Gudang
* **Username**: `admingudang`
* **Password**: `12345678`

### 3. Akun Teknisi
* **Username**: `teknisi1`
* **Password**: `12345678`
