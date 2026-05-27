<?php
/**
 * Script: Insert RBAC permissions langsung ke database
 * Jalankan via: php artisan tinker < scripts/insert_rbac.php
 * Atau: php scripts/insert_rbac.php (dari root project dengan bootstrap manual)
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('tbl_akses')->whereIn('role_id', [2, 3])->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$menus    = DB::table('tbl_menu')->get()->keyBy('menu_slug');
$submenus = DB::table('tbl_submenu')->get()->keyBy('submenu_redirect');

echo "Menus: " . $menus->count() . "\n";
echo "Submenus: " . $submenus->count() . "\n";
echo "Keys: " . implode(', ', array_keys($submenus->toArray())) . "\n";

$rows = [];
$full     = ['view', 'create', 'update', 'delete'];
$viewOnly = ['view'];

$grantMenu = function ($slug, $roleId, array $types) use ($menus, &$rows) {
    $m = $menus->get($slug);
    if (!$m) { echo "WARNING: menu slug '$slug' not found\n"; return; }
    foreach ($types as $type) {
        $rows[] = ['menu_id' => $m->menu_id, 'role_id' => $roleId, 'akses_type' => $type, 'created_at' => now(), 'updated_at' => now()];
    }
};

$grantSub = function ($redirect, $roleId, array $types) use ($submenus, &$rows) {
    $s = $submenus->get($redirect);
    if (!$s) { echo "WARNING: submenu_redirect '$redirect' not found\n"; return; }
    foreach ($types as $type) {
        $rows[] = ['submenu_id' => $s->submenu_id, 'role_id' => $roleId, 'akses_type' => $type, 'created_at' => now(), 'updated_at' => now()];
    }
};

// ── Admin Gudang (role_id=2) ─────────────────────────────────────────────────
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

// ── Pegawai Teknisi (role_id=3) ───────────────────────────────────────────────
$grantMenu('dashboard', 3, $viewOnly);
$grantMenu('transaksi', 3, $viewOnly);
$grantSub('/barang-masuk',  3, $viewOnly);
$grantSub('/barang-keluar', 3, ['view', 'create']);

// Insert
if (!empty($rows)) {
    foreach (array_chunk($rows, 100) as $chunk) {
        DB::table('tbl_akses')->insert($chunk);
    }
    echo "Inserted " . count($rows) . " rows\n";
}

// Verify
echo "\nFinal count per role:\n";
$counts = DB::table('tbl_akses')
    ->select('role_id', DB::raw('count(*) as total'))
    ->whereIn('role_id', [2, 3])
    ->groupBy('role_id')
    ->get();
foreach ($counts as $c) {
    echo "  role_id={$c->role_id} => {$c->total} permissions\n";
}
