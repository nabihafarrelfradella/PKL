<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $timestamp = Carbon::now()->timestamp;

        // 1. Create new Menu "User"
        // Find the sort position after Master Barang
        $masterBarang = DB::table('tbl_menu')->where('menu_judul', 'Master Barang')->first();
        $newSort = $masterBarang ? $masterBarang->menu_sort + 1 : 2;

        // Shift existing menus after new position
        DB::table('tbl_menu')->where('menu_sort', '>=', $newSort)->increment('menu_sort');

        DB::table('tbl_menu')->insert([
            'menu_id' => $timestamp,
            'menu_judul' => 'User',
            'menu_slug' => 'user',
            'menu_icon' => 'users', // Icon users
            'menu_redirect' => '-',
            'menu_sort' => $newSort,
            'menu_type' => '2', // Submenu type
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Add Submenus to "User"
        $submenus = [
            ['judul' => 'Role', 'redirect' => '/role'],
            ['judul' => 'List', 'redirect' => '/user'],
            ['judul' => 'Akses', 'redirect' => '/akses/role'],
        ];

        foreach ($submenus as $index => $sub) {
            DB::table('tbl_submenu')->insert([
                'menu_id' => $timestamp,
                'submenu_judul' => $sub['judul'],
                'submenu_slug' => strtolower($sub['judul']),
                'submenu_redirect' => $sub['redirect'],
                'submenu_sort' => $index + 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 3. Migrate existing permissions from othermenu to new submenus
        // othermenu_id 3 -> Role, 4 -> User (List), 5 -> Akses
        $mappings = [
            3 => 'Role',
            4 => 'List',
            5 => 'Akses'
        ];

        foreach ($mappings as $oldId => $newJudul) {
            $newSub = DB::table('tbl_submenu')->where('submenu_judul', $newJudul)->where('menu_id', $timestamp)->first();
            if ($newSub) {
                $permissions = DB::table('tbl_akses')->where('othermenu_id', $oldId)->get();
                foreach ($permissions as $p) {
                    DB::table('tbl_akses')->insert([
                        'role_id' => $p->role_id,
                        'submenu_id' => $newSub->submenu_id,
                        'akses_type' => $p->akses_type,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        // 4. Remove old othermenu entries for Role, List, Akses
        DB::table('tbl_akses')->whereIn('othermenu_id', [3, 4, 5])->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is a complex manual migration, down would involve reversing the above steps
        // For safety in this task, we focus on the UP.
    }
};
