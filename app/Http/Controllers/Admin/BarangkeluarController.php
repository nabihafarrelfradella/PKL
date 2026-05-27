<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangModel;
use App\Models\Admin\NotifikasiModel;
use App\Models\Admin\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class BarangkeluarController extends Controller
{
    public function index()
    {
        $data["title"]    = "Barang Keluar";
        $user             = Session::get('user');
        $data["roleId"]   = $user->role_id ?? 0;
        $data["hakTambah"]= ($data["roleId"] == 1 || $data["roleId"] == 2 || $data["roleId"] == 3) ? 1 : 0;

        // Untuk form tambah: daftar teknisi (hanya Owner & Admin yang pakai dropdown)
        $data["pegawai"] = UserModel::where('role_id', 3)->orderBy('user_nmlengkap', 'ASC')->get();

        // Jika teknisi: kirim info diri sendiri
        if ($data["roleId"] == 3) {
            $data["currentUser"] = $user;
        }

        return view('Admin.BarangKeluar.index', $data);
    }

    public function getTeknisi($id)
    {
        $user = UserModel::where('user_nmlengkap', $id)->where('role_id', 3)->first();
        return response()->json($user);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $user   = Session::get('user');
            $roleId = $user->role_id ?? 0;

            $query = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama')
                ->orderBy('tbl_barangkeluar.bk_id', 'DESC');

            // Teknisi hanya lihat punya sendiri (berdasarkan teknisi_sn)
            if ($roleId == 3) {
                $query->where('tbl_barangkeluar.teknisi', $user->teknisi_sn);
            }

            $data = $query->get();

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
                        return '<span class="badge bg-warning text-dark"><i class="fe fe-clock me-1"></i>Dipinjam</span>';
                    }
                    return '<span class="badge bg-success"><i class="fe fe-check me-1"></i>Selesai</span>';
                })
                ->addColumn('action', function ($row) {
                    $roleId         = Session::get('user')->role_id ?? 0;
                    $barangNamaClean = str_replace(["'", '"'], "", $row->barang_nama);
                    $tujuanClean    = str_replace(["'", '"', "\r", "\n"], "", $row->bk_tujuan);

                    $array = [
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
                    ];

                    $button = '';

                    // Tombol Kembalikan: hanya Owner & Admin Gudang, hanya jika status Dipinjam
                    if (($roleId == 1 || $roleId == 2) && $row->bk_status == 'Dipinjam') {
                        $button .= '<a class="btn modal-effect text-info btn-sm" data-bs-toggle="modal" href="#Kmodaldemo8" onclick=\'kembali(' . json_encode($array) . ')\' title="Kembalikan"><span class="fe fe-corner-up-left fs-14"></span></a>';
                    }

                    // Teknisi hanya lihat, tidak bisa edit/hapus setelah submit
                    if ($roleId == 1 || $roleId == 2) {
                        $button .= '<a class="btn modal-effect text-primary btn-sm" data-bs-toggle="modal" href="#Umodaldemo8" onclick=\'update(' . json_encode($array) . ')\' title="Ubah"><span class="fe fe-edit text-success fs-14"></span></a>';
                        $button .= '<a class="btn modal-effect text-danger btn-sm" data-bs-toggle="modal" href="#Hmodaldemo8" onclick=\'hapus(' . json_encode($array) . ')\' title="Hapus"><span class="fe fe-trash-2 fs-14"></span></a>';
                    }

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

            // Ambil data barang
            $barang = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->where('barang_kode', $request->barang)
                ->first();

            if (!$barang) {
                return response()->json(['error' => 'Barang tidak ditemukan'], 404);
            }

            // Status otomatis: Habis Pakai = Selesai, Kembali = Dipinjam
            $status = (str_contains(strtolower($barang->jenisbarang_nama ?? ''), 'habis')) ? 'Selesai' : 'Dipinjam';

            // Generate kode BK-MMYY-001
            $monthYear = now()->format('my');
            $lastBK    = BarangkeluarModel::where('bk_kode', 'LIKE', 'BK-' . $monthYear . '-%')
                ->orderBy('bk_kode', 'DESC')->first();
            $nextNo    = $lastBK ? str_pad(intval(substr($lastBK->bk_kode, -3)) + 1, 3, '0', STR_PAD_LEFT) : '001';
            $bk_kode   = "BK-{$monthYear}-{$nextNo}";

            // Tentukan teknisi: jika login sebagai teknisi, pakai dari session
            $user      = Session::get('user');
            $roleId    = $user->role_id ?? 0;
            $teknisiSN = ($roleId == 3) ? ($user->teknisi_sn ?? '') : $request->teknisi;
            $teknisiNm = ($roleId == 3) ? $user->user_nmlengkap : ($request->tujuan ?? '');

            // Customer/lokasi: dari request
            $customer  = $request->customer ?? $request->tujuan ?? '';

            $bk = BarangkeluarModel::create([
                'bk_kode'          => $bk_kode,
                'barang_kode'      => $request->barang,
                'kode_barang_unik' => $request->kode_barang_unik,
                'bk_tanggal'       => now()->toDateString(),
                'bk_tujuan'        => $customer,    // nama customer / lokasi
                'bk_jumlah'        => $jml,
                'bk_status'        => $status,
                'serial_number'    => $request->serial_number,
                'teknisi'          => $teknisiSN,
                'keterangan'       => $request->keterangan,
                'jam_keluar'       => now(),
            ]);

            // Kirim notifikasi ke Owner & Admin Gudang
            if ($roleId == 3) {
                $pesan = $status === 'Dipinjam'
                    ? "🔧 {$user->user_nmlengkap} meminjam {$jml} {$barang->barang_nama} untuk customer: {$customer}"
                    : "📦 {$user->user_nmlengkap} mengambil {$jml} {$barang->barang_nama} (habis pakai) untuk customer: {$customer}";

                NotifikasiModel::create([
                    'notif_type'        => ($status === 'Dipinjam') ? 'peminjaman' : 'habis_pakai',
                    'notif_pesan'       => $pesan,
                    'notif_dari'        => $user->user_id,
                    'notif_nama_teknisi'=> $user->user_nmlengkap,
                    'notif_barang'      => $barang->barang_nama,
                    'notif_customer'    => $customer,
                    'bk_id'             => $bk->bk_id,
                    'is_read_owner'     => 0,
                    'is_read_gudang'    => 0,
                ]);
            }

            return response()->json(['success' => 'Berhasil menyimpan data barang keluar.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal simpan: ' . $e->getMessage()], 500);
        }
    }

    public function proses_kembali(Request $request, $id)
    {
        $bk = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->where('tbl_barangkeluar.bk_id', $id)
            ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama')
            ->first();

        if (!$bk) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        BarangkeluarModel::find($id)->update([
            'bk_status'          => 'Selesai',
            'bk_tgl_kembali'     => $request->tglkembali,
            'bk_kondisi_kembali' => $request->kondisi,
            'bk_jumlah_kembali'  => $request->jml,
        ]);

        // Notifikasi pengembalian ke Owner & Admin Gudang
        $teknisi = UserModel::where('teknisi_sn', $bk->teknisi)->first();
        $nmTeknisi = $teknisi ? $teknisi->user_nmlengkap : ($bk->teknisi ?? 'Teknisi');

        NotifikasiModel::create([
            'notif_type'        => 'pengembalian',
            'notif_pesan'       => "✅ Barang {$bk->barang_nama} dari {$nmTeknisi} sudah dikembalikan. Kondisi: {$request->kondisi}",
            'notif_dari'        => $teknisi->user_id ?? 0,
            'notif_nama_teknisi'=> $nmTeknisi,
            'notif_barang'      => $bk->barang_nama ?? '-',
            'notif_customer'    => $bk->bk_tujuan,
            'bk_id'             => $id,
            'is_read_owner'     => 0,
            'is_read_gudang'    => 0,
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_ubah(Request $request, $id)
    {
        $bk = BarangkeluarModel::find($id);
        if ($bk) {
            $bk->update([
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
        $bk = BarangkeluarModel::find($id);
        if ($bk) {
            $bk->delete();
            return response()->json(['success' => 'Berhasil']);
        }
        return response()->json(['error' => 'Gagal'], 404);
    }
}
