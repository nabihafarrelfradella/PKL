<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Backfill teknisi_nama dari tbl_user berdasarkan teknisi (teknisi_sn)
        try {
            DB::table('tbl_barangkeluar')
                ->join('tbl_user', 'tbl_barangkeluar.teknisi', '=', 'tbl_user.teknisi_sn')
                ->whereNull('tbl_barangkeluar.teknisi_nama')
                ->update(['tbl_barangkeluar.teknisi_nama' => DB::raw('tbl_user.user_nmlengkap')]);
        } catch (\Exception $e) {
            // Abaikan jika terjadi galat saat migrasi agar tidak membatalkan seluruh proses
        }

        // 2. Backfill kode_barang_unik dari tbl_barangmasuk berdasarkan serial_number dan barang_kode
        try {
            DB::table('tbl_barangkeluar')
                ->join('tbl_barangmasuk', function($join) {
                    $join->on('tbl_barangmasuk.serial_number', '=', 'tbl_barangkeluar.serial_number')
                         ->on('tbl_barangmasuk.barang_kode', '=', 'tbl_barangkeluar.barang_kode');
                })
                ->whereNotNull('tbl_barangkeluar.serial_number')
                ->where('tbl_barangkeluar.serial_number', '!=', '-')
                ->where('tbl_barangkeluar.serial_number', '!=', '')
                ->whereNull('tbl_barangkeluar.kode_barang_unik')
                ->update(['tbl_barangkeluar.kode_barang_unik' => DB::raw('tbl_barangmasuk.kode_barang_unik')]);
        } catch (\Exception $e) {
            // Abaikan jika terjadi galat
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tidak ada rollback yang merusak data yang sudah di-backfill
    }
};
