<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

/**
 * Middleware: Only allows Owner (role_id=1, role_slug='admin')
 * All other roles are blocked and redirected with an error message.
 */
class CheckOwnerOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = Session::get('user');

        if (!$user) {
            return redirect('/admin/login');
        }

        // Only Owner (role_id == 1) can access User Management
        if ($user->role_id != 1) {
            // Log the unauthorized access attempt
            DB::table('tbl_audit_log')->insert([
                'user_id'    => $user->user_id,
                'role_slug'  => $user->role_slug,
                'activity'   => 'UNAUTHORIZED_ACCESS_ATTEMPT',
                'module'     => 'User Management',
                'details'    => "Role [{$user->role_title}] attempted to access User Management",
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return abort(403, 'Akses Ditolak! Hanya Owner yang dapat mengakses User Management.');
        }

        return $next($request);
    }
}
