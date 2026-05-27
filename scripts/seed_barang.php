<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// ─── 1. JENIS BARANG ──────────────────────────────────────────────────────────
DB::table('tbl_jenisbarang')->truncate();
DB::table('tbl_jenisbarang')->insert([
    [
        'jenisbarang_nama'         => 'Barang Kembali',
        'jenisbarang_slug'         => 'barang-kembali',
        'jenisbarang_keterangan'   => 'Barang Kembali',
        'created_at' => now(), 'updated_at' => now()
    ],
    [
        'jenisbarang_nama'         => 'Barang Habis Pakai',
        'jenisbarang_slug'         => 'barang-habis-pakai',
        'jenisbarang_keterangan'   => 'Barang Habis Pakai',
        'created_at' => now(), 'updated_at' => now()
    ],
]);
echo "✓ Jenis Barang: 2 data\n";

// ─── 2. MERK BARANG ───────────────────────────────────────────────────────────
DB::table('tbl_merk')->truncate();
$merks = [
    ['merk_nama' => 'MikroTik',   'merk_slug' => 'mikrotik',   'merk_keterangan' => 'Router & Networking'],
    ['merk_nama' => 'TP-Link',    'merk_slug' => 'tp-link',    'merk_keterangan' => 'Networking'],
    ['merk_nama' => 'Ubiquiti',   'merk_slug' => 'ubiquiti',   'merk_keterangan' => 'Enterprise Wireless'],
    ['merk_nama' => 'Huawei',     'merk_slug' => 'huawei',     'merk_keterangan' => 'Telecom Equipment'],
    ['merk_nama' => 'D-Link',     'merk_slug' => 'd-link',     'merk_keterangan' => 'Networking'],
    ['merk_nama' => 'Cisco',      'merk_slug' => 'cisco',      'merk_keterangan' => 'Enterprise Networking'],
    ['merk_nama' => 'Belden',     'merk_slug' => 'belden',     'merk_keterangan' => 'Kabel & Konektor'],
    ['merk_nama' => 'APC',        'merk_slug' => 'apc',        'merk_keterangan' => 'Power & UPS'],
    ['merk_nama' => 'Fiberhome',  'merk_slug' => 'fiberhome',  'merk_keterangan' => 'Fiber Optic'],
    ['merk_nama' => 'Generic',    'merk_slug' => 'generic',    'merk_keterangan' => 'Produk Umum'],
];
foreach ($merks as $m) {
    $m['created_at'] = now(); $m['updated_at'] = now();
    DB::table('tbl_merk')->insert($m);
}
$merkMap = DB::table('tbl_merk')->get()->keyBy('merk_slug');
echo "✓ Merk: " . count($merks) . " data\n";

// ─── 3. BARANG ───────────────────────────────────────────────────────────────
DB::table('tbl_barang')->truncate();
$jenisBK = DB::table('tbl_jenisbarang')->where('jenisbarang_keterangan', 'Barang Kembali')->first();
$jenisHP = DB::table('tbl_jenisbarang')->where('jenisbarang_keterangan', 'Barang Habis Pakai')->first();
$mmyy = Carbon::now()->format('my');

