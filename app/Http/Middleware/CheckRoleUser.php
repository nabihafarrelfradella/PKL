<?php

namespace App\Http\Middleware;

use App\Models\Admin\AksesModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckRoleUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $menu, $type)
    {   
        $getMenu = 0;
        $user = Session::get('user');
        
        // Define access type based on HTTP method
        $method = $request->method();
        $akses_type = 'view'; // Default
        if ($method == 'POST') $akses_type = 'create';
        if (in_array($method, ['PUT', 'PATCH'])) $akses_type = 'update';
        if ($method == 'DELETE') $akses_type = 'delete';

        // Superadmin Bypass: Admin (Role 1) always has full access to everything
        if ($user->role_id == 1) {
            // Audit Logging for Authorized Activity (Admin)
            if ($akses_type != 'view') {
                \Illuminate\Support\Facades\DB::table('tbl_audit_log')->insert([
                    'user_id' => $user->user_id,
                    'role_slug' => $user->role_slug,
                    'activity' => strtoupper($akses_type),
                    'module' => $menu,
                    'details' => "Superadmin performed {$akses_type} action in {$menu}",
                    'ip_address' => $request->ip(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            return $next($request);
        }

        // Non-Admin: Deny access to User Management (othermenu 1-5)
        if ($type == 'othermenu' && in_array($menu, [1, 2, 3, 4, 5])) {
            \Illuminate\Support\Facades\DB::table('tbl_audit_log')->insert([
                'user_id' => $user->user_id,
                'role_slug' => $user->role_slug,
                'activity' => 'UNAUTHORIZED_ACCESS_ATTEMPT',
                'module' => 'User Management',
                'details' => "Non-Admin role {$user->role_title} tried to access Superadmin module {$menu}",
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return abort(403, 'Hanya Superadmin yang dapat mengakses fitur ini.');
        }

        // Check explicit permissions in database for other roles
        if($type == 'othermenu'){
            $getMenu = AksesModel::where(array('role_id' => $user->role_id, 'othermenu_id' => $menu, 'akses_type' => $akses_type))->count();
        }else if($type == 'menu'){
            $getMenu = AksesModel::leftJoin('tbl_menu', 'tbl_menu.menu_id', '=', 'tbl_akses.menu_id')
                ->where(array('tbl_akses.role_id' => $user->role_id, 'tbl_menu.menu_redirect' => $menu, 'tbl_akses.akses_type' => $akses_type))
                ->count();
        }else if($type == 'submenu'){
            $getMenu = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')
                ->where(array('tbl_akses.role_id' => $user->role_id, 'tbl_submenu.submenu_redirect' => $menu, 'tbl_akses.akses_type' => $akses_type))
                ->count();
        }

        if($getMenu == 0){
            // Audit Logging for Unauthorized Access Attempt
            \Illuminate\Support\Facades\DB::table('tbl_audit_log')->insert([
                'user_id' => $user->user_id,
                'role_slug' => $user->role_slug,
                'activity' => 'Unauthorized Access Attempt',
                'module' => $menu,
                'details' => "Role {$user->role_title} tried to {$akses_type} in {$menu}",
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return abort(403, 'Anda tidak memiliki hak akses untuk fitur ini.');
        }

        // Audit Logging for Authorized Activity
        if ($akses_type != 'view') {
            \Illuminate\Support\Facades\DB::table('tbl_audit_log')->insert([
                'user_id' => $user->user_id,
                'role_slug' => $user->role_slug,
                'activity' => strtoupper($akses_type),
                'module' => $menu,
                'details' => "User performed {$akses_type} action in {$menu}",
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return $next($request);
    }
}
