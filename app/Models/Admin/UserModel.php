<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    use HasFactory;
    protected $table = "tbl_user";
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'role_id',
        'user_nama',
        'user_nmlengkap',
        'user_email',
        'user_password',
        'user_foto',
        'user_phone',
        'jenis_kelamin',
        'tanggal_lahir',
        'teknisi_sn',
    ];

    public function getRoleSlugAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }
        $role = \Illuminate\Support\Facades\DB::table('tbl_role')->where('role_id', $this->role_id)->first();
        return $role ? $role->role_slug : 'unknown';
    }

    public function getRoleTitleAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }
        $role = \Illuminate\Support\Facades\DB::table('tbl_role')->where('role_id', $this->role_id)->first();
        return $role ? $role->role_title : 'Unknown';
    }
}
