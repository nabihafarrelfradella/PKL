<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('tbl_barangmasuk')->truncate();

$barangs = DB::table('tbl_barang')
    ->join('tbl_jenisbarang','tbl_jenisbarang.jenisbarang_id','=','tbl_barang.jenisbarang_id')
    ->select('tbl_barang.*','tbl_jenisbarang.jenisbarang_keterangan')
    ->get();

$mmyy = Carbon::now()->format('my');
$counter = 1;
$totalRows = 0;

// Stok random per kategori
$stockMap = [
    // Barang Kembali: 3-12 unit per item (perangkat)
    'BK-' . $mmyy . '-001' => 8,   // Router MikroTik RB941
    'BK-' . $mmyy . '-002' => 5,   // Router MikroTik RB750Gr3
    'BK-' . $mmyy . '-003' => 10,  // ONU Fiberhome AN5506
    'BK-' . $mmyy . '-004' => 6,   // AP Ubiquiti UAP-AC-Lite
    'BK-' . $mmyy . '-005' => 9,   // AP TP-Link EAP225
    'BK-' . $mmyy . '-006' => 12,  // Switch TP-Link
    'BK-' . $mmyy . '-007' => 4,   // Switch MikroTik
    'BK-' . $mmyy . '-008' => 15,  // Power Supply Adaptor
    'BK-' . $mmyy . '-009' => 7,   // ODP 8C
    'BK-' . $mmyy . '-010' => 3,   // Optical Power Meter
    'BK-' . $mmyy . '-011' => 5,   // Tang Crimping
    'BK-' . $mmyy . '-012' => 3,   // Tangga Lipat

    // Barang Habis Pakai: 50-500 unit (consumable)
    'HP-' . $mmyy . '-001' => 200, // Kabel UTP Cat6 (meter)
    'HP-' . $mmyy . '-002' => 150, // Kabel FO Drop FTTH (meter)
    'HP-' . $mmyy . '-003' => 300, // Konektor RJ45 (pcs)
    'HP-' . $mmyy . '-004' => 80,  // Pigtail SC/APC (pcs)
    'HP-' . $mmyy . '-005' => 500, // Klem Kabel (pcs)
    'HP-' . $mmyy . '-006' => 400, // Tie Wrap (pcs)
    'HP-' . $mmyy . '-007' => 100, // Isolasi Listrik (pcs)
    'HP-' . $mmyy . '-008' => 250, // Baut + Mur (pcs)
];

// Tanggal masuk: 3 batch dalam 3 bulan terakhir
$dates = [
    Carbon::now()->subDays(90)->format('Y-m-d'),
    Carbon::now()->subDays(45)->format('Y-m-d'),
    Carbon::now()->subDays(7)->format('Y-m-d'),
];

foreach ($barangs as $b) {
    $totalStok = $stockMap[$b->barang_kode] ?? 5;
    $prefix_sn = strtoupper(substr($b->barang_kode, 0, 2));

    // Bagi stok ke 2-3 batch masuk
    $batches = ($totalStok >= 10) ? 3 : 2;
    $stokPerBatch = intdiv($totalStok, $batches);
    $remainder    = $totalStok % $batches;

    for ($batch = 0; $batch < $batches; $batch++) {
        $jmlBatch = $stokPerBatch + ($batch == 0 ? $remainder : 0);
        $tgl = $dates[min($batch, count($dates)-1)];

        if ($b->jenisbarang_keterangan === 'Barang Kembali') {
            // Barang Kembali: 1 baris per unit (tiap unit punya SN)
            for ($unit = 1; $unit <= $jmlBatch; $unit++) {
                $bm_kode = 'BM-' . $mmyy . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $randCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
                $loopIdx  = str_pad($unit, 2, '0', STR_PAD_LEFT);
                $sn       = "{$prefix_sn}-" . str_replace('-', '', $tgl) . "-{$randCode}-{$loopIdx}";
                $unik     = 'BRG-' . now()->timestamp . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

                DB::table('tbl_barangmasuk')->insert([
                    'bm_kode'          => $bm_kode,
                    'barang_kode'      => $b->barang_kode,
                    'customer_id'      => 0,
                    'bm_tanggal'       => $tgl,
                    'bm_jumlah'        => 1,
                    'serial_number'    => $sn,
                    'kode_barang_unik' => $unik,
                    'jam_masuk'        => $tgl . ' 08:00:00',
                    'created_at'       => $tgl,
                    'updated_at'       => $tgl,
                ]);
                $counter++;
                $totalRows++;
            }
        } else {
            // Barang Habis Pakai: 1 baris per batch (banyak unit per baris)
            $bm_kode = 'BM-' . $mmyy . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $randCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
            $sn       = "{$prefix_sn}-" . str_replace('-', '', $tgl) . "-{$randCode}-B" . str_pad($batch+1, 2, '0', STR_PAD_LEFT);
            $unik     = 'BRG-' . now()->timestamp . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

            DB::table('tbl_barangmasuk')->insert([
                'bm_kode'          => $bm_kode,
                'barang_kode'      => $b->barang_kode,
                'customer_id'      => 0,
                'bm_tanggal'       => $tgl,
                'bm_jumlah'        => $jmlBatch,
                'serial_number'    => $sn,
                'kode_barang_unik' => $unik,
                'jam_masuk'        => $tgl . ' 08:00:00',
                'created_at'       => $tgl,
                'updated_at'       => $tgl,
            ]);
            $counter++;
            $totalRows++;
        }
        usleep(1000); // hindari timestamp collision
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "✓ Total baris barang masuk: {$totalRows}\n\n";
echo "=== Ringkasan Stok ===\n";
foreach ($barangs as $b) {
    $jmlMasuk = DB::table('tbl_barangmasuk')->where('barang_kode', $b->barang_kode)->sum('bm_jumlah');
    $type = str_contains($b->barang_kode, 'BK') ? '[BK]' : '[HP]';
    echo "  {$type} {$b->barang_kode} | {$b->barang_nama} → Stok: {$jmlMasuk} {$b->satuan_id}\n";
}
