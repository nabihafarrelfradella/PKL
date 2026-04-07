<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    public function index()
    {
        $data["title"] = "Login";
        return view('Admin.Login.index', $data);
    }

    public function proseslogin(Request $request)
    {
        $where = array(
            'tbl_user.user_nama' => $request->user,
            'tbl_user.user_password' => md5($request->pwd)
        );
        $getCount = UserModel::where($where)->count();

        if ($getCount > 0) {
            $query = UserModel::leftJoin('tbl_role', 'tbl_role.role_id', '=', 'tbl_user.role_id')->select()->where($where)->first();
            
            // Overhaul: Strict Role-Based Authentication
            // Only Admin (role_id 1) and Staff Gudang (role_id 2) are allowed to login
            if ($query->role_id != 1 && $query->role_id != 2) {
                Session::flash('status', 'error');
                Session::flash('msg', 'Role Anda tidak diizinkan untuk login!');
                return redirect(URL::previous());
            }

            $role = AksesModel::where('role_id', '=', $query->role_id)->get();

            $request->session()->put('user', $query);
            $request->session()->put('user_role', $role);

            // Audit Logging: Successful Login
            \Illuminate\Support\Facades\DB::table('tbl_audit_log')->insert([
                'user_id' => $query->user_id,
                'role_slug' => $query->role_slug,
                'activity' => 'Login',
                'module' => 'Auth',
                'details' => 'User logged into the system',
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Session::flash('status', 'success');
            Session::flash('msg', 'Selamat Datang ' . $query->user_nmlengkap);

            //redirect to dashboard
            return redirect('/admin/dashboard');
        } else {
            Session::flash('status', 'error');
            Session::flash('msg', 'User password tidak cocok!');
            Session::flash('userInput', $request->user);

            //redirect to index
            return redirect(URL::previous());
        }
    }

    public function logout()
    {
        Session::forget('user');
        Session::forget('user_role');

        //redirect to index
        return redirect(URL::previous());
    }
}
