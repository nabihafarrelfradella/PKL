<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

// Cek tabel customer
echo "=== tbl_customer columns ===\n";
try {
    foreach(DB::select('SHOW COLUMNS FROM tbl_customer') as $c)
        echo "  {$c->Field} | {$c->Type} | Null:{$c->Null}\n";
} catch(\Exception $e) { echo "ERROR: {$e->getMessage()}\n"; }

// Cek FK constraints
echo "\n=== FK constraints on tbl_barangmasuk ===\n";
try {
    $fks = DB::select("
        SELECT COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_NAME = 'tbl_barangmasuk'
          AND TABLE_SCHEMA = DATABASE()
          AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    foreach($fks as $f)
        echo "  {$f->COLUMN_NAME} -> {$f->REFERENCED_TABLE_NAME}.{$f->REFERENCED_COLUMN_NAME}\n";
    if (empty($fks)) echo "  (no FK constraints)\n";
} catch(\Exception $e) { echo "ERROR: {$e->getMessage()}\n"; }

// Test barang masuk insert simulasi
echo "\n=== tbl_barangmasuk current count ===\n";
echo "  Total rows: " . DB::table('tbl_barangmasuk')->count() . "\n";

echo "\n=== tbl_barangkeluar current count ===\n";
echo "  Total rows: " . DB::table('tbl_barangkeluar')->count() . "\n";
