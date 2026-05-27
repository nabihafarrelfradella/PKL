<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

echo "=== tbl_barang columns ===\n";
foreach(DB::select('SHOW COLUMNS FROM tbl_barang') as $c)
    echo "  {$c->Field} | {$c->Type} | Null:{$c->Null}\n";

echo "\n=== tbl_barangmasuk columns ===\n";
foreach(DB::select('SHOW COLUMNS FROM tbl_barangmasuk') as $c)
    echo "  {$c->Field} | {$c->Type} | Null:{$c->Null}\n";

echo "\n=== tbl_barangkeluar columns ===\n";
foreach(DB::select('SHOW COLUMNS FROM tbl_barangkeluar') as $c)
    echo "  {$c->Field} | {$c->Type} | Null:{$c->Null}\n";

echo "\n=== MERK existing ===\n";
foreach(DB::table('tbl_merk')->get() as $m)
    echo "  ID:{$m->merk_id} | {$m->merk_nama}\n";

echo "\n=== JENIS BARANG existing ===\n";
foreach(DB::table('tbl_jenisbarang')->get() as $j)
    echo "  ID:{$j->jenisbarang_id} | {$j->jenisbarang_nama} | ket:{$j->jenisbarang_ket}\n";

echo "\n=== BARANG existing ===\n";
echo "  Total: " . DB::table('tbl_barang')->count() . "\n";
foreach(DB::table('tbl_barang')->limit(5)->get() as $b)
    echo "  {$b->barang_kode} | {$b->barang_nama} | stok:{$b->barang_stok}\n";

echo "\n=== BARANGMASUK sample (SN) ===\n";
foreach(DB::table('tbl_barangmasuk')->limit(5)->get() as $b)
    echo "  SN:[{$b->serial_number}] | unik:[{$b->kode_barang_unik}]\n";

echo "\n=== tbl_user role distribution ===\n";
foreach(DB::select('SELECT role_id, count(*) as cnt FROM tbl_user GROUP BY role_id') as $r)
    echo "  role_id:{$r->role_id} count:{$r->cnt}\n";
