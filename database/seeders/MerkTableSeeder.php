<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MerkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_merk')->truncate();
        DB::table('tbl_merk')->insert([
            [
                'merk_nama'       => 'Krisbow',
                'merk_slug'       => 'krisbow',
                'merk_keterangan' => 'Alat & Perkakas',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now(),
            ],
        ]);
    }
}
