<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangModel;
use App\Models\Admin\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class BarangkeluarController extends Controller
{
    public function index()
    {
        $data["title"] = "Barang Keluar";
        $data["hakTambah"] = (Session::get('user')->role_id == 1 || Session::get('user')->role_id == 2 || Session::get('user')->role_id == 3) ? 1 : 0;
        
        // Hanya ambil user dengan role Teknisi (role_id = 3)
        $data["pegawai"] = UserModel::where('role_id', 3)->orderBy('user_nmlengkap', 'ASC')->get();
        return view('Admin.BarangKeluar.index', $data);
    }

    public function getTeknisi($id)
    {
        // Cari teknisi berdasarkan nama lengkap
        $user = UserModel::where('user_nmlengkap', $id)->where('role_id', 3)->first();
        return response()->json($user);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            // Join ke tbl_barang untuk mengambil barang_nama
            $data = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama')
                ->orderBy('tbl_barangkeluar.bk_id', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    return $row->created_at == '' ? '-' : Carbon::parse($row->created_at)->translatedFormat('d F Y H:i:s');
                })
                ->addColumn('barang', function ($row) {
                    return $row->barang_nama ?? '-';
                })
                ->addColumn('tujuan', function ($row) {
                    return $row->bk_tujuan ?? '-';
                })
                ->addColumn('status', function ($row) {
                    if ($row->bk_status == 'Dipinjam') {
                        return '<span class="badge bg-warning">Dipinjam</span>';
                    }
                    return '<span class="badge bg-success">Selesai</span>';
                })
                ->addColumn('action', function ($row) {
                    // Proteksi string untuk JavaScript onclick agar tidak error jika ada tanda petik
                    $barangNamaClean = str_replace(["'", '"'], "", $row->barang_nama);
                    $tujuanClean = str_replace(["'", '"', "\r", "\n"], "", $row->bk_tujuan);

                    $array = array(
                        "bk_id"         => $row->bk_id,
                        "bk_kode"       => $row->bk_kode,
                        "barang_kode"   => $row->barang_kode,
                        "barang_nama"   => $barangNamaClean,
                        "bk_tanggal"    => Carbon::parse($row->created_at)->format('Y-m-d'),
                        "bk_tujuan"     => $tujuanClean,
                        "bk_jumlah"     => $row->bk_jumlah,
                        "bk_status"     => $row->bk_status,
                        "serial_number" => $row->serial_number,
                        "teknisi"       => $row->teknisi,
                        "keterangan"    => $row->keterangan,
                    );
                    
                    $button = '';
                    $roleId = Session::get('user')->role_id;
                    
                    // Tombol Pengembalian (Hanya jika status Dipinjam)
                    if (($roleId == 1 || $roleId == 2) && $row->bk_status == 'Dipinjam') {
                        $button .= '<a class="btn modal-effect text-info btn-sm" data-bs-toggle="modal" href="#Kmodaldemo8" onclick=\'kembali(' . json_encode($array) . ')\' title="Kembalikan"><span class="fe fe-corner-up-left fs-14"></span></a>';
                    }

                    $button .= '<a class="btn modal-effect text-primary btn-sm" data-bs-toggle="modal" href="#Umodaldemo8" onclick=\'update(' . json_encode($array) . ')\' title="Ubah"><span class="fe fe-edit text-success fs-14"></span></a>';
                    $button .= '<a class="btn modal-effect text-danger btn-sm" data-bs-toggle="modal" href="#Hmodaldemo8" onclick=\'hapus(' . json_encode($array) . ')\' title="Hapus"><span class="fe fe-trash-2 fs-14"></span></a>';
                    
                    return $button;
                })
                ->rawColumns(['action', 'tgl', 'status'])
                ->make(true);
        }
    }

    public function proses_tambah(Request $request)
    {
        try {
            $jml = intval($request->jml);
            if ($jml <= 0) {
                return response()->json(['error' => 'Jumlah keluar harus lebih dari 0'], 400);
            }

            // Ambil data barang untuk menentukan status otomatis
            $barang = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->where('barang_kode', $request->barang)
                ->first();

            if (!$barang) {
                return response()->json(['error' => 'Barang tidak ditemukan'], 404);
            }

            // Tentukan status: Barang Habis Pakai langsung 'Selesai', selain itu 'Dipinjam'
            $status = (str_contains(strtolower($barang->jenisbarang_nama), 'habis')) ? 'Selesai' : 'Dipinjam';

            // Generate Kode Barang Keluar: BK-MMYY-001
            $monthYear = now()->format('my');
            $lastBK = BarangkeluarModel::where('bk_kode', 'LIKE', 'BK-' . $monthYear . '-%')
                ->orderBy('bk_kode', 'DESC')
                ->first();

            if ($lastBK) {
                $lastNo = intval(substr($lastBK->bk_kode, -3));
                $nextNo = str_pad($lastNo + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextNo = '001';
            }
            $bk_kode = "BK-{$monthYear}-{$nextNo}";

            BarangkeluarModel::create([
                'bk_kode'       => $bk_kode,
                'barang_kode'   => $request->barang,
                'kode_barang_unik' => $request->kode_barang_unik, // Tambahkan ini
                'bk_tanggal'    => $request->tglkeluar,
                'bk_tujuan'     => $request->tujuan,
                'bk_jumlah'     => $request->jml,
                'bk_status'     => $status,
                'serial_number' => $request->serial_number, // SN Barang yang dipilih
                'teknisi'       => $request->teknisi,       // SN Teknisi
                'keterangan'    => $request->keterangan,
                'jam_keluar'    => now(), 
            ]);

            return response()->json(['success' => 'Berhasil menyimpan data barang keluar.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal simpan: ' . $e->getMessage()], 500);
        }
    }

    public function proses_kembali(Request $request, $id)
    {
        $barangkeluar = BarangkeluarModel::find($id);

        if ($barangkeluar) {
            $barangkeluar->update([
                'bk_status'          => 'Selesai',
                'bk_tgl_kembali'     => $request->tglkembali,
                'bk_kondisi_kembali' => $request->kondisi, 
                'bk_jumlah_kembali'  => $request->jml,    
            ]);

            return response()->json(['success' => 'Berhasil']);
        }

        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }

    public function proses_ubah(Request $request, $id)
    {
        $barangkeluar = BarangkeluarModel::find($id);
        
        if ($barangkeluar) {
            $barangkeluar->update([
                'bk_kode'       => $request->bkkode,
                'barang_kode'   => $request->barang,
                'bk_tanggal'    => $request->tglkeluar,
                'bk_tujuan'     => $request->tujuan,
                'bk_jumlah'     => $request->jml,
                'serial_number' => $request->serial_number,
                'teknisi'       => $request->teknisi,
                'keterangan'    => $request->keterangan,
            ]);
            return response()->json(['success' => 'Berhasil']);
        }

        return response()->json(['error' => 'Gagal'], 404);
    }

    public function proses_hapus($id)
    {
        $barangkeluar = BarangkeluarModel::find($id);
        if ($barangkeluar) {
            $barangkeluar->delete();
            return response()->json(['success' => 'Berhasil']);
        }
        return response()->json(['error' => 'Gagal'], 404);
    }
}
