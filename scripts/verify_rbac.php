<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

// Bersihkan SEMUA akses untuk role 2 dan 3, termasuk yang tidak valid
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('tbl_akses')->whereIn('role_id', [2, 3])->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Deleted old permissions for role 2 & 3\n";

$menus    = DB::table('tbl_menu')->get()->keyBy('menu_slug');
$submenus = DB::table('tbl_submenu')->get()->keyBy('submenu_redirect');

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

echo "Total rows to insert: " . count($rows) . "\n";
echo "Menu rows: " . count(array_filter($rows, fn($r) => isset($r['menu_id']))) . "\n";
echo "Submenu rows: " . count(array_filter($rows, fn($r) => isset($r['submenu_id']))) . "\n";

// Insert satu per satu untuk debug
$inserted = 0;
foreach ($rows as $row) {
    DB::table('tbl_akses')->insert($row);
    $inserted++;
}
echo "Inserted: $inserted\n";

// Verify final state
echo "\n=== FINAL VERIFICATION ===\n";
echo "Total akses in DB: " . DB::table('tbl_akses')->count() . "\n";
echo "\nAkses per role:\n";
foreach (DB::table('tbl_akses')->select('role_id', DB::raw('count(*) as total'))->whereIn('role_id', [1,2,3])->groupBy('role_id')->orderBy('role_id')->get() as $c) {
    echo "  role_id={$c->role_id} => {$c->total}\n";
}

echo "\nAdmin Gudang (2) detail:\n";
foreach (DB::table('tbl_akses')->where('role_id', 2)->orderBy('akses_id')->get() as $a) {
    if ($a->menu_id) {
        $menu = DB::table('tbl_menu')->where('menu_id', $a->menu_id)->first();
        echo "  MENU: " . ($menu ? $menu->menu_judul : '?') . " | {$a->akses_type}\n";
    } elseif ($a->submenu_id) {
        $sub = DB::table('tbl_submenu')->where('submenu_id', $a->submenu_id)->first();
        echo "  SUBMENU: " . ($sub ? $sub->submenu_judul : '?') . " | {$a->akses_type}\n";
    }
}

echo "\nPegawai Teknisi (3) detail:\n";
foreach (DB::table('tbl_akses')->where('role_id', 3)->orderBy('akses_id')->get() as $a) {
    if ($a->menu_id) {
        $menu = DB::table('tbl_menu')->where('menu_id', $a->menu_id)->first();
        echo "  MENU: " . ($menu ? $menu->menu_judul : '?') . " | {$a->akses_type}\n";
    } elseif ($a->submenu_id) {
        $sub = DB::table('tbl_submenu')->where('submenu_id', $a->submenu_id)->first();
        echo "  SUBMENU: " . ($sub ? $sub->submenu_judul : '?') . " | {$a->akses_type}\n";
    }
}
