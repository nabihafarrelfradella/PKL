<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\MerkModel;
use App\Models\Admin\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class MerkController extends Controller
{
    public function index()
    {
        $data["title"] = "Merk";
        $data["hakTambah"] = (Session::get('user')->role_id == 1 || Session::get('user')->role_id == 2) ? 1 : 0;
        return view('Admin.Merk.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = MerkModel::orderBy('merk_id', 'DESC')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('ket', function ($row) {
                    $ket = $row->merk_keterangan == '' ? '-' : $row->merk_keterangan;

                    return $ket;
                })
                ->addColumn('action', function ($row) {
                    $array = array(
                        "merk_id" => $row->merk_id,
                        "merk_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->merk_nama)),
                        "merk_keterangan" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->merk_keterangan))
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
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->merk)));

        //insert data
        MerkModel::create([
            'merk_nama' => $request->merk,
            'merk_slug' => $slug,
            'merk_keterangan'   => $request->ket,
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_ubah(Request $request, MerkModel $merk)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->merk)));

        //update data
        $merk->update([
            'merk_nama' => $request->merk,
            'merk_slug' => $slug,
            'merk_keterangan'  => $request->ket,
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    
    public function proses_hapus(Request $request, $id)
    {
        try {
            $merk = MerkModel::find($id);
            if (!$merk) {
                return response()->json(['error' => 'Data tidak ditemukan!'], 404);
            }

            // Check if there are any items with this brand
            $cekBarang = BarangModel::where('merk_id', $merk->merk_id)->count();
            if ($cekBarang > 0) {
                return response()->json(['error' => 'Data tidak bisa dihapus karena sudah digunakan pada Master Barang!'], 400);
            }

            //delete
            $merk->delete();

            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

}
