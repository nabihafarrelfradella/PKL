<?php

namespace App\Http\Middleware;

use App\Models\Admin\AksesModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckRoleUser
{
    /**
     * Handle an incoming request.
     * Memeriksa hak akses user berdasarkan role dan tipe menu (menu/submenu).
     * Owner (role_id=1) selalu bypass — akses penuh ke semua fitur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $menu     — redirect path atau ID menu
     * @param  string  $type     — 'menu' | 'submenu'
     */
    public function handle(Request $request, Closure $next, $menu, $type)
    {
        $user = Session::get('user');

        // Pastikan user sudah login
        if (!$user) {
            return redirect('/admin/login');
        }

        // Tentukan tipe akses berdasarkan HTTP method
        $method     = $request->method();
        $akses_type = 'view';
        if ($method === 'POST')                        $akses_type = 'create';
        if (in_array($method, ['PUT', 'PATCH']))       $akses_type = 'update';
        if ($method === 'DELETE')                      $akses_type = 'delete';

        // ─── Owner (role_id=1) — full bypass ───────────────────────────────
        if ($user->role_id == 1) {
            $this->auditLog($user, $akses_type, $menu, "Owner performed {$akses_type} on [{$menu}]");
            return $next($request);
        }

        // ─── Cek permission di database untuk role lain ─────────────────────
        $hasAccess = false;

        if ($type === 'menu') {
            $hasAccess = AksesModel::leftJoin('tbl_menu', 'tbl_menu.menu_id', '=', 'tbl_akses.menu_id')
                ->where('tbl_akses.role_id', $user->role_id)
                ->where('tbl_menu.menu_redirect', $menu)
                ->where('tbl_akses.akses_type', $akses_type)
                ->exists();

        } elseif ($type === 'submenu') {
            $hasAccess = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')
                ->where('tbl_akses.role_id', $user->role_id)
                ->where('tbl_submenu.submenu_redirect', $menu)
                ->where('tbl_akses.akses_type', $akses_type)
                ->exists();
        }

        if (!$hasAccess) {
            // Log percobaan akses tidak sah
            DB::table('tbl_audit_log')->insert([
                'user_id'    => $user->user_id,
                'role_slug'  => $user->role_slug,
                'activity'   => 'UNAUTHORIZED_ACCESS',
                'module'     => $menu,
                'details'    => "Role [{$user->role_title}] attempted [{$akses_type}] on [{$menu}] — ditolak",
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return abort(403, 'Anda tidak memiliki hak akses untuk fitur ini.');
        }

        // Log aktivitas yang berhasil (hanya untuk operasi non-view)
        $this->auditLog($user, $akses_type, $menu, "Role [{$user->role_title}] performed [{$akses_type}] on [{$menu}]");

        return $next($request);
    }

    /**
     * Simpan audit log — hanya untuk aksi yang mengubah data (bukan view).
     */
    private function auditLog($user, string $akses_type, string $module, string $details): void
    {
        if ($akses_type === 'view') {
            return;
        }

        try {
            DB::table('tbl_audit_log')->insert([
                'user_id'    => $user->user_id,
                'role_slug'  => $user->role_slug,
                'activity'   => strtoupper($akses_type),
                'module'     => $module,
                'details'    => $details,
                'ip_address' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Jangan biarkan error audit log menghentikan request
        }
    }
}
