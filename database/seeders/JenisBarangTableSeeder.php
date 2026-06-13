<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JenisBarangTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_jenisbarang')->truncate();
        DB::table('tbl_jenisbarang')->insert([
            [
                'jenisbarang_nama'         => 'Barang Kembali',
                'jenisbarang_slug'         => 'barang-kembali',
                'jenisbarang_keterangan'   => 'Barang Kembali',
                'created_at'               => Carbon::now(),
                'updated_at'               => Carbon::now(),
            ],
            [
                'jenisbarang_nama'         => 'Barang Habis Pakai',
                'jenisbarang_slug'         => 'barang-habis-pakai',
                'jenisbarang_keterangan'   => 'Barang Habis Pakai',
                'created_at'               => Carbon::now(),
                'updated_at'               => Carbon::now(),
            ],
        ]);
    }
}
