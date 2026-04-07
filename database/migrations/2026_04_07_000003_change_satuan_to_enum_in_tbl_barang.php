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
        // Set all existing values to 'Qty' first to avoid truncation errors
        DB::table('tbl_barang')->update(['satuan_id' => 'Qty']);
        
        // Update tbl_barang: change satuan_id to ENUM using raw SQL
        DB::statement("ALTER TABLE tbl_barang MODIFY COLUMN satuan_id ENUM('Meter', 'Qty', 'Pcs', 'Kg') DEFAULT 'Qty'");

        // Remove "Satuan" from tbl_submenu if exists
        DB::table('tbl_submenu')->where('submenu_judul', 'Satuan')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE tbl_barang MODIFY COLUMN satuan_id VARCHAR(255) NULL");
    }
};
