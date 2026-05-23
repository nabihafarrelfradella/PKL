<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['M', 'F'])->nullable()->after('user_nmlengkap');
            $table->date('tanggal_lahir')->nullable()->after('jenis_kelamin');
            $table->string('teknisi_sn')->nullable()->after('tanggal_lahir');
        });
    }

    public function down()
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'tanggal_lahir', 'teknisi_sn']);
        });
    }
};
