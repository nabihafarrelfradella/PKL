<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

// Cek schema tbl_jenisbarang dan tbl_merk
echo "=== tbl_jenisbarang FULL ===\n";
foreach(DB::select('SHOW COLUMNS FROM tbl_jenisbarang') as $c)
    echo "  {$c->Field} | {$c->Type} | Null:{$c->Null}\n";

echo "\n=== tbl_merk FULL ===\n";
foreach(DB::select('SHOW COLUMNS FROM tbl_merk') as $c)
    echo "  {$c->Field} | {$c->Type} | Null:{$c->Null}\n";
