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
        // 1. Add customer_kode column to tbl_customer
        if (!Schema::hasColumn('tbl_customer', 'customer_kode')) {
            Schema::table('tbl_customer', function (Blueprint $table) {
                $table->string('customer_kode')->unique()->nullable()->after('customer_id');
            });
        }

        // 2. Generate customer_kode for existing customers
        $customers = DB::table('tbl_customer')->orderBy('customer_id', 'asc')->get();
        $counter = 1;
        foreach ($customers as $cust) {
            $code = 'CUST-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
            // Ensure uniqueness
            while (DB::table('tbl_customer')->where('customer_kode', $code)->where('customer_id', '!=', $cust->customer_id)->exists()) {
                $counter++;
                $code = 'CUST-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
            }
            DB::table('tbl_customer')->where('customer_id', $cust->customer_id)->update(['customer_kode' => $code]);
            $counter++;
        }

        // 3. Update existing technicians' teknisi_sn to old format: [gender]-[dd]-[yyyy]
        $technicians = DB::table('tbl_user')->where('role_id', 3)->orderBy('user_id', 'asc')->get();
        foreach ($technicians as $tech) {
            $gender = $tech->jenis_kelamin ?? 'M';
            $dobDate = $tech->tanggal_lahir ? \Carbon\Carbon::parse($tech->tanggal_lahir) : now();
            $newSn = $gender . '-' . $dobDate->format('d-Y');

            // Update user record
            DB::table('tbl_user')->where('user_id', $tech->user_id)->update(['teknisi_sn' => $newSn]);

            // Update associated records in tbl_barangkeluar if there are any that match the old SN
            if ($tech->teknisi_sn) {
                DB::table('tbl_barangkeluar')
                    ->where('teknisi', $tech->teknisi_sn)
                    ->update(['teknisi' => $newSn]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('tbl_customer', 'customer_kode')) {
            Schema::table('tbl_customer', function (Blueprint $table) {
                $table->dropColumn('customer_kode');
            });
        }
    }
};
