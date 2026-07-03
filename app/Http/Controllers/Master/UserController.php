<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\RoleModel;
use App\Models\Admin\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    // =========================================================
    // Helpers
    // =========================================================
    private function logActivity($activity, $details)
    {
        $user = Session::get('user');
        DB::table('tbl_audit_log')->insert([
            'user_id'    => $user->user_id,
            'role_slug'  => $user->role_slug,
            'activity'   => $activity,
            'module'     => 'User Management',
            'details'    => $details,
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // =========================================================
    // LEGACY – User List (generic, used by old admin/user routes)
    // =========================================================
    public function index()
    {
        $data['title'] = 'User';
        $data['role']  = RoleModel::latest()->get();
        return view('Master.User.index', $data);
    }

    public function profile(UserModel $user)
    {
        $data['title'] = 'Profile';
        $data['data']  = UserModel::leftJoin('tbl_role', 'tbl_role.role_id', '=', 'tbl_user.role_id')
            ->select()
            ->where('tbl_user.user_id', '=', $user->user_id)
            ->first();
        return view('Master.User.profile', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = UserModel::leftJoin('tbl_role', 'tbl_role.role_id', '=', 'tbl_user.role_id')
                ->select()
                ->orderBy('user_id', 'DESC')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('img', function ($row) {
                    if ($row->user_foto == 'undraw_profile.svg') {
                        $img = '<span class="avatar avatar-lg brround cover-image" style="background: url(&quot;' . url('/assets/default/users') . '/' . $row->user_foto . '&quot;) center center;"></span>';
                    } else {
                        $img = '<span class="avatar avatar-lg brround cover-image" style="background: url(&quot;' . asset('storage/users/' . $row->user_foto) . '&quot;) center center;"></span>';
                    }
                    return $img;
                })
                ->addColumn('role', function ($row) {
                    return '<span class="badge bg-primary badge-sm me-1 mb-1 mt-1">' . $row->role_title . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $array = [
                        'user_id'        => $row->user_id,
                        'user_nama'      => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->user_nama)),
                        'user_nmlengkap' => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->user_nmlengkap)),
                        'user_foto'      => $row->user_foto,
                        'role_id'        => $row->role_id,
                        'user_email'     => $row->user_email,
                    ];
                    return '
                    <div class="g-2">
                        <a class="btn modal-effect text-primary btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Umodaldemo8" onclick=\'update(' . json_encode($array) . ')\'>
                            <span class="fe fe-edit text-success fs-14"></span>
                        </a>
                        <a class="btn modal-effect text-danger btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Hmodaldemo8" onclick=\'hapus(' . json_encode($array) . ')\'>
                            <span class="fe fe-trash-2 fs-14"></span>
                        </a>
                    </div>';
                })
                ->rawColumns(['action', 'img', 'role'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $img = $request->file('photo') ? $request->file('photo')->hashName() : 'undraw_profile.svg';
        if ($request->file('photo')) {
            $destinationPath = public_path('storage/users');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $request->file('photo')->move($destinationPath, $img);
        }

        $user = UserModel::create([
            'user_foto'      => $img,
            'user_nmlengkap' => $request->nmlengkap,
            'user_nama'      => $request->username,
            'user_email'     => $request->email,
            'role_id'        => $request->role,
            'user_password'  => md5($request->pwd),
        ]);

        $this->logActivity('CREATE', "Created user: {$user->user_nama} ({$user->user_email}) with role_id: {$user->role_id}");

        Session::flash('status', 'success');
        Session::flash('msg', 'Berhasil ditambah!');
        return redirect()->route('user.index');
    }

    public function update(Request $request, UserModel $user)
    {
        $updateData = [
            'user_nmlengkap' => $request->nmlengkapU,
            'user_nama'      => $request->usernameU,
            'user_email'     => $request->emailU,
            'role_id'        => $request->roleU,
        ];

        if ($request->pwdU != '') {
            $updateData['user_password'] = md5($request->pwdU);
        }

        if ($request->hasFile('photoU')) {
            $image = $request->file('photoU');
            $filename = $image->hashName();
            $destinationPath = public_path('storage/users');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $filename);
            if ($user->user_foto != 'undraw_profile.svg' && $user->user_foto != '') {
                $oldPath = public_path('storage/users/' . $user->user_foto);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $updateData['user_foto'] = $filename;
        }

        $user->update($updateData);
        $this->logActivity('UPDATE', "Updated user: {$user->user_nama} (user_id: {$user->user_id})");

        Session::flash('status', 'success');
        Session::flash('msg', 'Berhasil diubah!');
        return redirect()->route('user.index');
    }

    public function hapus(Request $request)
    {
        $detail = UserModel::findOrFail($request->iduser);
        if ($detail->user_foto != 'undraw_profile.svg' && $detail->user_foto != '') {
            $oldPath = public_path('storage/users/' . $detail->user_foto);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        $detail->delete();
        $this->logActivity('DELETE', "Deleted user: {$detail->user_nama} (user_id: {$request->iduser})");

        Session::flash('status', 'success');
        Session::flash('msg', 'Berhasil dihapus!');
        return redirect()->route('user.index');
    }

    public function updatePassword(Request $request, UserModel $user)
    {
        $checkPassword = UserModel::where([
            'user_id'       => $user->user_id,
            'user_password' => md5($request->currentpassword),
        ])->count();

        if ($checkPassword > 0) {
            $user->update(['user_password' => md5($request->newpassword)]);
            $this->logActivity('UPDATE_PASSWORD', "Updated password for user: {$user->user_nama}");
            Session::flash('status', 'success');
            Session::flash('msg', 'Password berhasil di ubah!');
        } else {
            Session::flash('status', 'error');
            Session::flash('msg', 'Password saat ini tidak sama dengan password lama!');
        }

        return redirect(url('admin/profile/' . $user->user_id));
    }

    public function updateProfile(Request $request, UserModel $user)
    {
        $updateData = [
            'user_nmlengkap' => $request->nmlengkap,
            'user_nama'      => $request->username,
            'user_email'     => $request->email,
        ];

        if ($request->has('remove_photo') && $request->remove_photo == '1') {
            if ($user->user_foto != 'undraw_profile.svg' && $user->user_foto != '') {
                $oldPath = public_path('storage/users/' . $user->user_foto);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $updateData['user_foto'] = 'undraw_profile.svg';
        } elseif ($request->hasFile('photoU')) {
            $image = $request->file('photoU');
            $filename = $image->hashName();
            $destinationPath = public_path('storage/users');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $filename);
            if ($user->user_foto != 'undraw_profile.svg' && $user->user_foto != '') {
                $oldPath = public_path('storage/users/' . $user->user_foto);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $updateData['user_foto'] = $filename;
        }

        $user->update($updateData);
        
        // Update Session if the updated user is the current logged-in user
        if (Session::has('user') && Session::get('user')->user_id == $user->user_id) {
            $updatedUser = UserModel::leftJoin('tbl_role', 'tbl_role.role_id', '=', 'tbl_user.role_id')
                ->where('tbl_user.user_id', $user->user_id)
                ->first();
            Session::put('user', $updatedUser);
        }

        Session::flash('status', 'success');
        Session::flash('msg', 'Profile Berhasil diubah!');
        return redirect(url('admin/profile/' . $user->user_id));
    }

    // =========================================================
    // NEW: Daftar Teknisi (Pegawai Teknisi, role_id=3)
    // =========================================================
    public function teknisiIndex()
    {
        $data['title'] = 'Daftar Teknisi';
        return view('Master.UserManagement.teknisi', $data);
    }

    public function teknisiShow(Request $request)
    {
        if ($request->ajax()) {
            // role_id = 3 = Pegawai / Teknisi
            $data = UserModel::leftJoin('tbl_role', 'tbl_role.role_id', '=', 'tbl_user.role_id')
                ->where('tbl_user.role_id', 3)
                ->select('tbl_user.*', 'tbl_role.role_title', 'tbl_role.role_slug')
                ->orderBy('user_id', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('foto', function ($row) {
                    if ($row->user_foto == 'undraw_profile.svg' || empty($row->user_foto)) {
                        $imgUrl = url('/assets/default/users/undraw_profile.svg');
                    } else {
                        $imgUrl = asset('storage/users/' . $row->user_foto);
                    }
                    return '<a href="javascript:void(0)" onclick="lihatFotoTeknisi(\'' . $imgUrl . '\')">
                                <span class="avatar avatar-md brround cover-image" style="background: url(&quot;' . $imgUrl . '&quot;) center center;"></span>
                            </a>';
                })
                ->editColumn('user_email', function ($row) {
                    return str_contains($row->user_email, '@no-email.local') ? '' : $row->user_email;
                })
                ->editColumn('jenis_kelamin', function ($row) {
                    return $row->jenis_kelamin == 'M' ? 'Laki-laki (M)' : 'Perempuan (F)';
                })
                ->addColumn('action', function ($row) {
                    $array = [
                        'user_id'        => $row->user_id,
                        'user_nama'      => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->user_nama)),
                        'user_nmlengkap' => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->user_nmlengkap)),
                        'user_email'     => $row->user_email,
                        'user_phone'     => $row->user_phone ?? '',
                        'jenis_kelamin'  => $row->jenis_kelamin,
                        'tanggal_lahir'  => $row->tanggal_lahir,
                        'teknisi_sn'     => $row->teknisi_sn,
                        'user_foto'      => $row->user_foto,
                    ];
                    return '
                    <div class="d-flex gap-1">
                        <a class="btn btn-sm btn-success-light" data-bs-toggle="modal" href="#modalEditTeknisi" onclick="editTeknisi(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')">
                            <span class="fe fe-edit fs-14"></span>
                        </a>
                        <a class="btn btn-sm btn-danger-light" data-bs-toggle="modal" href="#modalHapusTeknisi" onclick="hapusTeknisi(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')">
                            <span class="fe fe-trash-2 fs-14"></span>
                        </a>
                    </div>';
                })
                ->rawColumns(['action', 'foto'])
                ->make(true);
        }
    }

    public function teknisiStore(Request $request)
    {
        $request->validate([
            'nmlengkap'     => 'required|string|max:255',
            'email'         => 'nullable|email|unique:tbl_user,user_email',
            'jenis_kelamin' => 'required|in:M,F',
            'tanggal_lahir' => 'required|date',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nmlengkap.required'     => 'Nama Lengkap wajib diisi',
            'email.required'         => 'Email wajib diisi',
            'email.unique'           => 'Email sudah digunakan',
            'jenis_kelamin.required' => 'Jenis Kelamin wajib diisi',
            'jenis_kelamin.in'       => 'Jenis Kelamin harus M atau F',
            'tanggal_lahir.required' => 'Tanggal Lahir wajib diisi',
            'tanggal_lahir.date'     => 'Format Tanggal Lahir tidak valid',
        ]);

        $dob = \Carbon\Carbon::parse($request->tanggal_lahir);
        $teknisi_sn = $request->jenis_kelamin . '-' . $dob->format('d') . '-' . $dob->format('Y');

        $email = $request->email;
        if (empty($email)) {
            $email = strtolower($teknisi_sn) . '_' . time() . '@no-email.local';
        }

        // Proteksi Duplikasi Teknisi SN
        $cekDuplikat = UserModel::where('teknisi_sn', $teknisi_sn)->exists();
        if ($cekDuplikat) {
            return response()->json(['error' => 'Gagal! Sudah ada Teknisi lain dengan kombinasi Jenis Kelamin dan Tanggal Lahir yang sama.'], 400);
        }

        $foto = 'undraw_profile.svg';
        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $foto = $image->hashName();
            $destinationPath = public_path('storage/users');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $foto);
        }

        $user = UserModel::create([
            'user_foto'      => $foto,
            'user_nmlengkap' => $request->nmlengkap,
            'user_nama'      => 'teknisi_' . time() . rand(100,999), // Auto-generate dummy username
            'user_email'     => $email,
            'user_phone'     => $request->phone ?? null,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'tanggal_lahir'  => $request->tanggal_lahir,
            'teknisi_sn'     => $teknisi_sn,
            'role_id'        => 3, // Pegawai Teknisi — hardcoded
            'user_password'  => md5(\Illuminate\Support\Str::random(12)), // Auto-generate random password
        ]);

        $this->logActivity('CREATE_TEKNISI', "Owner created Teknisi account: {$user->user_nama} ({$user->user_email}) with ID: {$teknisi_sn}");

        return response()->json(['success' => 'Data Teknisi berhasil ditambahkan!']);
    }

    public function teknisiUpdate(Request $request, UserModel $user)
    {
        // Ensure we only update teknisi accounts
        if ($user->role_id != 3) {
            return response()->json(['error' => 'Data ini bukan Pegawai Teknisi!'], 403);
        }

        $request->validate([
            'nmlengkap'     => 'required|string|max:255',
            'email'         => 'nullable|email|unique:tbl_user,user_email,' . $user->user_id . ',user_id',
            'jenis_kelamin' => 'required|in:M,F',
            'tanggal_lahir' => 'required|date',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $oldSn = $user->teknisi_sn;
        // Re-generate SN jika gender atau dob berubah
        $dob = \Carbon\Carbon::parse($request->tanggal_lahir);
        $teknisi_sn = $request->jenis_kelamin . '-' . $dob->format('d') . '-' . $dob->format('Y');

        if ($oldSn !== $teknisi_sn) {
            $cekDuplikat = UserModel::where('teknisi_sn', $teknisi_sn)->where('user_id', '!=', $user->user_id)->exists();
            if ($cekDuplikat) {
                return response()->json(['error' => 'Gagal! Perubahan dibatalkan karena sudah ada Teknisi lain dengan kombinasi Jenis Kelamin dan Tanggal Lahir tersebut.'], 400);
            }
        }

        $email = $request->email;
        if (empty($email)) {
            $email = $user->user_email;
        }

        $updateData = [
            'user_nmlengkap' => $request->nmlengkap,
            'user_email'     => $email,
            'user_phone'     => $request->phone ?? null,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'tanggal_lahir'  => $request->tanggal_lahir,
            'teknisi_sn'     => $teknisi_sn,
        ];

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $filename = $image->hashName();
            $destinationPath = public_path('storage/users');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $filename);
            
            if ($user->user_foto != 'undraw_profile.svg' && $user->user_foto != '') {
                $oldPath = public_path('storage/users/' . $user->user_foto);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $updateData['user_foto'] = $filename;
        }

        $user->update($updateData);

        // Update associated records in tbl_barangkeluar
        if ($oldSn && $oldSn !== $teknisi_sn) {
            DB::table('tbl_barangkeluar')->where('teknisi', $oldSn)->update(['teknisi' => $teknisi_sn]);
        }

        $this->logActivity('UPDATE_TEKNISI', "Owner updated Teknisi account: {$user->user_nama} (user_id: {$user->user_id})");

        return response()->json(['success' => 'Data Teknisi berhasil diperbarui!']);
    }

    public function teknisiDestroy(Request $request, UserModel $user)
    {
        // Ensure we only delete teknisi accounts
        if ($user->role_id != 3) {
            return response()->json(['error' => 'Data ini bukan Pegawai Teknisi!'], 403);
        }

        $nama = $user->user_nama;
        $id   = $user->user_id;
        $user->delete();

        $this->logActivity('DELETE_TEKNISI', "Owner deleted Teknisi account: {$nama} (user_id: {$id})");
        return response()->json(['success' => 'Data Teknisi berhasil dihapus!']);
    }

    // =========================================================
    // Admin Gudang (role_id=2) — View + Edit only (1 akun)
    // =========================================================
    public function adminGudangIndex()
    {
        $data['title']       = 'Admin Gudang';
        $data['adminGudang'] = UserModel::leftJoin('tbl_role', 'tbl_role.role_id', '=', 'tbl_user.role_id')
            ->where('tbl_user.role_id', 2)
            ->select('tbl_user.*', 'tbl_role.role_title')
            ->first();
        return view('Master.UserManagement.admin_gudang', $data);
    }

    public function adminGudangUpdate(Request $request, UserModel $user)
    {
        if ($user->role_id != 2) {
            return response()->json(['error' => 'Akun ini bukan Admin Gudang!'], 403);
        }

        $request->validate([
            'nmlengkap' => 'required|string|max:255',
            'username'  => 'required|string|max:100|unique:tbl_user,user_nama,' . $user->user_id . ',user_id',
            'email'     => 'required|email|unique:tbl_user,user_email,' . $user->user_id . ',user_id',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $updateData = [
            'user_nmlengkap' => $request->nmlengkap,
            'user_nama'      => $request->username,
            'user_email'     => $request->email,
        ];

        if ($request->pwd && $request->pwd != '') {
            $updateData['user_password'] = md5($request->pwd);
        }

        if ($request->has('remove_photo') && $request->remove_photo == '1') {
            if ($user->user_foto != 'undraw_profile.svg' && $user->user_foto != '') {
                $oldPath = public_path('storage/users/' . $user->user_foto);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $updateData['user_foto'] = 'undraw_profile.svg';
        } elseif ($request->hasFile('foto')) {
            $image           = $request->file('foto');
            $filename        = $image->hashName();
            $destinationPath = public_path('storage/users');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $filename);
            // Hapus foto lama jika bukan default
            if ($user->user_foto != 'undraw_profile.svg' && $user->user_foto != '') {
                $oldPath = public_path('storage/users/' . $user->user_foto);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $updateData['user_foto'] = $filename;
        }

        $user->update($updateData);
        $this->logActivity('UPDATE_ADMIN_GUDANG', "Owner updated Admin Gudang: {$user->user_nama} (user_id: {$user->user_id})");

        return response()->json(['success' => 'Akun Staff Gudang berhasil diperbarui!']);
    }

    // =========================================================
    // NEW: Access Control — Interactive Toggle RBAC
    // =========================================================
    public function accessControl()
    {
        $data['title'] = 'Access Control';

        // Definisi fitur/modul yang bisa di-toggle
        // key = identifier unik, type = 'menu'|'submenu', redirect = path yang digunakan di middleware
        $data['modules'] = $this->getModuleDefinitions();

        // Ambil semua akses yang ada untuk role 2 dan 3
        $data['aksesRole2'] = DB::table('tbl_akses')
            ->where('role_id', 2)
            ->get()
            ->groupBy(function ($r) {
                return ($r->menu_id ? 'menu_' . $r->menu_id : 'sub_' . $r->submenu_id) . '_' . $r->akses_type;
            });

        $data['aksesRole3'] = DB::table('tbl_akses')
            ->where('role_id', 3)
            ->get()
            ->groupBy(function ($r) {
                return ($r->menu_id ? 'menu_' . $r->menu_id : 'sub_' . $r->submenu_id) . '_' . $r->akses_type;
            });

        // Ambil submenu_id dan menu_id untuk setiap modul
        $submenus = DB::table('tbl_submenu')->get()->keyBy('submenu_redirect');
        $menus    = DB::table('tbl_menu')->get()->keyBy('menu_slug');
        $data['submenus'] = $submenus;
        $data['menus']    = $menus;

        return view('Master.UserManagement.access_control', $data);
    }

    /**
     * AJAX: Toggle satu permission ON/OFF untuk satu role.
     * Body: { role_id, ref_type (menu|submenu), ref_id, akses_type, enabled (bool) }
     */
    public function accessControlToggle(Request $request)
    {
        $roleId    = (int) $request->role_id;
        $refType   = $request->ref_type;   // 'menu' atau 'submenu'
        $refId     = $request->ref_id;
        $aksesType = $request->akses_type;
        $enabled   = (bool) $request->enabled;

        // Proteksi: Owner tidak bisa diubah
        if ($roleId == 1) {
            return response()->json(['error' => 'Akses Owner tidak dapat diubah.'], 403);
        }

        // Validasi role yang ada
        if (!in_array($roleId, [2, 3])) {
            return response()->json(['error' => 'Role tidak valid.'], 400);
        }

        $where = ['role_id' => $roleId, 'akses_type' => $aksesType];
        if ($refType === 'menu') {
            $where['menu_id'] = $refId;
        } else {
            $where['submenu_id'] = $refId;
        }

        if ($enabled) {
            // Aktifkan: insert jika belum ada
            $exists = DB::table('tbl_akses')->where($where)->exists();
            if (!$exists) {
                DB::table('tbl_akses')->insert(array_merge($where, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        } else {
            // Nonaktifkan: hapus entry
            DB::table('tbl_akses')->where($where)->delete();
        }

        $roleName = $roleId == 2 ? 'Admin Gudang' : 'Pegawai Teknisi';
        $action   = $enabled ? 'GRANT' : 'REVOKE';
        $this->logActivity(
            "ACCESS_CONTROL_{$action}",
            "Owner {$action} [{$aksesType}] on [{$refType}:{$refId}] for role [{$roleName}]"
        );

        return response()->json([
            'success' => true,
            'message' => $enabled ? 'Akses diberikan.' : 'Akses dicabut.',
        ]);
    }

    /**
     * Helper: definisi semua modul yang bisa dikontrol.
     * Mengembalikan array dengan struktur:
     *   [ label, ref_type, ref_slug (menu_slug atau submenu_redirect), akses_types[] ]
     */
    private function getModuleDefinitions(): array
    {
        return [
            // Dashboard
            ['label' => 'Dashboard', 'group' => 'Dashboard', 'ref_type' => 'menu', 'ref_slug' => 'dashboard', 'types' => ['view', 'create', 'update', 'delete']],

            // Master Barang
            ['label' => 'Master Barang (menu)', 'group' => 'Master Barang', 'ref_type' => 'menu', 'ref_slug' => 'master-barang', 'types' => ['view']],
            ['label' => 'Merk Barang', 'group' => 'Master Barang', 'ref_type' => 'submenu', 'ref_slug' => '/merk', 'types' => ['view', 'create', 'update', 'delete']],
            ['label' => 'Data Barang', 'group' => 'Master Barang', 'ref_type' => 'submenu', 'ref_slug' => '/barang', 'types' => ['view', 'create', 'update', 'delete']],

            // Transaksi
            ['label' => 'Transaksi (menu)', 'group' => 'Transaksi', 'ref_type' => 'menu', 'ref_slug' => 'transaksi', 'types' => ['view']],
            ['label' => 'Barang Masuk', 'group' => 'Transaksi', 'ref_type' => 'submenu', 'ref_slug' => '/barang-masuk', 'types' => ['view', 'create', 'update', 'delete']],
            ['label' => 'Barang Keluar', 'group' => 'Transaksi', 'ref_type' => 'submenu', 'ref_slug' => '/barang-keluar', 'types' => ['view', 'create', 'update', 'delete']],
            ['label' => 'Barang Tracking', 'group' => 'Transaksi', 'ref_type' => 'submenu', 'ref_slug' => '/barang-tracking', 'types' => ['view', 'create', 'update', 'delete']],

            // Laporan
            ['label' => 'Laporan (menu)', 'group' => 'Laporan', 'ref_type' => 'menu', 'ref_slug' => 'laporan', 'types' => ['view']],
            ['label' => 'Lap. Barang Masuk', 'group' => 'Laporan', 'ref_type' => 'submenu', 'ref_slug' => '/lap-barang-masuk', 'types' => ['view', 'create', 'update', 'delete']],
            ['label' => 'Lap. Barang Keluar', 'group' => 'Laporan', 'ref_type' => 'submenu', 'ref_slug' => '/lap-barang-keluar', 'types' => ['view', 'create', 'update', 'delete']],
            ['label' => 'Lap. Stok Barang', 'group' => 'Laporan', 'ref_type' => 'submenu', 'ref_slug' => '/lap-stok-barang', 'types' => ['view', 'create', 'update', 'delete']],
        ];
    }
}