$barangs = [
    // === BARANG KEMBALI (router, alat, perangkat) ===
    ['kode'=>"BK-{$mmyy}-001",'nama'=>'Router MikroTik RB941',              'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'mikrotik'],
    ['kode'=>"BK-{$mmyy}-002",'nama'=>'Router MikroTik RB750Gr3 (hEX lite)', 'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'mikrotik'],
    ['kode'=>"BK-{$mmyy}-003",'nama'=>'ONU/ONT Fiberhome AN5506',            'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'fiberhome'],
    ['kode'=>"BK-{$mmyy}-004",'nama'=>'Access Point Ubiquiti UAP-AC-Lite',   'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'ubiquiti'],
    ['kode'=>"BK-{$mmyy}-005",'nama'=>'Access Point TP-Link EAP225',         'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'tp-link'],
    ['kode'=>"BK-{$mmyy}-006",'nama'=>'Switch TP-Link TL-SF1008D 8-Port',    'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'tp-link'],
    ['kode'=>"BK-{$mmyy}-007",'nama'=>'Switch MikroTik CSS106-5G-1S',        'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'mikrotik'],
    ['kode'=>"BK-{$mmyy}-008",'nama'=>'Power Supply Adaptor 12V 1A',         'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'generic'],
    ['kode'=>"BK-{$mmyy}-009",'nama'=>'ODP (Optical Distribution Point) 8C', 'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'fiberhome'],
    ['kode'=>"BK-{$mmyy}-010",'nama'=>'Optical Power Meter (OPM) Digital',   'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'generic'],
    ['kode'=>"BK-{$mmyy}-011",'nama'=>'Tang Crimping RJ45 + LAN Tester',     'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'generic'],
    ['kode'=>"BK-{$mmyy}-012",'nama'=>'Tangga Lipat Aluminium 5m',           'jenis'=>$jenisBK,'satuan'=>'Unit','merk'=>'generic'],

    // === BARANG HABIS PAKAI (consumable) ===
    ['kode'=>"HP-{$mmyy}-001",'nama'=>'Kabel UTP Cat6 Belden',               'jenis'=>$jenisHP,'satuan'=>'Meter','merk'=>'belden'],
    ['kode'=>"HP-{$mmyy}-002",'nama'=>'Kabel Fiber Optik Drop Cable FTTH',   'jenis'=>$jenisHP,'satuan'=>'Meter','merk'=>'fiberhome'],
    ['kode'=>"HP-{$mmyy}-003",'nama'=>'Konektor RJ45 Cat6',                  'jenis'=>$jenisHP,'satuan'=>'Pcs', 'merk'=>'generic'],
    ['kode'=>"HP-{$mmyy}-004",'nama'=>'Pigtail SC/APC 1m',                   'jenis'=>$jenisHP,'satuan'=>'Pcs', 'merk'=>'fiberhome'],
    ['kode'=>"HP-{$mmyy}-005",'nama'=>'Klem Kabel Plastik (Cable Clip)',      'jenis'=>$jenisHP,'satuan'=>'Pcs', 'merk'=>'generic'],
    ['kode'=>"HP-{$mmyy}-006",'nama'=>'Tali Pengikat / Tie Wrap',             'jenis'=>$jenisHP,'satuan'=>'Pcs', 'merk'=>'generic'],
    ['kode'=>"HP-{$mmyy}-007",'nama'=>'Isolasi Listrik (Electrical Tape)',    'jenis'=>$jenisHP,'satuan'=>'Pcs', 'merk'=>'generic'],
    ['kode'=>"HP-{$mmyy}-008",'nama'=>'Baut + Mur Stainless M6',              'jenis'=>$jenisHP,'satuan'=>'Pcs', 'merk'=>'generic'],
];

foreach ($barangs as $b) {
    $merkId = $merkMap->get($b['merk'])->merk_id ?? null;
    DB::table('tbl_barang')->insert([
        'barang_kode'    => $b['kode'],
        'barang_nama'    => $b['nama'],
        'jenisbarang_id' => $b['jenis']->jenisbarang_id,
        'satuan_id'      => $b['satuan'],
        'merk_id'        => $merkId,
        'barang_slug'    => strtolower(preg_replace('/[^A-Za-z0-9]+/','-',$b['nama'])),
        'barang_harga'   => '0',
        'barang_stok'    => 0,
        'tipe_barang'    => $b['jenis']->jenisbarang_keterangan,
        'barang_gambar'  => 'image.png',
        'serial_number'  => '-',
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);
}
echo "✓ Barang: " . count($barangs) . " data (12 BK + 8 HP)\n";

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// Verifikasi
echo "\n=== VERIFIKASI ===\n";
echo "  Merk: " . DB::table('tbl_merk')->count() . "\n";
echo "  Jenis Barang: " . DB::table('tbl_jenisbarang')->count() . "\n";
echo "  Barang: " . DB::table('tbl_barang')->count() . "\n";
echo "\nDaftar Barang:\n";
foreach (DB::table('tbl_barang')
    ->join('tbl_jenisbarang','tbl_jenisbarang.jenisbarang_id','=','tbl_barang.jenisbarang_id')
    ->join('tbl_merk','tbl_merk.merk_id','=','tbl_barang.merk_id')
    ->select('barang_kode','barang_nama','jenisbarang_nama','merk_nama','satuan_id')
    ->get() as $b) {
    $type = str_contains($b->barang_kode, 'BK') ? '[KEMBALI ]' : '[HABIS P.]';
    echo "  {$type} [{$b->barang_kode}] {$b->barang_nama} | {$b->merk_nama} | {$b->satuan_id}\n";
}
