<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class BarangmasukController extends Controller
{
    public function index()
    {
        $data["title"] = "Barang Masuk";
        $user = Session::get('user');
        $data["hakTambah"] = ($user && in_array($user->role_id, [1, 2])) ? 1 : 0;
        return view('Admin.BarangMasuk.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                ->select('tbl_barangmasuk.*', 'tbl_barang.barang_nama')
                ->orderBy('bm_id', 'DESC')
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
                    $array = [
                        "bm_id"            => $row->bm_id,
                        "bm_kode"          => $row->bm_kode,
                        "barang_kode"      => $row->barang_kode,
                        "barang_nama"      => $row->barang_nama,
                        "bm_tanggal"       => $row->bm_tanggal,
                        "bm_jumlah"        => $row->bm_jumlah,
                        "serial_number"    => $row->serial_number,
                        "kode_barang_unik" => $row->kode_barang_unik,
                    ];
                    
                    $user = Session::get('user');
                    $roleId = $user->role_id ?? 0;
                    $json = htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8');
                    $button = '';

                    // Tombol Print QR
                    $button .= '<a class="btn modal-effect text-info btn-sm" data-bs-toggle="modal" href="#Qmodaldemo8" onclick="showQR(' . $json . ')"><span class="fe fe-printer fs-14"></span></a>';

                    if (in_array($roleId, [1, 2])) {
                        $button .= '<a class="btn modal-effect text-primary btn-sm" data-bs-toggle="modal" href="#Umodaldemo8" onclick="update(' . $json . ')"><span class="fe fe-edit text-success fs-14"></span></a>';
                        $button .= '<a class="btn modal-effect text-danger btn-sm" data-bs-toggle="modal" href="#Hmodaldemo8" onclick="hapus(' . $json . ')"><span class="fe fe-trash-2 fs-14"></span></a>';
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
            // 1. Validasi Sederhana
            if (!$request->barang || !$request->jml) {
                return response()->json(['error' => 'Barang dan Jumlah tidak boleh kosong!'], 400);
            }

            $jml = intval($request->jml);
            $barang = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->where('tbl_barang.barang_kode', $request->barang)
                ->first();
            
            if (!$barang) {
                return response()->json(['error' => 'Data Barang tidak ditemukan!'], 404);
            }

            // 2. Prefix SN dari kode barang (BK / HP)
            $prefix_sn = strtoupper(substr($barang->barang_kode, 0, 2));
            $date_now  = now()->format('Ymd');

            // 3. Looping Simpan Berdasarkan Jumlah (Setiap baris punya Serial Number unik)
            for ($i = 1; $i <= $jml; $i++) {
                // Generate Kode Barang Masuk Unik (BM-MMYY-001)
                $monthYear = now()->format('my');
                $lastBM = BarangmasukModel::where('bm_kode', 'LIKE', 'BM-' . $monthYear . '-%')
                    ->orderBy('bm_kode', 'DESC')
                    ->first();

                if ($lastBM) {
                    $lastNo = intval(substr($lastBM->bm_kode, -3));
                    $nextNo = str_pad($lastNo + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    $nextNo = '001';
                }
                $bm_kode = "BM-{$monthYear}-{$nextNo}";

                $loop_index   = str_pad($i, 2, '0', STR_PAD_LEFT);
                $random_code  = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
                $serial_number = "{$prefix_sn}-{$date_now}-{$random_code}-{$loop_index}";

                // Generate Kode Barang Unik (Timestamp + Urutan)
                $kode_barang_unik = 'BRG-' . now()->timestamp . '-' . $loop_index;

                BarangmasukModel::create([
                    'bm_tanggal'       => $request->tglmasuk,
                    'bm_kode'          => $bm_kode,
                    'barang_kode'      => $request->barang,
                    'bm_jumlah'        => 1, // Setiap baris adalah 1 unit
                    'serial_number'    => $serial_number,
                    'kode_barang_unik' => $kode_barang_unik,
                    'jam_masuk'        => now(),
                    'customer_id'      => $request->customer_id ?? 0,
                ]);
            }

            return response()->json(['success' => "Berhasil menyimpan {$jml} data barang masuk."]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal simpan: ' . $e->getMessage()], 500);
        }
    }

    public function proses_ubah(Request $request, $id)
    {
        try {
            $barangmasuk = BarangmasukModel::findOrFail($id);
            $barangmasuk->update([
                'bm_tanggal'    => $request->tglmasuk,
                'bm_kode'       => $request->bmkode,
                'barang_kode'   => $request->barang,
                'bm_jumlah'     => $request->jml,
                'serial_number' => $request->serial_number,
                // kode_barang_unik & jam_masuk biasanya tidak diubah saat edit
            ]);

            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function proses_hapus($id)
    {
        try {
            $barangmasuk = BarangmasukModel::findOrFail($id);
            $barangmasuk->delete();
            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}