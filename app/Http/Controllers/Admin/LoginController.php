<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function index()
    {
        $data["title"] = "Login - Manajemen Alfatindo";
        return view('Admin.Login.index', $data);
    }

    public function proseslogin(Request $request)
    {
        $username = $request->user;
        $password = md5($request->pwd);

        $user = UserModel::leftJoin('tbl_role', 'tbl_role.role_id', '=', 'tbl_user.role_id')
            ->where('tbl_user.user_nama', $username)
            ->where('tbl_user.user_password', $password)
            ->first();

        if ($user) {
            // Tolak akses jika role adalah Pegawai Teknisi (role_id = 3)
            if ($user->role_id == 3) {
                Session::flash('status', 'error');
                Session::flash('msg', 'Akses ditolak! Teknisi tidak memiliki hak akses login.');
                Session::flash('userInput', $username);
                return redirect('/admin/login');
            }

            // Set Session
            $role = AksesModel::where('role_id', $user->role_id)->get();
            $request->session()->put('user', $user);
            $request->session()->put('user_role', $role);

            // Audit Log (Bungkus try-catch supaya kalau table ga ada, login ga macet)
            try {
                DB::table('tbl_audit_log')->insert([
                    'user_id' => $user->user_id,
                    'role_slug' => $user->role_slug,
                    'activity' => 'Login',
                    'module' => 'Auth',
                    'details' => 'User logged into the system',
                    'ip_address' => $request->ip(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                // Biarkan saja jika table audit log bermasalah
            }

            Session::flash('status', 'success');
            Session::flash('msg', 'Selamat Datang ' . $user->user_nmlengkap);
            return redirect('/admin/dashboard');

        } else {
            Session::flash('status', 'error');
            Session::flash('msg', 'Username atau Password salah!');
            Session::flash('userInput', $username);
            return redirect('/admin/login');
        }
    }

    public function logout()
    {
        \App\Helpers\AuditLogHelper::log('Logout', 'Auth', 'User logged out of the system');
        Session::forget('user');
        Session::forget('user_role');
        return redirect('/admin/login');
    }
}