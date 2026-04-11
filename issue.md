# [REVISI] Perbaikan Sistem Persediaan Barang - Alfatindo

## Deskripsi

Website **Alfatindo** adalah sistem manajemen persediaan barang (inventory) untuk penyedia layanan internet. Sistem ini mengelola alur barang masuk dan keluar dengan flow sebagai berikut:

```
CS (Customer Service) → Admin → Gudang (Request Barang) → CS (Barang Masuk) → CS (Barang Keluar)
```

Dokumen ini berisi daftar lengkap revisi yang diminta oleh klien, dikelompokkan berdasarkan modul. Setiap item harus dikerjakan dan diverifikasi sebelum dianggap selesai.

---

## A. Modul Barang Masuk

### A1. Filter Pencarian pada Halaman Barang
- Tambahkan **filter pencarian** berdasarkan **nama barang** dan **serial number** pada halaman daftar barang.
- Filter harus bersifat real-time atau menggunakan tombol "Cari" untuk memproses pencarian.

### A2. Kolom Serial Number & Timestamp pada Form Barang Masuk
- Pada saat input barang baru masuk, tambahkan kolom **serial number**.
- Tambahkan kolom **jam masuk** yang secara otomatis terisi dengan waktu saat ini (`now()`), menampilkan **tanggal dan jam**.

### A3. Perubahan Pilihan Satuan Barang
- Ganti opsi satuan barang yang tersedia menjadi hanya:
  - **Meter**
  - **Pcs**
  - **Roll**
  - **Unit**
- Hapus satuan lama yang tidak termasuk dalam daftar di atas.

### A4. Kolom Serial Number pada Halaman Daftar Barang
- Tambahkan kolom **serial number** pada tabel di halaman daftar barang (`/admin/barang`).
- Serial number harus terlihat di setiap baris data barang.

### A5. Kode Barang Unik dengan Sufiks Urutan
- Setiap barang yang masuk harus memiliki **kode barang unik** dengan format:
  ```
  BRG-{timestamp}-{urutan}
  ```
- Contoh implementasi:
  ```
  BRG-1775879111354-01
  BRG-1775879111354-02
  BRG-1775879111354-03
  ```
- Kode dasar (`BRG-{timestamp}`) di-generate saat transaksi barang masuk.
- Sufiks urutan (`-01`, `-02`, dst.) ditambahkan secara otomatis berdasarkan jumlah barang yang masuk dalam transaksi tersebut.

### A6. Hapus Pilihan Customer dari Form Barang Masuk
- Hilangkan field **"Pilih Customer"** dari form input barang masuk.
- Customer tidak diperlukan pada proses barang masuk.

---

## B. Modul Barang Keluar

### B1. Penambahan Jam pada Transaksi Barang Keluar
- Tambahkan kolom **jam transaksi** pada data barang keluar.
- Tampilkan **tanggal dan jam** secara lengkap di tabel dan form transaksi barang keluar.

### B2. Perbaikan UI - Select2 untuk Daftar Pegawai
- Ubah dropdown daftar pegawai/user menjadi menggunakan **Select2** (searchable dropdown).
- Pastikan Select2 berfungsi dengan baik untuk pencarian nama pegawai.

### B3. Kolom Tambahan: Teknisi & Keterangan
- Tambahkan kolom baru **"Teknisi"** pada form barang keluar:
  - Kolom ini khusus untuk barang yang memiliki **serial number**.
  - Input berupa nama teknisi yang bertanggung jawab.
- Tambahkan kolom baru **"Keterangan"** pada form barang keluar:
  - Kolom ini khusus untuk barang **non-serial number**.
  - Input berupa catatan/keterangan penggunaan barang.

### B4. Pop-up Notifikasi Stok Menipis
- Tambahkan **pop-up alert/notifikasi** pada halaman daftar barang (`/admin/barang`).
- Pop-up muncul apabila terdapat barang dengan **stok kurang dari 5 unit**.
- Tampilkan daftar barang yang stoknya menipis dalam pop-up tersebut.

---

## C. Halaman Baru: Barang Tracking

### Deskripsi
Buat **halaman baru** untuk melacak seluruh data barang masuk dan keluar, dilengkapi dengan **QR Code** pada setiap atribut barang.

### C1. Halaman Tracking Barang
- Buat halaman baru di route `/admin/barang-tracking`.
- Halaman ini menampilkan daftar seluruh barang beserta detail tracking-nya.

### C2. QR Code untuk Setiap Atribut Barang
Pada setiap item barang, generate dan tampilkan **QR Code** untuk atribut berikut:

