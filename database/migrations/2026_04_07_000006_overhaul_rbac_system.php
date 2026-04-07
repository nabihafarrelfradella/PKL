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
        // 1. Reset Roles
        DB::table('tbl_role')->truncate();
        DB::table('tbl_role')->insert([
            ['role_id' => 1, 'role_title' => 'Admin', 'role_slug' => 'admin', 'role_desc' => 'Full System Access', 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'role_title' => 'Staff Gudang', 'role_slug' => 'staff-gudang', 'role_desc' => 'Inventory Management', 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'role_title' => 'Pegawai', 'role_slug' => 'pegawai', 'role_desc' => 'Read-only Access to Items', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Create Audit Log Table
        Schema::create('tbl_audit_log', function (Blueprint $table) {
            $table->id('audit_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role_slug');
            $table->string('activity');
            $table->string('module');
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // 3. Setup Initial Permissions (tbl_akses)
        DB::table('tbl_akses')->truncate();
        
        // Admin: Full Access to all Menus (Dashboard, Barang, User, Laporan, Transaksi, Settings)
        // Staff Gudang: Access to Dashboard, Barang, Laporan, Transaksi. NO User Access.
        // Pegawai: Only View Barang.

        // Get all menus
        $menus = DB::table('tbl_menu')->get();
        $submenus = DB::table('tbl_submenu')->get();

        foreach ($menus as $m) {
            // Admin gets everything
            DB::table('tbl_akses')->insert(['menu_id' => $m->menu_id, 'role_id' => 1, 'akses_type' => 'view', 'created_at' => now()]);
            DB::table('tbl_akses')->insert(['menu_id' => $m->menu_id, 'role_id' => 1, 'akses_type' => 'create', 'created_at' => now()]);
            DB::table('tbl_akses')->insert(['menu_id' => $m->menu_id, 'role_id' => 1, 'akses_type' => 'update', 'created_at' => now()]);
            DB::table('tbl_akses')->insert(['menu_id' => $m->menu_id, 'role_id' => 1, 'akses_type' => 'delete', 'created_at' => now()]);

            // Staff Gudang: NO User (module name might vary, let's check titles)
            if ($m->menu_judul != 'User' && $m->menu_judul != 'Settings') {
                DB::table('tbl_akses')->insert(['menu_id' => $m->menu_id, 'role_id' => 2, 'akses_type' => 'view', 'created_at' => now()]);
                DB::table('tbl_akses')->insert(['menu_id' => $m->menu_id, 'role_id' => 2, 'akses_type' => 'create', 'created_at' => now()]);
                DB::table('tbl_akses')->insert(['menu_id' => $m->menu_id, 'role_id' => 2, 'akses_type' => 'update', 'created_at' => now()]);
                DB::table('tbl_akses')->insert(['menu_id' => $m->menu_id, 'role_id' => 2, 'akses_type' => 'delete', 'created_at' => now()]);
            }
        }

        foreach ($submenus as $s) {
            // Admin gets everything
            DB::table('tbl_akses')->insert(['submenu_id' => $s->submenu_id, 'role_id' => 1, 'akses_type' => 'view', 'created_at' => now()]);
            DB::table('tbl_akses')->insert(['submenu_id' => $s->submenu_id, 'role_id' => 1, 'akses_type' => 'create', 'created_at' => now()]);
            DB::table('tbl_akses')->insert(['submenu_id' => $s->submenu_id, 'role_id' => 1, 'akses_type' => 'update', 'created_at' => now()]);
            DB::table('tbl_akses')->insert(['submenu_id' => $s->submenu_id, 'role_id' => 1, 'akses_type' => 'delete', 'created_at' => now()]);

            // Staff Gudang: Only if not User management
            $parentMenu = DB::table('tbl_menu')->where('menu_id', $s->menu_id)->first();
            if ($parentMenu && $parentMenu->menu_judul != 'User') {
                DB::table('tbl_akses')->insert(['submenu_id' => $s->submenu_id, 'role_id' => 2, 'akses_type' => 'view', 'created_at' => now()]);
                DB::table('tbl_akses')->insert(['submenu_id' => $s->submenu_id, 'role_id' => 2, 'akses_type' => 'create', 'created_at' => now()]);
                DB::table('tbl_akses')->insert(['submenu_id' => $s->submenu_id, 'role_id' => 2, 'akses_type' => 'update', 'created_at' => now()]);
                DB::table('tbl_akses')->insert(['submenu_id' => $s->submenu_id, 'role_id' => 2, 'akses_type' => 'delete', 'created_at' => now()]);
            }

            // Pegawai: Only View for 'Barang' related submenus
            if ($s->submenu_judul == 'Barang' || $s->submenu_judul == 'Jenis' || $s->submenu_judul == 'Merk') {
                DB::table('tbl_akses')->insert(['submenu_id' => $s->submenu_id, 'role_id' => 3, 'akses_type' => 'view', 'created_at' => now()]);
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
        Schema::dropIfExists('tbl_audit_log');
    }
};
