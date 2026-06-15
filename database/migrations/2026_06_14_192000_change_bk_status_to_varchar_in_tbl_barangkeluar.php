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
        DB::statement("ALTER TABLE tbl_barangkeluar MODIFY COLUMN bk_status VARCHAR(50) NOT NULL DEFAULT 'Dipinjam'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE tbl_barangkeluar MODIFY COLUMN bk_status ENUM('Dipinjam', 'Selesai') NOT NULL DEFAULT 'Dipinjam'");
    }
};
