<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        $submenuBarangMasuk = DB::table('tbl_submenu')->where('submenu_redirect', '=', '/barang-masuk')->first();
        $submenuBarangKeluar = DB::table('tbl_submenu')->where('submenu_redirect', '=', '/barang-keluar')->first();

        if ($submenuBarangMasuk) {
            $aksesTypes = ['view', 'create', 'update', 'delete'];
            foreach ($aksesTypes as $type) {
                $exists = DB::table('tbl_akses')
                    ->where('submenu_id', $submenuBarangMasuk->submenu_id)
                    ->where('role_id', 3)
                    ->where('akses_type', $type)
                    ->exists();
                
                if (!$exists) {
                    DB::table('tbl_akses')->insert([
                        'submenu_id' => $submenuBarangMasuk->submenu_id,
                        'role_id' => 3,
                        'akses_type' => $type,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        if ($submenuBarangKeluar) {
            $aksesTypes = ['view', 'create', 'update', 'delete'];
            foreach ($aksesTypes as $type) {
                $exists = DB::table('tbl_akses')
                    ->where('submenu_id', $submenuBarangKeluar->submenu_id)
                    ->where('role_id', 3)
                    ->where('akses_type', $type)
                    ->exists();
                
                if (!$exists) {
                    DB::table('tbl_akses')->insert([
                        'submenu_id' => $submenuBarangKeluar->submenu_id,
                        'role_id' => 3,
                        'akses_type' => $type,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    public function down()
    {
        DB::table('tbl_akses')->where('role_id', 3)->whereIn('submenu_id', function($query) {
            $query->select('submenu_id')->from('tbl_submenu')
                ->whereIn('submenu_redirect', ['/barang-masuk', '/barang-keluar']);
        })->delete();
    }
};