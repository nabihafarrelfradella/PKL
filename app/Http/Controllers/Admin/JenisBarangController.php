<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\JenisBarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class JenisBarangController extends Controller
{
    public function index()
    {
        $data["title"] = "Jenis";
        $data["hakTambah"] = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Jenis', 'tbl_akses.akses_type' => 'create'))->count();
        return view('Admin.JenisBarang.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = JenisBarangModel::orderBy('jenisbarang_id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('ket', function ($row) {
                    $ket = $row->jenisbarang_ket == '' ? '-' : $row->jenisbarang_ket;

                    return $ket;
                })
                ->addColumn('action', function ($row) {
                    $array = array(
                        "jenisbarang_id" => $row->jenisbarang_id,
                        "jenisbarang_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->jenisbarang_nama)),
                        "jenisbarang_ket" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->jenisbarang_ket)),
                    );
                    $button = '';
                    $hakEdit = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Jenis', 'tbl_akses.akses_type' => 'update'))->count();
                    $hakDelete = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Jenis', 'tbl_akses.akses_type' => 'delete'))->count();
                    if ($hakEdit > 0 && $hakDelete > 0) {
                        $button .= '
                        <div class="g-2">
                        <a class="btn modal-effect text-primary btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Umodaldemo8" data-bs-toggle="tooltip" data-bs-original-title="Edit" onclick="update(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')"><span class="fe fe-edit text-success fs-14"></span></a>
                        <a class="btn modal-effect text-danger btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Hmodaldemo8" onclick="hapus(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')"><span class="fe fe-trash-2 fs-14"></span></a>
                        </div>
                        ';
                    } else if ($hakEdit > 0 && $hakDelete == 0) {
                        $button .= '
                        <div class="g-2">
                            <a class="btn modal-effect text-primary btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Umodaldemo8" data-bs-toggle="tooltip" data-bs-original-title="Edit" onclick="update(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')"><span class="fe fe-edit text-success fs-14"></span></a>
                        </div>
                        ';
                    } else if ($hakEdit == 0 && $hakDelete > 0) {
                        $button .= '
                        <div class="g-2">
                        <a class="btn modal-effect text-danger btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Hmodaldemo8" onclick="hapus(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')"><span class="fe fe-trash-2 fs-14"></span></a>
                        </div>
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

        //create
        JenisBarangModel::create([
            'jenisbarang_nama' => $request->jenisbarang,
            'jenisbarang_slug'   => $slug,
            'jenisbarang_ket' => $request->ket
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_ubah(Request $request, JenisBarangModel $jenisbarang)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->jenisbarang)));

        //update
        $jenisbarang->update([
            'jenisbarang_nama' => $request->jenisbarang,
            'jenisbarang_slug'   => $slug,
            'jenisbarang_ket' => $request->ket
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

            // Check if there are any items with this type
            $cekBarang = BarangModel::where('jenisbarang_id', $jenisbarang->jenisbarang_id)->count();
            if ($cekBarang > 0) {
                return response()->json(['error' => 'Data tidak bisa dihapus karena sudah digunakan pada Master Barang!'], 400);
            }

            //delete
            $jenisbarang->delete();

            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

}
