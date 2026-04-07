# Panduan Penggunaan Sistem Klasifikasi Barang Pinjaman

Sistem ini membagi barang menjadi dua tipe utama untuk memudahkan pengelolaan transaksi peminjaman:

## 1. Barang Kembali (Returnable)
**Definisi:** Barang yang dipinjam dan harus dikembalikan dalam kondisi utuh setelah selesai digunakan.
**Contoh:** Tangga, perkakas (bor, kunci pas), laptop, alat ukur.

**Alur Kerja:**
- Saat barang dikeluarkan, status transaksi akan menjadi **"Dipinjam"**.
- Stok barang akan berkurang sementara.
- Setelah pemakaian selesai, user harus melakukan proses **"Pengembalian"** melalui tombol khusus di menu Barang Keluar.
- Pada proses pengembalian, sistem akan meminta input:
  - **Tanggal Kembali**: Tanggal barang diterima kembali.
  - **Jumlah Kembali**: Memastikan jumlah yang dikembalikan sesuai.
  - **Kondisi Barang**: Mencatat kondisi (Baik/Rusak Ringan/Rusak Berat).
- Setelah dikembalikan, status transaksi berubah menjadi **"Selesai"**.

## 2. Barang Habis Pakai (Consumable)
**Definisi:** Barang yang tidak dikembalikan karena akan habis atau terpasang secara permanen setelah digunakan.
**Contoh:** Kabel, semen, baut, bahan kimia, oli.

**Alur Kerja:**
- Saat barang dikeluarkan, sistem secara otomatis menandai transaksi sebagai **"Selesai"**.
- Stok barang akan berkurang secara permanen.
- Tidak diperlukan proses pengembalian untuk tipe barang ini.

## Fitur Pelaporan
User dapat melihat laporan stok dan memfilter barang berdasarkan tipe ("Barang Kembali" atau "Barang Habis Pakai") untuk memantau aset yang sedang dipinjam vs aset yang sudah dikonsumsi.
