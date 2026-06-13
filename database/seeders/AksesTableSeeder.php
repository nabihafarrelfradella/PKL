<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * AksesTableSeeder
 *
 * Dijalankan saat fresh install (migrate:fresh --seed).
 * Untuk database yang sudah running, gunakan script: scripts/verify_rbac.php
 *
 * Matrix Hak Akses (sesuai screenshot Access Control):
 *   Owner (1)          → bypass via CheckOwnerOnly + CheckRoleUser (tidak perlu entry DB)
 *   Admin Gudang (2)   → dashboard (full), master barang (full), transaksi (full), laporan (full)
 *   Pegawai Teknisi (3)→ dashboard (view), barang masuk (view), barang keluar (view+create)
 */
class AksesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('tbl_akses')->truncate();
        $menus    = DB::table('tbl_menu')->get()->keyBy('menu_slug');
        $submenus = DB::table('tbl_submenu')->get()->keyBy('submenu_redirect');
        $rows     = [];
        $full     = ['view', 'create', 'update', 'delete'];
        $viewOnly = ['view'];

        $grantMenu = function ($slug, $roleId, array $types) use ($menus, &$rows) {
            $m = $menus->get($slug);
            if (!$m) return;
            foreach ($types as $type) {
                $rows[] = [
                    'menu_id'      => $m->menu_id,
                    'submenu_id'   => null,
                    'othermenu_id' => null,
                    'role_id'      => $roleId,
                    'akses_type'   => $type,
                    'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                ];
            }
        };

        $grantSub = function ($redirect, $roleId, array $types) use ($submenus, &$rows) {
            $s = $submenus->get($redirect);
            if (!$s) return;
            foreach ($types as $type) {
                $rows[] = [
                    'menu_id'      => null,
                    'submenu_id'   => $s->submenu_id,
                    'othermenu_id' => null,
                    'role_id'      => $roleId,
                    'akses_type'   => $type,
                    'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                ];
            }
        };

        // ── Admin Gudang (role_id=2) ──────────────────────────────────────────
        $grantMenu('dashboard',     2, $full);
        $grantMenu('master-barang', 2, $viewOnly);
        $grantSub('/jenisbarang',   2, $full);
        $grantSub('/merk',          2, $full);
        $grantSub('/barang',        2, $full);
        $grantMenu('transaksi',     2, $viewOnly);
        $grantSub('/barang-masuk',    2, $full);
        $grantSub('/barang-keluar',   2, $full);
        $grantSub('/barang-tracking', 2, $full);
        $grantMenu('laporan',         2, $viewOnly);
        $grantSub('/lap-barang-masuk',  2, $full);
        $grantSub('/lap-barang-keluar', 2, $full);
        $grantSub('/lap-stok-barang',   2, $full);

        // ── Pegawai Teknisi (role_id=3) ───────────────────────────────────────
        $grantMenu('dashboard', 3, $viewOnly);
        $grantMenu('transaksi', 3, $viewOnly);
        $grantSub('/barang-masuk',  3, $viewOnly);
        $grantSub('/barang-keluar', 3, ['view', 'create']);

        // Insert semua rows
        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('tbl_akses')->insert($chunk);
        }
    }
}
