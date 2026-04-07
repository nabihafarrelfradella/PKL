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
        // 1. Find the "User" menu
        $userMenu = DB::table('tbl_menu')->where('menu_judul', 'User')->first();

        if ($userMenu) {
            // 2. Find all roles that have access to its submenus
            $submenuIds = DB::table('tbl_submenu')->where('menu_id', $userMenu->menu_id)->pluck('submenu_id');

            $rolesWithAccess = DB::table('tbl_akses')
                ->whereIn('submenu_id', $submenuIds)
                ->pluck('role_id')
                ->unique();

            foreach ($rolesWithAccess as $roleId) {
                // Check if already has access to menu_id
                $exists = DB::table('tbl_akses')
                    ->where('role_id', $roleId)
                    ->where('menu_id', $userMenu->menu_id)
                    ->where('akses_type', 'view')
                    ->exists();

                if (!$exists) {
                    DB::table('tbl_akses')->insert([
                        'role_id' => $roleId,
                        'menu_id' => $userMenu->menu_id,
                        'akses_type' => 'view',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
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
        // Remove permissions added in up()
        $userMenu = DB::table('tbl_menu')->where('menu_judul', 'User')->first();
        if ($userMenu) {
            DB::table('tbl_akses')->where('menu_id', $userMenu->menu_id)->delete();
        }
    }
};
