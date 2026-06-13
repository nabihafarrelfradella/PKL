<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_barangkeluar', function (Blueprint $table) {
            $table->string('teknisi_nama')->nullable()->after('teknisi');
        });

        // Pre-populate existing records with current technician names if they still exist in tbl_user
        try {
            \Illuminate\Support\Facades\DB::table('tbl_barangkeluar')
                ->join('tbl_user', 'tbl_barangkeluar.teknisi', '=', 'tbl_user.teknisi_sn')
                ->update(['tbl_barangkeluar.teknisi_nama' => \Illuminate\Support\Facades\DB::raw('tbl_user.user_nmlengkap')]);
        } catch (\Exception $e) {
            // Log or ignore if table/columns don't match, to prevent migration crash
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_barangkeluar', function (Blueprint $table) {
            $table->dropColumn('teknisi_nama');
        });
    }
};
