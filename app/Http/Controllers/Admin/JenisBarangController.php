<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\JenisBarangModel;
use App\Models\Admin\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class JenisBarangController extends Controller
{
    public function index()
    {
        $data["title"] = "Jenis";
        $data["hakTambah"] = (Session::get('user')->role_id == 1 || Session::get('user')->role_id == 2) ? 1 : 0;
        return view('Admin.JenisBarang.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = JenisBarangModel::orderBy('jenisbarang_id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('ket', function ($row) {
                    // Menggunakan nama kolom jenisbarang_keterangan
                    return $row->jenisbarang_keterangan ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $array = array(
                        "jenisbarang_id" => $row->jenisbarang_id,
                        "jenisbarang_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->jenisbarang_nama)),
                        // Sesuaikan key array untuk dikirim ke JS modal
                        "jenisbarang_keterangan" => $row->jenisbarang_keterangan,
                    );
                    
                    $button = '';
                    $roleId = Session::get('user')->role_id;
                    $hakEdit = ($roleId == 1 || $roleId == 2) ? 1 : 0;
                    $hakDelete = ($roleId == 1 || $roleId == 2) ? 1 : 0;

                    if ($hakEdit > 0 && $hakDelete > 0) {
                        $button .= '
                        <a class="btn modal-effect text-primary btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Umodaldemo8" data-bs-toggle="tooltip" data-bs-original-title="Edit" onclick="update(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')"><span class="fe fe-edit text-success fs-14"></span></a>
                        <a class="btn modal-effect text-danger btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Hmodaldemo8" onclick="hapus(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')"><span class="fe fe-trash-2 fs-14"></span></a>
                        ';
                    } else if ($hakEdit > 0 && $hakDelete == 0) {
                        $button .= '
                            <a class="btn modal-effect text-primary btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Umodaldemo8" data-bs-toggle="tooltip" data-bs-original-title="Edit" onclick="update(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')"><span class="fe fe-edit text-success fs-14"></span></a>
                        ';
                    } else if ($hakEdit == 0 && $hakDelete > 0) {
                        $button .= '
                        <a class="btn modal-effect text-danger btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Hmodaldemo8" onclick="hapus(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')"><span class="fe fe-trash-2 fs-14"></span></a>
                        ';
                    } else {
                        $button .= '-';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'ket'])->make(true);
        }
    }

    public function proses_tambah(Request $request)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->jenisbarang)));

        // Sesuaikan dengan input dari view (biasanya name="keterangan" di form)
        JenisBarangModel::create([
            'jenisbarang_nama' => $request->jenisbarang,
            'jenisbarang_slug' => $slug,
            'jenisbarang_keterangan' => $request->keterangan // Pastikan input view mengirim 'keterangan'
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_ubah(Request $request, $id)
    {
        $jenisbarang = JenisBarangModel::find($id);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->jenisbarang)));

        $jenisbarang->update([
            'jenisbarang_nama' => $request->jenisbarang,
            'jenisbarang_slug' => $slug,
            'jenisbarang_keterangan' => $request->keterangan 
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_hapus(Request $request, $id)
    {
        try {
            $jenisbarang = JenisBarangModel::find($id);
            if (!$jenisbarang) {
                return response()->json(['error' => 'Data tidak ditemukan!'], 404);
            }

            // Cek relasi ke Master Barang
            $cekBarang = BarangModel::where('jenisbarang_id', $id)->count();
            if ($cekBarang > 0) {
                return response()->json(['error' => 'Data tidak bisa dihapus karena sudah digunakan pada Master Barang!'], 400);
            }

            $jenisbarang->delete();

            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}