<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangmasukModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class BarangmasukController extends Controller
{
    public function index()
    {
        $data["title"] = "Barang Masuk";
        // Menggunakan session user secara aman
        $user = Session::get('user');
        $data["hakTambah"] = ($user && ($user->role_id == 1 || $user->role_id == 2)) ? 1 : 0;
        return view('Admin.BarangMasuk.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            // Gunakan select untuk menghindari tabrakan kolom ID antara tabel barang dan barang masuk
            $data = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                ->select('tbl_barangmasuk.*', 'tbl_barang.barang_nama', 'tbl_barang.barang_id as id_barang_master')
                ->orderBy('tbl_barangmasuk.bm_id', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    return $row->bm_tanggal ? Carbon::parse($row->bm_tanggal)->translatedFormat('d F Y H:i:s') : '-';
                })
                ->addColumn('barang', function ($row) {
                    return $row->barang_nama ?? '-';
                })
                ->addColumn('action', function ($row) {
                    // Data untuk dikirim ke modal JavaScript
                    $array = [
                        "bm_id" => $row->bm_id,
                        "bm_kode" => $row->bm_kode,
                        "barang_kode" => $row->barang_kode,
                        "bm_tanggal" => $row->bm_tanggal,
                        "bm_jumlah" => $row->bm_jumlah,
                        "serial_number" => $row->serial_number,
                        "kode_barang_unik" => $row->kode_barang_unik
                    ];
                    
                    $json_data = htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8');
                    
                    $user = Session::get('user');
                    $roleId = $user ? $user->role_id : 0;
                    $isAuthorized = ($roleId == 1 || $roleId == 2);

                    $button = '<a class="btn modal-effect text-info btn-sm" data-bs-toggle="modal" href="#Qmodaldemo8" title="Print QR" onclick=\'showQR(' . $json_data . ')\'><span class="fe fe-printer fs-14"></span></a>';

                    if ($isAuthorized) {
                        $button .= '
                            <a class="btn modal-effect text-primary btn-sm" data-bs-toggle="modal" href="#Umodaldemo8" title="Edit" onclick=\'update(' . $json_data . ')\'><span class="fe fe-edit text-success fs-14"></span></a>
                            <a class="btn modal-effect text-danger btn-sm" data-bs-toggle="modal" href="#Hmodaldemo8" title="Hapus" onclick=\'hapus(' . $json_data . ')\'><span class="fe fe-trash-2 fs-14"></span></a>';
                    }

                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function proses_tambah(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi sederhana (opsional tapi disarankan)
            if(!$request->barang || !$request->jml) {
                return response()->json(['error' => 'Data tidak lengkap'], 400);
            }

            // Generate kode unik
            $timestamp = now()->timestamp;
            // Gunakan whereDate pada created_at agar urutan reset setiap hari
            $countToday = BarangmasukModel::whereDate('created_at', Carbon::today())->count();
            $urutan = str_pad($countToday + 1, 2, '0', STR_PAD_LEFT);
            $kode_barang_unik = 'BRG-' . $timestamp . '-' . $urutan;

            BarangmasukModel::create([
                'bm_tanggal'       => $request->tglmasuk ?? now(),
                'bm_kode'          => $request->bmkode,
                'barang_kode'      => $request->barang,
                'bm_jumlah'        => $request->jml,
                'serial_number'    => $request->serial_number,
                'kode_barang_unik' => $kode_barang_unik,
                'jam_masuk'        => now(),
                'customer_id'      => 0 // Sesuaikan jika ada input customer
            ]);

            DB::commit();
            return response()->json(['success' => 'Berhasil']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function proses_ubah(Request $request)
    {
        try {
            // Mencari data berdasarkan bm_id yang dikirim dari form hidden input
            $barangmasuk = BarangmasukModel::findOrFail($request->bm_id);
            
            $barangmasuk->update([
                'bm_tanggal'    => $request->tglmasuk,
                'barang_kode'   => $request->barang,
                'bm_jumlah'     => $request->jml,
                'serial_number' => $request->serial_number,
            ]);

            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function proses_hapus(Request $request)
    {
        try {
            // Ambil ID dari request untuk keamanan
            $barangmasuk = BarangmasukModel::findOrFail($request->bm_id);
            $barangmasuk->delete();

            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}