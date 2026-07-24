<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tbl_barangkeluar DROP INDEX tbl_barangkeluar_bk_kode_unique");
        } catch (\Exception $e) {
            // Index might not exist, ignore
        }

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tbl_barangmasuk DROP INDEX tbl_barangmasuk_bm_kode_unique");
        } catch (\Exception $e) {
            // Index might not exist, ignore
        }
    }

    public function down()
    {
        // 
    }
};
