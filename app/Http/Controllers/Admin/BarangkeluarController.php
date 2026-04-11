<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
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
        $data["hakTambah"] = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Barang Keluar', 'tbl_akses.akses_type' => 'create'))->count();
        $data["pegawai"] = UserModel::orderBy('user_nmlengkap', 'ASC')->get();
        return view('Admin.BarangKeluar.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')->orderBy('bk_id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    $tgl = $row->bk_tanggal == '' ? '-' : Carbon::parse($row->bk_tanggal)->translatedFormat('d F Y');

                    return $tgl;
                })
                ->addColumn('tujuan', function ($row) {
                    $tujuan = $row->bk_tujuan == '' ? '-' : $row->bk_tujuan;

                    return $tujuan;
                })
                ->addColumn('barang', function ($row) {
                    $barang = $row->barang_id == '' ? '-' : $row->barang_nama;

                    return $barang;
                })
                ->addColumn('tipe', function ($row) {
                    $tipe = $row->jenisbarang_ket == '' ? '-' : $row->jenisbarang_ket;

                    return $tipe;
                })
                ->addColumn('status', function ($row) {
                    if ($row->bk_status == 'Dipinjam') {
                        $status = '<span class="badge bg-warning">Dipinjam</span>';
                    } else {
                        $status = '<span class="badge bg-success">Selesai</span>';
                    }

                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $array = array(
                        "bk_id" => $row->bk_id,
                        "bk_kode" => $row->bk_kode,
                        "barang_kode" => $row->barang_kode,
                        "barang_nama" => $row->barang_nama,
                        "bk_tanggal" => $row->bk_tanggal,
                        "bk_tujuan" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->bk_tujuan)),
                        "bk_jumlah" => $row->bk_jumlah,
                        "tipe_barang" => $row->jenisbarang_ket,
                        "bk_status" => $row->bk_status,
                        "serial_number" => $row->serial_number,
                        "teknisi" => $row->teknisi,
                        "keterangan" => $row->keterangan,
                        "jam_keluar" => $row->jam_keluar
                    );
                    $button = '';
                    $hakEdit = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Barang Keluar', 'tbl_akses.akses_type' => 'update'))->count();
                    $hakDelete = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Barang Keluar', 'tbl_akses.akses_type' => 'delete'))->count();
                    
                    if ($row->bk_status == 'Dipinjam' && $row->jenisbarang_ket == 'Barang Kembali') {
                        $button .= '
                        <div class="g-2">
                        <a class="btn modal-effect text-info btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Kmodaldemo8" data-bs-toggle="tooltip" data-bs-original-title="Kembalikan" onclick=kembali(' . json_encode($array) . ')><span class="fe fe-corner-up-left fs-14"></span></a>
                        </div>
                        ';
                    }

                    if ($hakEdit > 0 && $hakDelete > 0) {
                        $button .= '
                        <div class="g-2">
                        <a class="btn modal-effect text-primary btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Umodaldemo8" data-bs-toggle="tooltip" data-bs-original-title="Edit" onclick=update(' . json_encode($array) . ')><span class="fe fe-edit text-success fs-14"></span></a>
                        <a class="btn modal-effect text-danger btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Hmodaldemo8" onclick=hapus(' . json_encode($array) . ')><span class="fe fe-trash-2 fs-14"></span></a>
                        </div>
                        ';
                    } else if ($hakEdit > 0 && $hakDelete == 0) {
                        $button .= '
                        <div class="g-2">
                            <a class="btn modal-effect text-primary btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Umodaldemo8" data-bs-toggle="tooltip" data-bs-original-title="Edit" onclick=update(' . json_encode($array) . ')><span class="fe fe-edit text-success fs-14"></span></a>
                        </div>
                        ';
                    } else if ($hakEdit == 0 && $hakDelete > 0) {
                        $button .= '
                        <div class="g-2">
                        <a class="btn modal-effect text-danger btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Hmodaldemo8" onclick=hapus(' . json_encode($array) . ')><span class="fe fe-trash-2 fs-14"></span></a>
                        </div>
                        ';
                    } else {
                        if($button == '') $button .= '-';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'tgl', 'tujuan', 'barang', 'status', 'tipe', 'teknisi'])->make(true);
        }
    }

    public function proses_tambah(Request $request)
    {
        $barang = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->where('barang_kode', $request->barang)
            ->first();
        $status = 'Dipinjam';
        if ($barang->jenisbarang_ket == 'Barang Habis Pakai') {
            $status = 'Selesai';
        }

        //insert data
        BarangkeluarModel::create([
            'bk_tanggal' => $request->tglkeluar,
            'bk_kode' => $request->bkkode,
            'barang_kode' => $request->barang,
            'bk_tujuan'   => $request->tujuan,
            'bk_jumlah'   => $request->jml,
            'bk_status'   => $status,
            'serial_number' => $request->serial_number,
            'teknisi' => $request->teknisi,
            'keterangan' => $request->keterangan,
            'jam_keluar' => now(),
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_kembali(Request $request, BarangkeluarModel $barangkeluar)
    {
        $barangkeluar->update([
            'bk_status' => 'Selesai',
            'bk_tgl_kembali' => $request->tglkembali,
            'bk_kondisi_kembali' => $request->kondisi,
            'bk_jumlah_kembali' => $request->jml,
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_ubah(Request $request, BarangkeluarModel $barangkeluar)
    {
        //update data
        $barangkeluar->update([
            'bk_tanggal' => $request->tglkeluar,
            'bk_kode' => $request->bkkode,
            'barang_kode' => $request->barang,
            'bk_tujuan'   => $request->tujuan,
            'bk_jumlah'   => $request->jml,
            'serial_number' => $request->serial_number,
            'teknisi' => $request->teknisi,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_hapus(Request $request, BarangkeluarModel $barangkeluar)
    {
        //delete
        $barangkeluar->delete();

        return response()->json(['success' => 'Berhasil']);
    }

}
