<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Seed tabel role dengan 3 role sistem Alfatindo:
     *  1 = Owner          → akses penuh, hardcoded tidak bisa diubah lewat UI
     *  2 = Admin Gudang   → kelola barang, transaksi, laporan. Dikelola oleh Owner.
     *  3 = Pegawai Teknisi → lihat barang, ajukan peminjaman. Dikelola oleh Owner.
     */
    public function run()
    {
        DB::table('tbl_role')->insert([
            [
                'role_id'    => 1,
                'role_title' => 'Owner',
                'role_slug'  => 'owner',
                'role_desc'  => 'Akses penuh ke seluruh sistem. Tidak dapat diubah.',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'role_id'    => 2,
                'role_title' => 'Admin Gudang',
                'role_slug'  => 'admin-gudang',
                'role_desc'  => 'Mengelola data barang, transaksi masuk/keluar, dan laporan.',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'role_id'    => 3,
                'role_title' => 'Pegawai Teknisi',
                'role_slug'  => 'pegawai-teknisi',
                'role_desc'  => 'Melihat data barang dan mengajukan peminjaman barang.',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
