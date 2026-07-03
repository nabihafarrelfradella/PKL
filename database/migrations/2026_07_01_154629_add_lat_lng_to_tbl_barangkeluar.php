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
            $table->string('bk_map_url')->nullable()->after('bk_lokasi');
            $table->string('bk_lat')->nullable()->after('bk_map_url');
            $table->string('bk_lng')->nullable()->after('bk_lat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_barangkeluar', function (Blueprint $table) {
            $table->dropColumn(['bk_map_url', 'bk_lat', 'bk_lng']);
        });
    }
};
