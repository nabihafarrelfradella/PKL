<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds user_phone field to tbl_user for Pegawai Teknisi phone numbers.
     */
    public function up()
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_user', 'user_phone')) {
                $table->string('user_phone', 20)->nullable()->after('user_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_user', 'user_phone')) {
                $table->dropColumn('user_phone');
            }
        });
    }
};
