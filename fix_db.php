<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\BarangkeluarModel;

$brokenRecords = BarangkeluarModel::whereNull('kode_barang_unik')
    ->where(function($q) {
        $q->where('serial_number', 'LIKE', 'HP-%')
          ->orWhere('serial_number', 'LIKE', 'BK-%');
    })->get();

foreach ($brokenRecords as $record) {
    $record->kode_barang_unik = $record->serial_number;
    $record->serial_number = null;
    $record->save();
}

echo "Fixed " . count($brokenRecords) . " records.\n";
