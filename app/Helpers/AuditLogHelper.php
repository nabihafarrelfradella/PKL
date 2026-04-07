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
            DB::table('tbl_audit_log')->insert([
                'user_id' => $user->user_id,
                'role_slug' => $user->role_slug,
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
