<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Sinkronisasi tbl_submenu dengan route middleware, lalu setup RBAC final.
 *
 * Masalah yang ditemukan: 
 *   tbl_submenu hanya berisi Role, List, Akses (untuk User Management).
 *   Route middleware seperti checkRoleUser:/barang-masuk,submenu tidak bisa bekerja
 *   karena submenu dengan redirect '/barang-masuk' tidak ada di database.
 *
 * Solusi:
 *   1. Tambahkan submenu yang diperlukan ke tbl_submenu (jika belum ada)
 *   2. Tambahkan menu parent yang diperlukan ke tbl_menu (jika belum ada)
 *   3. Reset dan isi ulang tbl_akses sesuai matrix RBAC final
 *   4. Hapus role duplikat (role_id 4,5,6)
 */
return new class extends Migration
{
    public function up()
    {
        // ── 1. Hapus role duplikat yang tersisa dari seeder lama ──────────────
        // Role yang valid: 1=Owner, 2=Admin Gudang, 3=Pegawai Teknisi
        DB::table('tbl_role')->where('role_id', '>', 3)->delete();

        // ── 2. Pastikan menu parent ada di tbl_menu ───────────────────────────
        $now = now();
        $existingSlugs = DB::table('tbl_menu')->pluck('menu_slug')->toArray();

        $menusToAdd = [
            ['menu_id' => 1000000001, 'menu_judul' => 'Master Barang', 'menu_slug' => 'master-barang', 'menu_icon' => 'package', 'menu_redirect' => '-', 'menu_sort' => 2, 'menu_type' => 2],
            ['menu_id' => 1000000002, 'menu_judul' => 'Transaksi',     'menu_slug' => 'transaksi',    'menu_icon' => 'repeat',  'menu_redirect' => '-', 'menu_sort' => 3, 'menu_type' => 2],
            ['menu_id' => 1000000003, 'menu_judul' => 'Laporan',       'menu_slug' => 'laporan',      'menu_icon' => 'printer', 'menu_redirect' => '-', 'menu_sort' => 4, 'menu_type' => 2],
        ];

        foreach ($menusToAdd as $menu) {
            if (!in_array($menu['menu_slug'], $existingSlugs)) {
                DB::table('tbl_menu')->insert(array_merge($menu, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        // ── 3. Pastikan submenu ada di tbl_submenu ────────────────────────────
        // Ambil ulang menu setelah insert
        $menus = DB::table('tbl_menu')->get()->keyBy('menu_slug');
        $existingRedirects = DB::table('tbl_submenu')->pluck('submenu_redirect')->toArray();

        $submenusToAdd = [
            // Master Barang
            ['menu_slug' => 'master-barang', 'submenu_judul' => 'Jenis Barang', 'submenu_slug' => 'jenisbarang', 'submenu_redirect' => '/jenisbarang', 'submenu_sort' => 1],
            ['menu_slug' => 'master-barang', 'submenu_judul' => 'Merk Barang',  'submenu_slug' => 'merk',        'submenu_redirect' => '/merk',        'submenu_sort' => 2],
            ['menu_slug' => 'master-barang', 'submenu_judul' => 'Data Barang',  'submenu_slug' => 'barang',      'submenu_redirect' => '/barang',       'submenu_sort' => 3],
            // Transaksi
            ['menu_slug' => 'transaksi', 'submenu_judul' => 'Barang Masuk',    'submenu_slug' => 'barang-masuk',    'submenu_redirect' => '/barang-masuk',    'submenu_sort' => 1],
            ['menu_slug' => 'transaksi', 'submenu_judul' => 'Barang Keluar',   'submenu_slug' => 'barang-keluar',   'submenu_redirect' => '/barang-keluar',   'submenu_sort' => 2],
            ['menu_slug' => 'transaksi', 'submenu_judul' => 'Barang Tracking', 'submenu_slug' => 'barang-tracking', 'submenu_redirect' => '/barang-tracking', 'submenu_sort' => 3],
            // Laporan
            ['menu_slug' => 'laporan', 'submenu_judul' => 'Lap. Barang Masuk',  'submenu_slug' => 'lap-barang-masuk',  'submenu_redirect' => '/lap-barang-masuk',  'submenu_sort' => 1],
            ['menu_slug' => 'laporan', 'submenu_judul' => 'Lap. Barang Keluar', 'submenu_slug' => 'lap-barang-keluar', 'submenu_redirect' => '/lap-barang-keluar', 'submenu_sort' => 2],
            ['menu_slug' => 'laporan', 'submenu_judul' => 'Lap. Stok Barang',   'submenu_slug' => 'lap-stok-barang',   'submenu_redirect' => '/lap-stok-barang',   'submenu_sort' => 3],
        ];

        foreach ($submenusToAdd as $sub) {
            if (!in_array($sub['submenu_redirect'], $existingRedirects)) {
                $parentMenu = $menus->get($sub['menu_slug']);
                if ($parentMenu) {
                    DB::table('tbl_submenu')->insert([
                        'menu_id'          => $parentMenu->menu_id,
                        'submenu_judul'    => $sub['submenu_judul'],
                        'submenu_slug'     => $sub['submenu_slug'],
                        'submenu_redirect' => $sub['submenu_redirect'],
                        'submenu_sort'     => $sub['submenu_sort'],
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                }
            }
        }

        // ── 4. Reset & isi ulang tbl_akses dengan matrix RBAC final ──────────
        DB::table('tbl_akses')->truncate();

        // Ambil ulang setelah insert
        $menus    = DB::table('tbl_menu')->get()->keyBy('menu_slug');
        $submenus = DB::table('tbl_submenu')->get()->keyBy('submenu_redirect');

        $rows = [];
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
                    'created_at'   => now(),
                    'updated_at'   => now(),
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
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        };

        // ── Admin Gudang (role_id=2): full akses semua kecuali User Management ──
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

        // ── Pegawai Teknisi (role_id=3): terbatas ─────────────────────────────
        $grantMenu('dashboard', 3, $viewOnly);        // Dashboard: view saja
        $grantMenu('transaksi', 3, $viewOnly);        // Parent transaksi: view (untuk sidebar)
        $grantSub('/barang-masuk',  3, $viewOnly);    // Barang Masuk: lihat saja
        $grantSub('/barang-keluar', 3, ['view', 'create']); // Barang Keluar: lihat + ajukan peminjaman

        // Insert semua rows dalam batch
        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('tbl_akses')->insert($chunk);
        }

        \Illuminate\Support\Facades\Log::info('[RBAC v2] Permissions applied. Rows: ' . count($rows) . ' | Submenus: ' . $submenus->count());
    }

    public function down()
    {
        // Hapus submenu yang ditambahkan migration ini
        DB::table('tbl_submenu')->whereIn('submenu_redirect', [
            '/jenisbarang', '/merk', '/barang',
            '/barang-masuk', '/barang-keluar', '/barang-tracking',
            '/lap-barang-masuk', '/lap-barang-keluar', '/lap-stok-barang',
        ])->delete();

        // Hapus menu parent yang ditambahkan
        DB::table('tbl_menu')->whereIn('menu_slug', ['master-barang', 'transaksi', 'laporan'])->delete();

        // Reset akses
        DB::table('tbl_akses')->truncate();
    }
};
