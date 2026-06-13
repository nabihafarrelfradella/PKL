<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuditLogHelper
{
    public static function log($activity, $module, $details = null)
    {
        $user = Session::get('user');
        if ($user) {
            $roleSlug = $user->role_slug;
            if (!$roleSlug) {
                $role = DB::table('tbl_role')->where('role_id', $user->role_id)->first();
                $roleSlug = $role ? $role->role_slug : 'unknown';
            }
            DB::table('tbl_audit_log')->insert([
                'user_id' => $user->user_id,
                'role_slug' => $roleSlug,
                'activity' => $activity,
                'module' => $module,
                'details' => $details,
                'ip_address' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
