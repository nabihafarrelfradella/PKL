<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\MenuModel;
use App\Models\Admin\RoleModel;
use App\Models\Admin\SubmenuModel;
use App\Models\Admin\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AksesController extends Controller
{
    private function logActivity($activity, $details)
    {
        $user = Session::get('user');
        DB::table('tbl_audit_log')->insert([
            'user_id' => $user->user_id,
            'role_slug' => $user->role_slug,
            'activity' => $activity,
            'module' => 'Access Control',
            'details' => $details,
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function index($role)
    {
        $data["title"] = "Akses";
        $data["roleid"] = $role == 'role' ? '' : $role;
        $data["detailrole"] = $role == 'role' ? '' : RoleModel::where('role_id', '=', $role)->first();
        $data["role"] = RoleModel::latest()->get();
        $data["menu"] = MenuModel::where('menu_type', '=', '1')->orderBy('menu_sort', 'ASC')->get();
        $data["menusub"] = MenuModel::where('menu_type', '=', '2')->orderBy('menu_sort', 'ASC')->get();
        return view('Master.Akses.index', $data);
    }

    public function addAkses($idmenu, $idrole, $type, $akses)
    {
        if ($type == 'menu') {
            //create input menu
            AksesModel::create([
                'menu_id' => $idmenu,
                'role_id' => $idrole,
                'akses_type' => $akses
            ]);
        } else if ($type == 'submenu') {
            //create input submenu
            AksesModel::create([
                'submenu_id' => $idmenu,
                'role_id' => $idrole,
                'akses_type' => $akses
            ]);
        } else if ($type == 'othermenu') {
            //create input othermenu
            AksesModel::create([
                'othermenu_id' => $idmenu,
                'role_id' => $idrole,
                'akses_type' => $akses
            ]);
        }

        $this->logActivity('GRANT_ACCESS', "Granted {$akses} access for {$type} (ID: {$idmenu}) to role_id: {$idrole}");

        $data['title'] = "Akses";

        //redirect to index
        return redirect(url('admin/akses/' . $idrole))->with($data);
    }

    public function removeAkses($idmenu, $idrole, $type, $akses)
    {
        if ($type == 'menu') {
            AksesModel::where(array('menu_id' => $idmenu, 'role_id' => $idrole, 'akses_type' => $akses))->delete();
        } else if ($type == 'submenu') {
            AksesModel::where(array('submenu_id' => $idmenu, 'role_id' => $idrole, 'akses_type' => $akses))->delete();
        } else if ($type == 'othermenu') {
            AksesModel::where(array('othermenu_id' => $idmenu, 'role_id' => $idrole, 'akses_type' => $akses))->delete();
        }

        $this->logActivity('REVOKE_ACCESS', "Revoked {$akses} access for {$type} (ID: {$idmenu}) from role_id: {$idrole}");

        $data['title'] = "Akses";
        //redirect to index
        return redirect(url('admin/akses/' . $idrole))->with($data);
    }

    public function setAllAkses($idrole)
    {
        // Proteksi: akses Owner tidak bisa diubah via UI (Owner selalu bypass middleware)
        if ($idrole == 1) {
            return redirect(url('admin/akses/' . $idrole))
                ->with('status', 'error')
                ->with('msg', 'Akses Owner tidak dapat diubah.');
        }

        AksesModel::where('role_id', $idrole)->delete();
        $rows = [];

        // Grant all access untuk semua menu
        $menus = MenuModel::orderBy('menu_sort', 'ASC')->get();
        foreach ($menus as $m) {
            foreach (['view', 'create', 'update', 'delete'] as $type) {
                $rows[] = [
                    'menu_id'    => $m->menu_id,
                    'role_id'    => $idrole,
                    'akses_type' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Grant all access untuk semua submenu
        $submenus = SubmenuModel::orderBy('submenu_sort', 'ASC')->get();
        foreach ($submenus as $sb) {
            foreach (['view', 'create', 'update', 'delete'] as $type) {
                $rows[] = [
                    'submenu_id' => $sb->submenu_id,
                    'role_id'    => $idrole,
                    'akses_type' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($rows)) {
            AksesModel::insert($rows);
        }

        $this->logActivity('GRANT_ALL_ACCESS', "Granted ALL access to role_id: {$idrole}");

        $data['title'] = "Akses";
        return redirect(url('admin/akses/' . $idrole))->with($data);
    }

    public function unsetAllAkses($idrole)
    {
        // Proteksi: akses Owner tidak dapat dicabut
        if ($idrole == 1) {
            return redirect(url('admin/akses/' . $idrole))
                ->with('status', 'error')
                ->with('msg', 'Akses Owner tidak dapat diubah.');
        }

        AksesModel::where('role_id', $idrole)->delete();

        $this->logActivity('REVOKE_ALL_ACCESS', "Revoked ALL access from role_id: {$idrole}");

        $data['title'] = "Akses";
        return redirect(url('admin/akses/' . $idrole))->with($data);
    }
}