| No | Atribut | Keterangan |
|----|---------|------------|
| 1 | Barang | QR Code berisi informasi lengkap barang |
| 2 | Serial Number | QR Code berisi serial number barang |
| 3 | Kode Barang | QR Code berisi kode unik barang (format `BRG-xxx-xx`) |
| 4 | Satuan Barang | QR Code berisi satuan (Meter/Pcs/Roll/Unit) |
| 5 | Stok Barang | QR Code berisi jumlah stok terkini |
| 6 | Keterangan | QR Code berisi keterangan barang |
| 7 | Tanggal Masuk | QR Code berisi tanggal & jam barang masuk |
| 8 | Tanggal Keluar | QR Code berisi tanggal & jam barang keluar |
| 9 | Teknisi | QR Code berisi nama teknisi penanggung jawab |
| 10 | Customer | QR Code berisi informasi customer |
| 11 | Admin | QR Code berisi informasi admin yang memproses |
| 12 | Gudang | QR Code berisi informasi gudang penyimpanan |
| 13 | Request Barang | QR Code berisi data request barang |

### C3. Spesifikasi Teknis QR Code
- Gunakan library QR Code yang kompatibel dengan Laravel (contoh: `simplesoftwareio/simple-qrcode` atau `chillerlan/php-qrcode`).
- QR Code harus bisa di-scan untuk menampilkan data yang relevan.
- Pertimbangkan untuk menyediakan opsi **print/download QR Code** per barang.

---

## Referensi Teknis

### File & Direktori yang Terdampak

| Modul | File/Direktori | Keterangan |
|-------|---------------|------------|
| Model | `app/Models/Admin/BarangModel.php` | Tambah field serial_number, kode_barang |
| Model | `app/Models/Admin/BarangmasukModel.php` | Tambah relasi & field jam |
| Model | `app/Models/Admin/BarangkeluarModel.php` | Tambah field teknisi, keterangan, jam |
| Controller | `app/Http/Controllers/Admin/BarangController.php` | Filter, serial number |
| Controller | `app/Http/Controllers/Admin/BarangmasukController.php` | Hapus customer, tambah serial number |
| Controller | `app/Http/Controllers/Admin/BarangkeluarController.php` | Tambah teknisi, keterangan, jam |
| Controller | **(BARU)** `app/Http/Controllers/Admin/BarangTrackingController.php` | Halaman tracking |
| View | `resources/views/Admin/Barang/` | Kolom serial number, filter, pop-up stok |
| View | `resources/views/Admin/BarangMasuk/` | Hapus customer, tambah serial number & jam |
| View | `resources/views/Admin/BarangKeluar/` | Tambah teknisi, keterangan, jam, Select2 |
| View | **(BARU)** `resources/views/Admin/BarangTracking/` | Halaman tracking + QR Code |
| Migration | `database/migrations/` | Migrasi untuk kolom baru |
| Routes | `routes/web.php` | Tambah route barang tracking |

### Database Migration yang Diperlukan
1. Tambah kolom `serial_number` pada `tbl_barang`
2. Tambah kolom `kode_barang_unik` pada `tbl_barang` (format: `BRG-{timestamp}-{urutan}`)
3. Tambah kolom `jam_masuk` pada `tbl_barangmasuk`
4. Tambah kolom `jam_keluar` pada `tbl_barangkeluar`
5. Tambah kolom `teknisi` pada `tbl_barangkeluar`
6. Tambah kolom `keterangan` pada `tbl_barangkeluar`
7. Update enum satuan pada `tbl_barang` menjadi: Meter, Pcs, Roll, Unit

---

## Prioritas Pengerjaan

1. **High** — A3 (Satuan), A6 (Hapus Customer), A2 (Serial Number & Jam Masuk)
2. **High** — A5 (Kode Barang Unik), A4 (Kolom Serial Number di Tabel)
3. **Medium** — A1 (Filter Pencarian)
4. **Medium** — B1 (Jam Keluar), B2 (Select2), B3 (Teknisi & Keterangan)
5. **Medium** — B4 (Pop-up Stok Menipis)
6. **Low** — C1-C3 (Halaman Tracking & QR Code)

---

## Checklist Penyelesaian

- [x] A1 — Filter pencarian (nama barang & serial number)
- [x] A2 — Kolom serial number & jam pada barang masuk
- [x] A3 — Satuan barang: Meter, Pcs, Roll, Unit
- [x] A4 — Kolom serial number di halaman barang
- [x] A5 — Kode barang unik dengan sufiks urutan
- [x] A6 — Hapus pilihan customer dari barang masuk
- [x] B1 — Jam pada transaksi barang keluar
- [x] B2 — Select2 untuk daftar pegawai
- [x] B3 — Kolom teknisi & keterangan pada barang keluar
- [x] B4 — Pop-up notifikasi stok menipis (< 5)
- [x] C1 — Halaman tracking barang
- [x] C2 — QR Code untuk setiap atribut barang
- [x] C3 — Integrasi library QR Code
