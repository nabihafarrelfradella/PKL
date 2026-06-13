<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Final RBAC Setup untuk Alfatindo Inventory System
 *
 * Role:
 *   1 = Owner          → bypass semua permission (CheckOwnerOnly), akses tidak bisa diubah via UI
 *   2 = Admin Gudang   → full akses: dashboard, master barang, transaksi, laporan
 *   3 = Pegawai Teknisi → terbatas: dashboard (view), barang masuk (view), barang keluar (view+create)
 *
 * Struktur menu yang ada di DB (berdasarkan migration sebelumnya):
 *   tbl_menu  → menu utama (Dashboard, Master Barang, Transaksi, Laporan, User Management)
 *   tbl_submenu → sub-menu di bawah menu utama
 */
return new class extends Migration
{
    public function up()
    {
        // ── 1. Fix nama role ──────────────────────────────────────────────────
        DB::table('tbl_role')->where('role_id', 1)->update([
            'role_title' => 'Owner',
            'role_slug'  => 'owner',
            'role_desc'  => 'Akses penuh ke seluruh sistem. Tidak dapat diubah.',
        ]);

        DB::table('tbl_role')->where('role_id', 2)->update([
            'role_title' => 'Admin Gudang',
            'role_slug'  => 'admin-gudang',
            'role_desc'  => 'Mengelola data barang, transaksi masuk/keluar, dan laporan.',
        ]);

        DB::table('tbl_role')->where('role_id', 3)->update([
            'role_title' => 'Pegawai Teknisi',
            'role_slug'  => 'pegawai-teknisi',
            'role_desc'  => 'Melihat data barang dan mengajukan peminjaman barang.',
        ]);

        // ── 2. Hapus semua permission lama dan isi ulang ─────────────────────
        DB::table('tbl_akses')->truncate();

        // Ambil semua menu dan submenu
        $menus    = DB::table('tbl_menu')->get()->keyBy('menu_slug');
        $submenus = DB::table('tbl_submenu')->get()->keyBy('submenu_redirect');

        $rows = [];

        // Helper: tambah akses berdasarkan menu_id
        $grantMenu = function ($menuSlug, $roleId, array $types) use ($menus, &$rows) {
            $menu = $menus->get($menuSlug);
            if (!$menu) return;
            foreach ($types as $type) {
                $rows[] = [
                    'menu_id'      => $menu->menu_id,
                    'submenu_id'   => null,
                    'othermenu_id' => null,
                    'role_id'      => $roleId,
                    'akses_type'   => $type,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        };

        // Helper: tambah akses berdasarkan submenu_redirect
        $grantSubmenu = function ($redirect, $roleId, array $types) use ($submenus, &$rows) {
            $sub = $submenus->get($redirect);
            if (!$sub) return;
            foreach ($types as $type) {
                $rows[] = [
                    'menu_id'      => null,
                    'submenu_id'   => $sub->submenu_id,
                    'othermenu_id' => null,
                    'role_id'      => $roleId,
                    'akses_type'   => $type,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        };

        $full = ['view', 'create', 'update', 'delete'];
        $viewOnly = ['view'];

        // ── 3. Permission: Admin Gudang (role_id = 2) ─────────────────────────
        // Dashboard
        $grantMenu('dashboard', 2, $full);

        // Master Barang (menu parent — beri view agar sidebar bisa terbuka)
        $grantMenu('master-barang', 2, ['view']);
        $grantSubmenu('/jenisbarang', 2, $full);
        $grantSubmenu('/merk', 2, $full);
        $grantSubmenu('/barang', 2, $full);

        // Transaksi (menu parent)
        $grantMenu('transaksi', 2, ['view']);
        $grantSubmenu('/barang-masuk', 2, $full);
        $grantSubmenu('/barang-keluar', 2, $full);
        $grantSubmenu('/barang-tracking', 2, $full);

        // Laporan (menu parent)
        $grantMenu('laporan', 2, ['view']);
        $grantSubmenu('/lap-barang-masuk', 2, $full);
        $grantSubmenu('/lap-barang-keluar', 2, $full);
        $grantSubmenu('/lap-stok-barang', 2, $full);

        // User Management — Admin Gudang TIDAK boleh akses
        // (diproteksi via CheckOwnerOnly middleware)

        // ── 4. Permission: Pegawai Teknisi (role_id = 3) ─────────────────────
        // Dashboard — view only
        $grantMenu('dashboard', 3, $viewOnly);

        // Transaksi: Barang Masuk — view only
        $grantMenu('transaksi', 3, $viewOnly);
        $grantSubmenu('/barang-masuk', 3, $viewOnly);

        // Transaksi: Barang Keluar — view + create (ajukan peminjaman)
        $grantSubmenu('/barang-keluar', 3, ['view', 'create']);

        // Master Barang — TIDAK ada akses
        // Laporan — TIDAK ada akses
        // Barang Tracking — TIDAK ada akses
        // User Management — TIDAK ada akses

        // Insert semua rows
        if (!empty($rows)) {
            // Batch insert per 100 rows untuk menghindari query terlalu besar
            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('tbl_akses')->insert($chunk);
            }
        }

        \Illuminate\Support\Facades\Log::info('[RBAC Migration] Final RBAC permissions applied. Total rows: ' . count($rows));
    }

    public function down()
    {
        // Revert nama role ke nilai lama
        DB::table('tbl_role')->where('role_id', 1)->update([
            'role_title' => 'Admin',
            'role_slug'  => 'admin',
        ]);
        DB::table('tbl_role')->where('role_id', 2)->update([
            'role_title' => 'Staff Gudang',
            'role_slug'  => 'staff-gudang',
        ]);
        DB::table('tbl_role')->where('role_id', 3)->update([
            'role_title' => 'Pegawai',
            'role_slug'  => 'pegawai',
        ]);

        // Hapus semua permission yang ditambahkan
        DB::table('tbl_akses')->truncate();
    }
};
