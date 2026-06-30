<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function checkAccess($roleId, $redirect, $akses_type = 'create', $type = 'submenu')
    {
        if ($roleId == 1) return 1;

        if ($type === 'menu') {
            return \App\Models\Admin\AksesModel::leftJoin('tbl_menu', 'tbl_menu.menu_id', '=', 'tbl_akses.menu_id')
                ->where('tbl_akses.role_id', $roleId)
                ->where('tbl_menu.menu_redirect', $redirect)
                ->where('tbl_akses.akses_type', $akses_type)
                ->exists() ? 1 : 0;
        } else {
            return \App\Models\Admin\AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')
                ->where('tbl_akses.role_id', $roleId)
                ->where('tbl_submenu.submenu_redirect', $redirect)
                ->where('tbl_akses.akses_type', $akses_type)
                ->exists() ? 1 : 0;
        }
    }
}
