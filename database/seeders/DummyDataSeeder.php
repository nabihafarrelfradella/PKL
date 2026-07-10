<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('==> Seeding Merk...');
        $this->seedMerk();

        $this->command->info('==> Seeding Jenis Barang...');
        $this->seedJenisBarang();

        $this->command->info('==> Seeding Master Barang (~110 items)...');
        $this->seedBarang();

        $this->command->info('==> Seeding Barang Masuk (~48 records)...');
        $this->seedBarangMasuk();

        $this->command->info('==> Seeding Barang Keluar (~170 records)...');
        $this->seedBarangKeluar();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('==> Done! All dummy data inserted successfully.');
    }

    // ================================================================
    // MERK
    // ================================================================
    private function seedMerk()
    {
        $merks = [
            'ZTE', 'Zimlink', 'TP-Link', 'Tenda', 'Totolink', 'Huawei',
            'Fastlink', 'Netvibes', 'GGC Link', 'C-Link', 'XSF', 'Sigma',
            'Merys', 'Kingtype', 'Interluck', 'Nokia', 'Mimosa', 'Generic',
        ];
        foreach ($merks as $merk) {
            DB::table('tbl_merk')->updateOrInsert(
                ['merk_nama' => $merk],
                [
                    'merk_nama'       => $merk,
                    'merk_slug'       => Str::slug($merk),
                    'merk_keterangan' => $merk,
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now(),
                ]
            );
        }
    }

    // ================================================================
    // JENIS BARANG
    // ================================================================
    private function seedJenisBarang()
    {
        $jenis = [
            ['jenisbarang_nama' => 'Barang Kembali',     'jenisbarang_slug' => 'barang-kembali'],
            ['jenisbarang_nama' => 'Barang Habis Pakai', 'jenisbarang_slug' => 'barang-habis-pakai'],
        ];
        foreach ($jenis as $j) {
            DB::table('tbl_jenisbarang')->updateOrInsert(
                ['jenisbarang_nama' => $j['jenisbarang_nama']],
                array_merge($j, [
                    'jenisbarang_keterangan' => $j['jenisbarang_nama'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }

    // ================================================================
    // MASTER BARANG
    // Data bersumber dari BARANG.xlsx dan LAPORAN STOK BARANG
    // Format: [kode, nama, satuan, stok_awal, tipe_barang]
    // ================================================================
    private function seedBarang()
    {
        $barangs = [
            // ---- MODEM ----
            ['MDM-F663',    'Modem ZTE F663V3A',         'Unit',  5,    'Barang Kembali'],
            ['MDM-F670L-A', 'Modem ZTE F670L Lama',      'Unit',  1,    'Barang Kembali'],
            ['MDM-F670L-B', 'Modem ZTE F670L Baru',      'Unit',  10,   'Barang Kembali'],
            ['MDM-F609',    'Modem ZTE F609',             'Unit',  3,    'Barang Kembali'],
            ['MDM-FASLINK', 'Modem Fastlink',             'Unit',  7,    'Barang Kembali'],
            ['MDM-TJ653',   'Modem TJ653',                'Unit',  0,    'Barang Kembali'],
            ['MDM-F660',    'Modem ZTE F660',             'Unit',  16,   'Barang Kembali'],
            ['MDM-ZIMLINK', 'Modem Zimlink',              'Unit',  3,    'Barang Kembali'],
            ['MDM-GGC',     'Modem GGC Link',             'Unit',  0,    'Barang Kembali'],
            ['MDM-F620',    'Modem ZTE F620',             'Unit',  1,    'Barang Kembali'],
            ['MDM-NETVIBES','Modem Netvibes',             'Unit',  1,    'Barang Kembali'],
            ['MDM-TJ680X',  'Modem TJ680x',              'Unit',  4,    'Barang Kembali'],
            ['MDM-CLINK',   'Modem C-Link',               'Unit',  1,    'Barang Kembali'],
            ['MDM-XSF609',  'Modem XSF609',              'Unit',  4,    'Barang Kembali'],
            ['MDM-SIGMA',   'Modem Sigma',                'Unit',  0,    'Barang Kembali'],
            // ---- ROUTER ----
            ['RTR-WR840',   'Router TP-Link WR840',       'Unit',  10,   'Barang Kembali'],
            ['RTR-TENDA',   'Router Tenda',               'Unit',  5,    'Barang Kembali'],
            ['RTR-TPLINK',  'Router TP-Link',             'Unit',  2,    'Barang Kembali'],
            ['RTR-MERYS',   'Router Merys',               'Unit',  1,    'Barang Kembali'],
            ['RTR-KINGTYPE','Router Kingtype',            'Unit',  1,    'Barang Kembali'],
            ['RTR-HUAWEI',  'Router Huawei',              'Unit',  1,    'Barang Kembali'],
            ['RTR-TOTOLINK','Router Totolink',            'Unit',  18,   'Barang Kembali'],
            // ---- KABEL ----
            ['KBL-SSP',     'Kabel SSP',                  'Meter', 868,  'Barang Habis Pakai'],
            ['KBL-LAN',     'Kabel LAN Interluck',        'Meter', 121,  'Barang Habis Pakai'],
            ['KBL-DC-1C',   'Kabel Dropcore 1C',          'Meter', 0,    'Barang Habis Pakai'],
            ['KBL-DC-12C',  'Kabel Dropcore 12C',         'Meter', 1000, 'Barang Habis Pakai'],
            ['KBL-DC-24C',  'Kabel Dropcore 24C',         'Meter', 1000, 'Barang Habis Pakai'],
            ['KBL-DC-96C',  'Kabel Dropcore 96C',         'Meter', 500,  'Barang Habis Pakai'],
            ['KBL-DAC',     'Kabel DAC',                  'Unit',  19,   'Barang Kembali'],
            // ---- AKSESORIS JARINGAN ----
            ['RJ45-ZIMLINK','RJ45 Zimlink',               'Pcs',   31,   'Barang Habis Pakai'],
            ['PROTEKTOR',   'Protektor',                  'Pcs',   3,    'Barang Habis Pakai'],
            ['PTCORE-SCLC', 'Patchcore SC-LC',            'Pcs',   4,    'Barang Habis Pakai'],
            ['PTCORE-SCSC', 'Patchcore SC-SC',            'Pcs',   4,    'Barang Habis Pakai'],
            ['PTCORE-LCLC', 'Patchcore LC-LC Duplex 5M',  'Pcs',  3,    'Barang Habis Pakai'],
            ['PATCHORE',    'Patchore',                   'Pcs',   36,   'Barang Habis Pakai'],
            ['PIGTAIL',     'Pigtail',                    'Pcs',   7,    'Barang Habis Pakai'],
            ['SOLASI',      'Solasi',                     'Pcs',   26,   'Barang Habis Pakai'],
            ['PAKU-KLEM',   'Paku Klem 5-6mm',            'Pcs',   25,   'Barang Habis Pakai'],
            ['STRAP-KABEL', 'Strap Kabel',                'Pcs',   0,    'Barang Habis Pakai'],
            // ---- CONVERTER ----
            ['CVR-SET',     'Converter Single Set',        'Unit',  1,    'Barang Kembali'],
            ['CVR-A',       'Converter A',                 'Unit',  6,    'Barang Kembali'],
            ['CVR-B',       'Converter B',                 'Unit',  6,    'Barang Kembali'],
            ['CVR-2SC',     'Converter 2SC',               'Unit',  8,    'Barang Kembali'],
            ['CVR-3SC',     'Converter 3SC',               'Unit',  1,    'Barang Kembali'],
            ['CVR-4SC',     'Converter 4SC',               'Unit',  0,    'Barang Kembali'],
            ['CVR-6SC',     'Converter 6SC',               'Unit',  2,    'Barang Kembali'],
            // ---- ADAPTOR ----
            ['ADP-12V',     'Adaptor 12 Volt',             'Unit',  74,   'Barang Kembali'],
            ['ADP-12V-1A5', 'Adaptor 12V 1.5A',            'Unit',  166,  'Barang Kembali'],
            ['ADP-5V',      'Adaptor 5 Volt',              'Unit',  30,   'Barang Kembali'],
            ['ADP-9V',      'Adaptor 9 Volt',              'Unit',  15,   'Barang Kembali'],
            ['ADP-24V',     'Adaptor 24 Volt',             'Unit',  8,    'Barang Kembali'],
            // ---- SPLITTER ----
            ['SM-16K',      'Splitter Modullar 1x16 Kuning','Unit', 2,   'Barang Habis Pakai'],
            ['SM-8K',       'Splitter Modullar 1x8 Kuning', 'Unit', 2,   'Barang Habis Pakai'],
            ['SM-2',        'Splitter Modullar 1x2',        'Unit', 11,  'Barang Habis Pakai'],
            ['SM-4',        'Splitter Modullar 1x4',        'Unit', 8,   'Barang Habis Pakai'],
            ['SM-8H',       'Splitter Modullar 1x8 Hitam',  'Unit', 6,   'Barang Habis Pakai'],
            ['SP-16',       'Splitter Passive 1x16',        'Unit', 13,  'Barang Habis Pakai'],
            ['SP-8',        'Splitter Passive 1x8',         'Unit', 6,   'Barang Habis Pakai'],
            ['SP-2',        'Splitter Passive 1x2',         'Unit', 1,   'Barang Habis Pakai'],
            ['SP-4',        'Splitter Passive 1x4',         'Unit', 5,   'Barang Habis Pakai'],
            // ---- RASIO ----
            ['RASIO-10-90', 'Rasio 10-90',                 'Pcs',   4,    'Barang Habis Pakai'],
            ['RASIO-15-85', 'Rasio 15-85',                 'Pcs',   5,    'Barang Habis Pakai'],
            ['RASIO-20-80', 'Rasio 20-80',                 'Pcs',   7,    'Barang Habis Pakai'],
            ['RASIO-25-75', 'Rasio 25-75',                 'Pcs',   3,    'Barang Habis Pakai'],
            ['RASIO-30-70', 'Rasio 30-70',                 'Pcs',   2,    'Barang Habis Pakai'],
            ['RASIO-35-65', 'Rasio 35-65',                 'Pcs',   2,    'Barang Habis Pakai'],
            ['RASIO-40-60', 'Rasio 40-60',                 'Pcs',   4,    'Barang Habis Pakai'],
            ['RASIO-12-88', 'Rasio 12-88',                 'Pcs',   1,    'Barang Habis Pakai'],
            ['RASIO-6-94',  'Rasio 6-94',                  'Pcs',   1,    'Barang Habis Pakai'],
            ['RASIO-9-91',  'Rasio 9-91',                  'Pcs',   1,    'Barang Habis Pakai'],
            // ---- ODP ----
            ['ODP-16',      'ODP 16 Core',                 'Unit',  5,    'Barang Kembali'],
            ['ODP-24',      'ODP 24 Core',                 'Unit',  6,    'Barang Kembali'],
            ['ODP-8',       'ODP 8 Core',                  'Unit',  0,    'Barang Kembali'],
            // ---- JOIN CLOSURE ----
            ['JC-12',       'Join Closure 12C',            'Unit',  6,    'Barang Kembali'],
            ['JC-24',       'Join Closure 24C',            'Unit',  1,    'Barang Kembali'],
            ['JC-48',       'Join Closure 48C',            'Unit',  1,    'Barang Kembali'],
            ['JC-FLAT',     'Join Closure Flat',           'Unit',  2,    'Barang Kembali'],
            // ---- ADSS ----
            ['ADSS-24',     'ADSS 24 Core',                'Meter', 1100, 'Barang Habis Pakai'],
            ['ADSS-6',      'ADSS 6 Core',                 'Meter', 0,    'Barang Habis Pakai'],
            ['ADSS-12',     'ADSS 12 Core',                'Meter', 0,    'Barang Habis Pakai'],
            // ---- HARDWARE JARINGAN ----
            ['SWITCH-HUB',  'Switch Hub',                  'Unit',  8,    'Barang Kembali'],
            ['POE-A',       'POE A',                       'Unit',  15,   'Barang Kembali'],
            ['POE-B',       'POE B',                       'Unit',  14,   'Barang Kembali'],
            ['ANTENA-AC',   'Antena AC',                   'Unit',  1,    'Barang Kembali'],
            ['ANTENA-LHG',  'Antena LHG',                  'Unit',  1,    'Barang Kembali'],
            // ---- AKSESORIS FISIK ----
            ['BAREL',       'Barel',                       'Pcs',   262,  'Barang Habis Pakai'],
            ['BRIKET-A',    'Briket A',                    'Pcs',   25,   'Barang Habis Pakai'],
            ['DEAD-END',    'Dead End',                    'Pcs',   25,   'Barang Habis Pakai'],
            ['SB-BELT',     'Stainlist Belt',              'Pcs',   0,    'Barang Habis Pakai'],
            ['STOP-LINK',   'Stop Link',                   'Pcs',   80,   'Barang Habis Pakai'],
            ['SUSPENSI',    'Suspensi Corong',             'Pcs',   68,   'Barang Habis Pakai'],
            ['TIANG-3FT',   'Tiang 3 Feet',                'Unit',  0,    'Barang Habis Pakai'],
            ['TIANG-4FT',   'Tiang 4 Feet',                'Unit',  0,    'Barang Habis Pakai'],
            ['ROSET',       'Roset',                       'Pcs',   0,    'Barang Habis Pakai'],
            // ---- SFP ----
            ['SFP-40KM',    'SFP 1G 40KM',                'Unit',  4,    'Barang Kembali'],
            ['SFP-DUPLEX',  'SFP Plus Duplex',             'Unit',  30,   'Barang Kembali'],
            ['SFP-QSFP',    'QSFP 40G',                    'Unit',  0,    'Barang Kembali'],
            ['SFP-1G',      'SFP 1G Duplex',               'Unit',  5,    'Barang Kembali'],
            ['SFP-OLT',     'SFP OLT GPON',                'Unit',  2,    'Barang Kembali'],
            ['SFP-BIDI',    'SFP Plus 40KM BIDI',          'Unit',  1,    'Barang Kembali'],
            ['SFP-RJ45',    'SFP RJ45',                    'Unit',  2,    'Barang Kembali'],
            ['SFP-20KM',    'SFP 1G 20KM',                 'Unit',  2,    'Barang Kembali'],
            ['PIG-DUPLEX',  'Pigtail SFP Duplex',          'Unit',  4,    'Barang Kembali'],
            ['PIG-BIDI',    'Pigtail SFP BIDI',            'Unit',  6,    'Barang Kembali'],
            // ---- LAINNYA ----
            ['MIKROTIK',    'Mikrotik',                    'Unit',  0,    'Barang Kembali'],
            ['MKR-CSS',     'Mikrotik CSS ADP',            'Unit',  1,    'Barang Kembali'],
            ['MKR-CRS236',  'Mikrotik CRS236',             'Unit',  0,    'Barang Kembali'],
            ['SW-BROCADE',  'Switch Brocade',              'Unit',  0,    'Barang Kembali'],
            ['SW-NOKIA',    'Switch Nokia 7210',           'Unit',  1,    'Barang Kembali'],
            ['PRINT-LABEL', 'Print Label',                 'Unit',  1,    'Barang Kembali'],
            ['MICROBIT',    'Microbit',                    'Unit',  5,    'Barang Kembali'],
            ['MIMOSA',      'Mimosa',                      'Unit',  0,    'Barang Kembali'],
            ['BRIKET-RAK',  'Briket Rak Universal',        'Unit',  1,    'Barang Kembali'],
            ['OPM',         'OPM',                         'Unit',  0,    'Barang Kembali'],
            ['RJ45-BULK',   'RJ45 Bulk',                   'Pcs',   50,   'Barang Habis Pakai'],
        ];

        $jenisId1 = DB::table('tbl_jenisbarang')->where('jenisbarang_slug', 'barang-kembali')->value('jenisbarang_id') ?? 1;
        $jenisId2 = DB::table('tbl_jenisbarang')->where('jenisbarang_slug', 'barang-habis-pakai')->value('jenisbarang_id') ?? 2;

        foreach ($barangs as $b) {
            [$kode, $nama, $satuan, $stok, $tipe] = $b;
            DB::table('tbl_barang')->updateOrInsert(
                ['barang_kode' => $kode],
                [
                    'jenisbarang_id' => $tipe === 'Barang Kembali' ? $jenisId1 : $jenisId2,
                    'satuan_id'      => $satuan,
                    'merk_id'        => 1,
                    'barang_kode'    => $kode,
                    'barang_nama'    => $nama,
                    'barang_slug'    => Str::slug($nama),
                    'barang_harga'   => 0,
                    'barang_stok'    => $stok,
                    'tipe_barang'    => $tipe,
                    'barang_gambar'  => '',
                    'created_at'     => Carbon::create(2026, 1, 1),
                    'updated_at'     => Carbon::create(2026, 1, 1),
                ]
            );
        }
    }

    // ================================================================
    // BARANG MASUK - Data Juni 2026 dari sheet BARANG MASUK
    // Format: [kode_barang, tanggal, jumlah, keterangan]
    // ================================================================
    private function seedBarangMasuk()
    {
        $masuk = [
            ['MDM-XSF609',  '2026-06-05', 10,  'Pengadaan modem XSF609 batch 1'],
            ['MDM-SIGMA',   '2026-06-05', 5,   'Pengadaan modem Sigma batch 1'],
            ['MDM-F663',    '2026-06-21', 2,   'Pengadaan modem ZTE F663'],
            ['MDM-F663',    '2026-06-28', 1,   'Pengadaan modem ZTE F663 tambahan'],
            ['MDM-F609',    '2026-06-26', 1,   'Pengadaan modem ZTE F609'],
            ['MDM-CLINK',   '2026-06-27', 1,   'Pengadaan modem C-Link'],
            ['MDM-XSF609',  '2026-06-21', 10,  'Pengadaan modem XSF609 batch 2'],
            ['MDM-SIGMA',   '2026-06-23', 10,  'Pengadaan modem Sigma batch 2'],
            ['MDM-F660',    '2026-06-23', 2,   'Pengadaan modem ZTE F660'],
            ['MDM-ZIMLINK', '2026-06-17', 1,   'Pengadaan modem Zimlink batch 1'],
            ['MDM-ZIMLINK', '2026-06-26', 1,   'Pengadaan modem Zimlink batch 2'],
            ['RTR-WR840',   '2026-06-21', 1,   'Pengadaan router TP-Link WR840'],
            ['RTR-WR840',   '2026-06-23', 1,   'Pengadaan router TP-Link WR840 tambahan'],
            ['RTR-TOTOLINK','2026-06-27', 1,   'Pengadaan router Totolink'],
            ['KBL-SSP',     '2026-06-09', 3000,'Pembelian kabel SSP 3000m'],
            ['KBL-LAN',     '2026-06-09', 300, 'Pembelian kabel LAN 300m'],
            ['RJ45-ZIMLINK','2026-06-09', 50,  'Pengadaan RJ45 50pcs'],
            ['RJ45-ZIMLINK','2026-06-23', 100, 'Pengadaan RJ45 100pcs tambahan'],
            ['PROTEKTOR',   '2026-06-23', 5,   'Pengadaan protektor 5pcs'],
            ['PATCHORE',    '2026-06-23', 30,  'Pengadaan patchore 30pcs'],
            ['CVR-A',       '2026-06-05', 1,   'Pengadaan Converter A batch1'],
            ['CVR-A',       '2026-06-21', 1,   'Pengadaan Converter A batch2'],
            ['CVR-B',       '2026-06-09', 1,   'Pengadaan Converter B'],
            ['CVR-2SC',     '2026-06-09', 2,   'Pengadaan Converter 2SC'],
            ['CVR-6SC',     '2026-06-20', 1,   'Pengadaan Converter 6SC batch1'],
            ['CVR-6SC',     '2026-06-23', 2,   'Pengadaan Converter 6SC batch2'],
            ['PAKU-KLEM',   '2026-06-14', 30,  'Pengadaan paku klem 30pcs'],
            ['ADP-12V',     '2026-06-03', 4,   'Pengadaan adaptor 12V batch1'],
            ['ADP-12V',     '2026-06-21', 3,   'Pengadaan adaptor 12V batch2'],
            ['ADP-12V',     '2026-06-23', 4,   'Pengadaan adaptor 12V batch3'],
            ['ADP-12V',     '2026-06-25', 1,   'Pengadaan adaptor 12V batch4'],
            ['ADP-12V',     '2026-06-26', 1,   'Pengadaan adaptor 12V batch5'],
            ['ADP-12V',     '2026-06-27', 2,   'Pengadaan adaptor 12V batch6'],
            ['ADP-12V-1A5', '2026-06-03', 1,   'Pengadaan adaptor 12V 1.5A'],
            ['ADP-12V-1A5', '2026-06-25', 1,   'Pengadaan adaptor 12V 1.5A tambahan'],
            ['ADP-5V',      '2026-06-03', 2,   'Pengadaan adaptor 5V batch1'],
            ['ADP-5V',      '2026-06-21', 2,   'Pengadaan adaptor 5V batch2'],
            ['ADP-5V',      '2026-06-23', 20,  'Pengadaan adaptor 5V batch3'],
            ['ADP-5V',      '2026-06-27', 1,   'Pengadaan adaptor 5V batch4'],
            ['ADP-9V',      '2026-06-21', 1,   'Pengadaan adaptor 9V batch1'],
            ['ADP-9V',      '2026-06-23', 1,   'Pengadaan adaptor 9V batch2'],
            ['ADP-24V',     '2026-06-03', 2,   'Pengadaan adaptor 24V batch1'],
            ['ADP-24V',     '2026-06-21', 2,   'Pengadaan adaptor 24V batch2'],
            ['SWITCH-HUB',  '2026-06-21', 1,   'Pengadaan switch hub'],
            ['POE-B',       '2026-06-21', 2,   'Pengadaan POE B'],
            ['SM-2',        '2026-06-06', 3,   'Pengadaan splitter modullar 1x2'],
            ['SM-2',        '2026-06-23', 1,   'Pengadaan splitter modullar 1x2 tambahan'],
            ['SP-8',        '2026-06-06', 1,   'Pengadaan splitter passive 1x8'],
            ['PRINT-LABEL', '2026-06-17', 6,   'Pengadaan print label 6unit'],
        ];

        foreach ($masuk as $i => $m) {
            $bm_kode = 'BM-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT);
            $barang  = DB::table('tbl_barang')->where('barang_kode', $m[0])->first();
            if (!$barang) continue;
            if (DB::table('tbl_barangmasuk')->where('bm_kode', $bm_kode)->exists()) continue;

            DB::table('tbl_barangmasuk')->insert([
                'bm_kode'          => $bm_kode,
                'barang_kode'      => $m[0],
                'customer_id'      => 1,
                'bm_tanggal'       => $m[1],
                'bm_jumlah'        => $m[2],
                'kode_barang_unik' => null,
                'serial_number'    => null,
                'jam_masuk'        => Carbon::parse($m[1])->setTime(8, 0, 0),
                'created_at'       => Carbon::parse($m[1]),
                'updated_at'       => Carbon::parse($m[1]),
            ]);
            DB::table('tbl_barang')->where('barang_kode', $m[0])->increment('barang_stok', $m[2]);
        }
    }

    // ================================================================
    // BARANG KELUAR - Feb-Jun 2026 dari sheet BARANG KELUAR
    // Format: [kode_barang, tanggal, jumlah, keterangan, teknisi]
    // ================================================================
    private function seedBarangKeluar()
    {
        $keluar = [
            // ===== FEBRUARI 2026 =====
            ['MDM-TJ653',   '2026-02-12', 1,  'Instalasi Padangan mas fiqi Purboyo', 'Fajar'],
            ['MDM-TJ653',   '2026-02-12', 1,  'Instalasi Padangan mas fajar Bintoro', 'Fajar'],
            ['MDM-TJ653',   '2026-02-13', 1,  'Ganti modem sugito sidomulyo', 'Fajar'],
            ['MDM-TJ653',   '2026-02-13', 1,  'Instalasi rangga pamungkas', 'Fajar'],
            ['MDM-TJ653',   '2026-02-13', 1,  'Instalasi hana afidah', 'Wahid'],
            ['MDM-TJ653',   '2026-02-14', 1,  'Instalasi maratus shofiyah', 'Wahid'],
            ['MDM-F670L-B', '2026-02-14', 1,  'Instalasi bahrul fawaid', 'Yoga'],
            ['MDM-F670L-B', '2026-02-16', 1,  'Instalasi wisnu nugroho branggahan', 'Fajar'],
            ['MDM-F670L-B', '2026-02-16', 1,  'Ganti modem mahmudi gembongan', 'Fajar'],
            ['MDM-F670L-B', '2026-02-17', 1,  'PSB mahdan nurul huda karangnongko', 'Fajar'],
            ['MDM-F670L-B', '2026-02-18', 1,  'PSB umi nadziroh gembongan', 'Fajar'],
            ['MDM-F670L-B', '2026-02-18', 1,  'PSB padangan rismanto', 'Wahid'],
            ['MDM-F670L-B', '2026-02-18', 1,  'PSB indra nava slumbung', 'Wahid'],
            ['MDM-F670L-B', '2026-02-20', 1,  'PSB imam suwonso padangan', 'Wahid'],
            ['MDM-F670L-B', '2026-02-21', 1,  'PSB imam kusnin ahmad bakung', 'Wahid'],
            ['MDM-F670L-B', '2026-02-23', 1,  'PSB pasti surya savana', 'Wahid'],
            ['MDM-GGC',     '2026-02-24', 1,  'PSB safaat branggahan', 'Fajar'],
            ['MDM-GGC',     '2026-02-25', 1,  'PSB maftukhul qulub', 'Wahid'],
            ['MDM-GGC',     '2026-02-26', 1,  'PSB dadang kurniawan', 'Fajar'],
            ['MDM-GGC',     '2026-02-28', 1,  'PSB padangan prawito', 'Fajar'],
            ['RTR-WR840',   '2026-02-21', 1,  'Pasang ririn sumbersari', 'Wahid'],
            ['RTR-WR840',   '2026-02-21', 1,  'Router keluar ke pelanggan', 'Wahid'],
            ['CVR-A',       '2026-02-19', 1,  'Penggantian alat nur anisawati B', 'Wahid'],
            // ===== MARET 2026 =====
            ['MDM-GGC',     '2026-03-02', 1,  'PSB surono padangan', 'Wahid'],
            ['MDM-GGC',     '2026-03-02', 1,  'PSB david', 'Fajar'],
            ['MDM-GGC',     '2026-03-03', 1,  'Ganti modem agung noc', 'Fajar'],
            ['MDM-F670L-A', '2026-03-03', 1,  'Ganti modem pasti surya', 'Fajar'],
            ['MDM-GGC',     '2026-03-07', 1,  'Migrasi yusuf', 'Wahid'],
            ['MDM-GGC',     '2026-03-07', 1,  'PSB suyono padangan', 'Fajar'],
            ['MDM-GGC',     '2026-03-10', 1,  'Ripiter gembongan febri', 'Fajar'],
            ['MDM-GGC',     '2026-03-12', 1,  'Penggantian modem nurul hidayah', 'Fajar'],
            ['MDM-GGC',     '2026-03-13', 1,  'PSB reynaldi saputra', 'Fajar'],
            ['MDM-GGC',     '2026-03-13', 1,  'PSB ahmad sugianto', 'Fajar'],
            ['MDM-GGC',     '2026-03-13', 1,  'PSB farrih ahbabana', 'Wahid'],
            ['MDM-F670L-B', '2026-03-23', 1,  'Instalasi fajar teknisi', 'Fajar'],
            ['MDM-ZIMLINK', '2026-03-18', 1,  'Penggantian alat nur kolis', 'Piki'],
            ['MDM-ZIMLINK', '2026-03-19', 1,  'Penggantian ont ujang efendi', 'Wahid'],
            ['MDM-F609',    '2026-03-03', 1,  'PSB gawik wahyudi', 'Fajar'],
            ['MDM-F609',    '2026-03-18', 1,  'PSB padangan sukarno', 'Wahid'],
            ['MDM-F609',    '2026-03-18', 1,  'PSB catur rip utomo', 'Wahid'],
            ['MDM-F609',    '2026-03-18', 1,  'PSB padangan eva lina', 'Wahid'],
            ['MDM-F670L-A', '2026-03-25', 1,  'Penggantian ont arif fauzi', 'Wahid'],
            ['MDM-F670L-A', '2026-03-27', 1,  'Penggantian ont agung noc', 'Wahid'],
            ['RTR-TPLINK',  '2026-03-11', 1,  'Instalasi moh rizal fahmi', 'Fajar'],
            ['CVR-2SC',     '2026-03-12', 1,  'Instalasi masjid MI wahid hasyim', 'Wahid'],
            ['CVR-B',       '2026-03-04', 1,  'Ganti subandono', 'Wahid'],
            ['PATCHORE',    '2026-03-05', 2,  'Patchore keluar instalasi', 'Fajar'],
            // ===== APRIL 2026 =====
            ['MDM-ZIMLINK', '2026-04-01', 1,  'PSB nurikah padangan', 'Fajar'],
            ['MDM-ZIMLINK', '2026-04-07', 1,  'Ganti modem ali mudofar', 'Yoga'],
            ['MDM-ZIMLINK', '2026-04-17', 1,  'Ganti ont agus susilo', 'Wahid'],
            ['MDM-ZIMLINK', '2026-04-20', 1,  'PSB ali mustajib padangan', 'Fajar'],
            ['MDM-ZIMLINK', '2026-04-20', 1,  'PSB edy santoso padangan', 'Fajar'],
            ['MDM-ZIMLINK', '2026-04-21', 1,  'PSB alex wibawanto', 'Fajar'],
            ['MDM-ZIMLINK', '2026-04-23', 1,  'Ganti ont lailatul zahro', 'Wahid'],
            ['MDM-ZIMLINK', '2026-04-25', 1,  'Ganti ont lailatul zahro', 'Wahid'],
            ['MDM-ZIMLINK', '2026-04-28', 1,  'PSB enip farida', 'Wahid'],
            ['MDM-F670L-A', '2026-04-06', 1,  'PSB andreas nova kristanto', 'Fajar'],
            ['MDM-F670L-A', '2026-04-14', 1,  'PSB genta buana chakti', 'Yoga'],
            ['MDM-F670L-A', '2026-04-18', 1,  'PSB ulim marifah', 'Wahid'],
            ['MDM-TJ653',   '2026-04-06', 1,  'Ganti ont kaji badik', 'Fajar'],
            ['MDM-TJ653',   '2026-04-07', 1,  'PSB moh sodik', 'Wahid'],
            ['MDM-TJ653',   '2026-04-08', 1,  'PSB triman padangan', 'Wahid'],
            ['MDM-TJ653',   '2026-04-09', 1,  'PSB moh sjai', 'Fajar'],
            ['MDM-TJ653',   '2026-04-11', 1,  'PSB mukarom padangan', 'Wahid'],
            ['MDM-TJ653',   '2026-04-11', 1,  'PSB sukirnanto padangan', 'Fajar'],
            ['MDM-TJ653',   '2026-04-11', 1,  'PSB pdangan abadi', 'Fajar'],
            ['MDM-TJ653',   '2026-04-13', 1,  'Penggantian alat tutik ayam', 'Fajar'],
            ['MDM-NETVIBES','2026-04-14', 1,  'Ganti alat edy jatmiko', 'Fajar'],
            ['MDM-NETVIBES','2026-04-14', 1,  'PSB abd fatkhur rohman', 'Wahid'],
            ['RTR-TPLINK',  '2026-04-04', 1,  'Migrasi SDN 2 Bakung', 'Wahid'],
            ['CVR-A',       '2026-04-20', 1,  'Penggantian alat converter A', 'Wahid'],
            ['KBL-SSP',     '2026-04-15', 250,'Penarikan kabel SSP wilayah Bakung', 'Wahid'],
            ['PATCHORE',    '2026-04-10', 1,  'Patchore keluar', 'Wahid'],
            ['ADP-9V',      '2026-04-04', 1,  'Adaptor 9V keluar', 'Wahid'],
            // ===== MEI 2026 =====
            ['MDM-ZIMLINK', '2026-05-08', 1,  'PSB kawit padangan', 'Fajar'],
            ['MDM-ZIMLINK', '2026-05-11', 1,  'PSB dwi rahayu', 'Yoga'],
            ['MDM-ZIMLINK', '2026-05-11', 1,  'PSB noortypan juni eldiant', 'Wahid'],
            ['MDM-ZIMLINK', '2026-05-13', 1,  'Ganti ont ibnu rofik', 'Fajar'],
            ['MDM-ZIMLINK', '2026-05-16', 1,  'Ganti ont m ngaming thohari', 'Fajar'],
            ['MDM-ZIMLINK', '2026-05-16', 1,  'PSB ahmad samsodin bakung', 'Wahid'],
            ['MDM-ZIMLINK', '2026-05-18', 1,  'PSB mujiono', 'Wahid'],
            ['MDM-ZIMLINK', '2026-05-18', 1,  'Ganti ont gianah', 'Fajar'],
            ['MDM-ZIMLINK', '2026-05-19', 1,  'PSB diana suryadi', 'Fajar'],
            ['MDM-ZIMLINK', '2026-05-19', 1,  'Kirim Nganjuk', 'Admin'],
            ['MDM-F670L-A', '2026-05-20', 1,  'PSB siti kholifah fajar', 'Fajar'],
            ['MDM-F670L-A', '2026-05-20', 1,  'Ganti ont bambang triono wonorejo', 'Fajar'],
            ['MDM-F609',    '2026-05-12', 1,  'Dismantle mirna yulia eko rahayu', 'Wahid'],
            ['MDM-XSF609',  '2026-05-24', 1,  'Ganti ont binah', 'Fajar'],
            ['MDM-XSF609',  '2026-05-25', 1,  'PSB najwa nur adibah putri', 'Fajar'],
            ['MDM-XSF609',  '2026-05-26', 1,  'PSB rohmah', 'Wahid'],
            ['MDM-XSF609',  '2026-05-28', 1,  'PSB m fauzi', 'Wahid'],
            ['MDM-XSF609',  '2026-05-28', 1,  'PSB arri susanto', 'Fajar'],
            ['MDM-XSF609',  '2026-05-28', 1,  'PSB juwarti', 'Wahid'],
            ['MDM-TJ680X',  '2026-05-05', 1,  'Ganti ont candra pradana', 'Fajar'],
            ['MDM-TJ680X',  '2026-05-08', 1,  'Ganti ont didit prima rahmansyah', 'Fajar'],
            ['KBL-SSP',     '2026-05-10', 250,'Penarikan kabel SSP wilayah Ngraho', 'Wahid'],
            ['KBL-SSP',     '2026-05-15', 50, 'Penarikan kabel SSP', 'Fajar'],
            ['PATCHORE',    '2026-05-06', 3,  'Patchore keluar', 'Fajar'],
            ['CVR-2SC',     '2026-05-20', 1,  'Dismantle converter 2SC', 'Wahid'],
            ['ODP-16',      '2026-05-22', 1,  'ODP 16 Core dikeluarkan', 'Wahid'],
            ['KBL-SSP',     '2026-05-25', 400,'Penarikan kabel SSP backbone Wonotirto', 'Fajar'],
            ['ADP-12V',     '2026-05-16', 1,  'Adaptor 12V keluar', 'Wahid'],
            // ===== JUNI 2026 =====
            ['MDM-ZIMLINK', '2026-06-13', 1,  'Ganti ont DIYA BERO', 'Fajar'],
            ['MDM-ZIMLINK', '2026-06-16', 1,  'Ganti ont nurul hidayah', 'Fajar'],
            ['MDM-ZIMLINK', '2026-06-25', 1,  'Dismantle imam bukori', 'Wahid'],
            ['MDM-ZIMLINK', '2026-06-28', 1,  'Ganti ont ahmad iwan fauzi', 'Wahid'],
            ['MDM-F670L-A', '2026-06-09', 1,  'Ganti ont MTS fathul huda', 'Wahid'],
            ['MDM-XSF609',  '2026-06-01', 1,  'PSB ira wahyu ningtyas', 'Fajar'],
            ['MDM-XSF609',  '2026-06-01', 1,  'PSB rozikin', 'Wahid'],
            ['MDM-XSF609',  '2026-06-02', 1,  'PSB nurun nida indamala', 'Wahid'],
            ['MDM-XSF609',  '2026-06-05', 1,  'PSB erwan samsul hadi', 'Fajar'],
            ['MDM-XSF609',  '2026-06-11', 1,  'PSB m imron', 'Wahid'],
            ['MDM-XSF609',  '2026-06-12', 1,  'Ganti ont asrofindika', 'Yoga'],
            ['MDM-XSF609',  '2026-06-12', 1,  'PSB chila audia', 'Wahid'],
            ['MDM-XSF609',  '2026-06-13', 1,  'PSB fitra yuni setiawan', 'Wahid'],
            ['MDM-XSF609',  '2026-06-16', 1,  'Ganti ont nurul hidayah', 'Fajar'],
            ['MDM-XSF609',  '2026-06-18', 1,  'Ganti ont asrofindika', 'Yoga'],
            ['MDM-XSF609',  '2026-06-20', 1,  'Ganti modem nova hariyono', 'Yoga'],
            ['MDM-SIGMA',   '2026-06-09', 1,  'Ganti ont sigma 1', 'Fajar'],
            ['MDM-SIGMA',   '2026-06-12', 1,  'Ganti ont sigma 2', 'Wahid'],
            ['MDM-SIGMA',   '2026-06-16', 1,  'Ganti ont sigma 3', 'Fajar'],
            ['MDM-SIGMA',   '2026-06-28', 1,  'Dismantle sigma', 'Wahid'],
            ['MDM-F660',    '2026-06-22', 1,  'Dismantle ahmad kusairi', 'Wahid'],
            ['MDM-F660',    '2026-06-22', 1,  'Dismantle faridatul', 'Wahid'],
            ['MDM-TJ680X',  '2026-06-22', 1,  'Ganti ont habib hanafi', 'Yoga'],
            ['MDM-F609',    '2026-06-25', 1,  'Dismantle andria septa', 'Wahid'],
            ['RTR-TPLINK',  '2026-06-04', 1,  'PSB siti zulaikah', 'Fajar'],
            ['RTR-WR840',   '2026-06-01', 2,  'Router TP-Link keluar ke pelanggan', 'Wahid'],
            ['RTR-WR840',   '2026-06-04', 1,  'Router TP-Link keluar ke pelanggan', 'Fajar'],
            ['RTR-WR840',   '2026-06-21', 1,  'Router TP-Link keluar ke pelanggan', 'Yoga'],
            ['KBL-SSP',     '2026-06-01', 200,'Penarikan kabel SSP wilayah Padangan', 'Fajar'],
            ['KBL-SSP',     '2026-06-02', 100,'Penarikan kabel SSP wilayah Branggahan', 'Fajar'],
            ['KBL-SSP',     '2026-06-05', 250,'Penarikan kabel SSP wilayah Bakung', 'Wahid'],
            ['KBL-SSP',     '2026-06-06', 250,'Penarikan kabel SSP wilayah Ngraho', 'Wahid'],
            ['KBL-SSP',     '2026-06-08', 50, 'Penarikan kabel SSP', 'Fajar'],
            ['KBL-SSP',     '2026-06-09', 400,'Penarikan kabel SSP backbone Wonotirto', 'Fajar'],
            ['KBL-SSP',     '2026-06-11', 100,'Penarikan kabel SSP wilayah Kelud', 'Fajar'],
            ['KBL-SSP',     '2026-06-12', 400,'Penarikan kabel SSP wilayah Ngasem', 'Wahid'],
            ['KBL-SSP',     '2026-06-13', 60, 'Penarikan kabel SSP', 'Wahid'],
            ['KBL-SSP',     '2026-06-15', 75, 'Penarikan kabel SSP', 'Fajar'],
            ['KBL-SSP',     '2026-06-17', 325,'Penarikan kabel SSP wilayah Kalitidu', 'Admin'],
            ['KBL-SSP',     '2026-06-19', 50, 'Penarikan kabel SSP', 'Wahid'],
            ['KBL-SSP',     '2026-06-20', 100,'Penarikan kabel SSP', 'Fajar'],
            ['KBL-SSP',     '2026-06-21', 100,'Penarikan kabel SSP', 'Fajar'],
            ['KBL-SSP',     '2026-06-22', 50, 'Penarikan kabel SSP', 'Wahid'],
            ['KBL-SSP',     '2026-06-26', 150,'Penarikan kabel SSP', 'Fajar'],
            ['KBL-SSP',     '2026-06-27', 200,'Penarikan kabel SSP', 'Wahid'],
            ['KBL-LAN',     '2026-06-01', 20, 'Penarikan kabel LAN', 'Fajar'],
            ['KBL-LAN',     '2026-06-15', 15, 'Penarikan kabel LAN', 'Wahid'],
            ['RJ45-ZIMLINK','2026-06-01', 4,  'RJ45 keluar instalasi', 'Wahid'],
            ['RJ45-ZIMLINK','2026-06-19', 50, 'RJ45 keluar instalasi massal', 'Fajar'],
            ['PATCHORE',    '2026-06-01', 2,  'Patchore keluar instalasi', 'Fajar'],
            ['PATCHORE',    '2026-06-04', 1,  'Patchore keluar', 'Wahid'],
            ['PATCHORE',    '2026-06-08', 3,  'Patchore keluar', 'Fajar'],
            ['PATCHORE',    '2026-06-09', 1,  'Patchore keluar instalasi', 'Wahid'],
            ['PATCHORE',    '2026-06-12', 2,  'Patchore keluar', 'Fajar'],
            ['PATCHORE',    '2026-06-14', 2,  'Patchore keluar', 'Wahid'],
            ['PATCHORE',    '2026-06-22', 1,  'Patchore keluar', 'Wahid'],
            ['PATCHORE',    '2026-06-25', 1,  'Patchore keluar', 'Fajar'],
            ['PATCHORE',    '2026-06-26', 2,  'Patchore keluar', 'Fajar'],
            ['PATCHORE',    '2026-06-27', 1,  'Patchore keluar', 'Wahid'],
            ['PATCHORE',    '2026-06-28', 1,  'Patchore keluar', 'Wahid'],
            ['SOLASI',      '2026-06-08', 1,  'Solasi keluar', 'Wahid'],
            ['SOLASI',      '2026-06-20', 1,  'Solasi keluar', 'Fajar'],
            ['SOLASI',      '2026-06-21', 2,  'Solasi keluar', 'Fajar'],
            ['SOLASI',      '2026-06-22', 1,  'Solasi keluar', 'Wahid'],
            ['SOLASI',      '2026-06-27', 1,  'Solasi keluar', 'Wahid'],
            ['PAKU-KLEM',   '2026-06-01', 8,  'Paku klem keluar', 'Fajar'],
            ['PAKU-KLEM',   '2026-06-02', 2,  'Paku klem keluar', 'Wahid'],
            ['PAKU-KLEM',   '2026-06-04', 2,  'Paku klem keluar', 'Wahid'],
            ['PAKU-KLEM',   '2026-06-05', 1,  'Paku klem keluar', 'Fajar'],
            ['PAKU-KLEM',   '2026-06-08', 4,  'Paku klem keluar', 'Wahid'],
            ['PAKU-KLEM',   '2026-06-09', 2,  'Paku klem keluar', 'Fajar'],
            ['PAKU-KLEM',   '2026-06-11', 2,  'Paku klem keluar', 'Wahid'],
            ['PAKU-KLEM',   '2026-06-12', 3,  'Paku klem keluar', 'Fajar'],
            ['PAKU-KLEM',   '2026-06-15', 3,  'Paku klem keluar', 'Wahid'],
            ['PAKU-KLEM',   '2026-06-19', 2,  'Paku klem keluar', 'Wahid'],
            ['PAKU-KLEM',   '2026-06-21', 2,  'Paku klem keluar', 'Fajar'],
            ['PAKU-KLEM',   '2026-06-26', 2,  'Paku klem keluar', 'Wahid'],
            ['PAKU-KLEM',   '2026-06-27', 2,  'Paku klem keluar', 'Fajar'],
            ['PAKU-KLEM',   '2026-06-28', 2,  'Paku klem keluar', 'Wahid'],
            ['ADP-5V',      '2026-06-02', 3,  'Adaptor 5V keluar', 'Wahid'],
            ['ADP-5V',      '2026-06-04', 3,  'Adaptor 5V keluar', 'Fajar'],
            ['ADP-5V',      '2026-06-08', 1,  'Adaptor 5V keluar', 'Wahid'],
            ['ADP-5V',      '2026-06-11', 1,  'Adaptor 5V keluar', 'Wahid'],
            ['ADP-5V',      '2026-06-12', 1,  'Adaptor 5V keluar', 'Fajar'],
            ['ADP-5V',      '2026-06-19', 11, 'Adaptor 5V keluar massal', 'Fajar'],
            ['ADP-5V',      '2026-06-21', 1,  'Adaptor 5V keluar', 'Fajar'],
            ['ADP-5V',      '2026-06-23', 1,  'Adaptor 5V keluar', 'Wahid'],
            ['ADP-12V',     '2026-06-16', 1,  'Adaptor 12V keluar', 'Wahid'],
            ['ADP-12V',     '2026-06-19', 10, 'Adaptor 12V keluar massal', 'Admin'],
            ['CVR-3SC',     '2026-06-21', 1,  'Converter 3SC keluar', 'Wahid'],
            ['CVR-6SC',     '2026-06-09', 1,  'Converter 6SC keluar', 'Wahid'],
            ['CVR-6SC',     '2026-06-19', 2,  'Converter 6SC keluar', 'Fajar'],
            ['BAREL',       '2026-06-12', 16, 'Barel keluar', 'Admin'],
            ['JC-12',       '2026-06-01', 1,  'Join Closure 12C dipakai backbone', 'Wahid'],
            ['JC-12',       '2026-06-11', 1,  'Join Closure 12C dipakai', 'Fajar'],
            ['PROTEKTOR',   '2026-06-09', 1,  'Protektor keluar', 'Wahid'],
            ['PROTEKTOR',   '2026-06-29', 1,  'Protektor keluar', 'Fajar'],
            ['CVR-A',       '2026-06-21', 1,  'Converter A keluar', 'Fajar'],
            ['CVR-2SC',     '2026-06-09', 1,  'Dismantle converter 2SC', 'Wahid'],
            ['CVR-2SC',     '2026-06-15', 1,  'Converter 2SC keluar', 'Wahid'],
            ['CVR-B',       '2026-06-05', 1,  'Converter B keluar', 'Wahid'],
            ['ADP-9V',      '2026-06-01', 2,  'Adaptor 9V keluar', 'Wahid'],
            ['ADP-9V',      '2026-06-21', 1,  'Adaptor 9V keluar', 'Fajar'],
            ['POE-B',       '2026-06-15', 1,  'POE B keluar', 'Wahid'],
            ['RASIO-20-80', '2026-06-13', 1,  'Rasio 20-80 keluar', 'Wahid'],
            ['RASIO-30-70', '2026-06-13', 1,  'Rasio 30-70 keluar', 'Wahid'],
            ['MDM-F663',    '2026-06-20', 1,  'Dismantle yoyok ptanoto', 'Fajar'],
            ['MDM-F663',    '2026-06-20', 1,  'Dismantle erik', 'Fajar'],
            ['MDM-F663',    '2026-06-27', 1,  'Migrasi karisun', 'Yoga'],
            ['ODP-16',      '2026-06-12', 1,  'ODP 16 Core dikeluarkan', 'Wahid'],
        ];

        foreach ($keluar as $i => $k) {
            $bk_kode = 'BK-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT);
            $barang  = DB::table('tbl_barang')->where('barang_kode', $k[0])->first();
            if (!$barang) continue;
            if (DB::table('tbl_barangkeluar')->where('bk_kode', $bk_kode)->exists()) continue;

            $tujuan  = $k[3] ?? '-';
            $teknisi = $k[4] ?? 'Admin';

            DB::table('tbl_barangkeluar')->insert([
                'bk_kode'      => $bk_kode,
                'barang_kode'  => $k[0],
                'bk_tanggal'   => $k[1],
                'bk_tujuan'    => $tujuan,
                'bk_jumlah'    => $k[2],
                'serial_number'=> null,
                'teknisi'      => $teknisi,
                'teknisi_nama' => $teknisi,
                'keterangan'   => $tujuan,
                'jam_keluar'   => Carbon::parse($k[1])->setTime(9, 0, 0),
                'created_at'   => Carbon::parse($k[1]),
                'updated_at'   => Carbon::parse($k[1]),
            ]);
            DB::table('tbl_barang')->where('barang_kode', $k[0])->decrement('barang_stok', $k[2]);
        }
    }
}
