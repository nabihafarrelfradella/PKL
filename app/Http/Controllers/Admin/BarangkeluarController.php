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

    public function getTeknisiBySn($sn)
    {
        $user = UserModel::where('teknisi_sn', $sn)->where('role_id', 3)->first();
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

                    $teknisiUser = UserModel::where('teknisi_sn', $row->teknisi)->first();
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
                        "teknisi_nama"  => $teknisiUser ? $teknisiUser->user_nmlengkap : ($row->teknisi_nama ?? ''),
                        "keterangan"    => $row->keterangan,
                        "created_at"    => $row->jam_keluar ? Carbon::parse($row->jam_keluar)->format('Y-m-d H:i:s') : ($row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d H:i:s') : null),
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
                ->addColumn('teknisi', function ($row) {
                    $teknisiUser = UserModel::where('teknisi_sn', $row->teknisi)->first();
                    if ($teknisiUser) {
                        $genderText = $teknisiUser->jenis_kelamin == 'M' ? 'Laki-laki' : ($teknisiUser->jenis_kelamin == 'F' ? 'Perempuan' : '-');
                        
                        $popoverContent = '<div style="font-size: 11px; min-width: 180px;">' .
                                          '<div class="d-flex justify-content-between mb-1" style="border-bottom: 1px solid #f1f1f9; padding-bottom: 4px;">' .
                                          '<span class="text-muted" style="font-weight: 500;">Nama Lengkap:</span>' .
                                          '<span class="text-dark fw-semibold text-end">' . htmlspecialchars($teknisiUser->user_nmlengkap) . '</span>' .
                                          '</div>' .
                                          '<div class="d-flex justify-content-between mb-1" style="border-bottom: 1px solid #f1f1f9; padding-bottom: 4px;">' .
                                          '<span class="text-muted" style="font-weight: 500;">Gender:</span>' .
                                          '<span class="text-dark fw-semibold text-end">' . $genderText . '</span>' .
                                          '</div>' .
                                          '<div class="d-flex justify-content-between align-items-center">' .
                                          '<span class="text-muted" style="font-weight: 500;">ID Teknisi:</span>' .
                                          '<span class="badge bg-primary-light text-primary fw-bold fs-10" style="letter-spacing: 0.5px;">' . htmlspecialchars($row->teknisi) . '</span>' .
                                          '</div>' .
                                          '</div>';
                        
                        $popoverTitle = '<span class="text-primary fw-bold fs-13"><i class="fe fe-user me-1"></i>Detail Teknisi</span>';
                        
                        return htmlspecialchars($teknisiUser->user_nmlengkap) . ' ' .
                            '<span class="text-primary ms-1" style="cursor: pointer;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true" data-bs-placement="top" ' .
                            'title="' . htmlspecialchars($popoverTitle, ENT_QUOTES, 'UTF-8') . '" ' .
                            'data-bs-content="' . htmlspecialchars($popoverContent, ENT_QUOTES, 'UTF-8') . '">' .
                            '<i class="fe fe-info fs-13"></i>' .
                            '</span>';
                    }
                    
                    if ($row->teknisi_nama) {
                        $popoverContent = '<div style="font-size: 11px; min-width: 180px;">' .
                                          '<div class="d-flex justify-content-between mb-1" style="border-bottom: 1px solid #f1f1f9; padding-bottom: 4px;">' .
                                          '<span class="text-muted" style="font-weight: 500;">Nama Lengkap:</span>' .
                                          '<span class="text-dark fw-semibold text-end">' . htmlspecialchars($row->teknisi_nama) . '</span>' .
                                          '</div>' .
                                          '<div class="d-flex justify-content-between mb-1" style="border-bottom: 1px solid #f1f1f9; padding-bottom: 4px;">' .
                                          '<span class="text-muted" style="font-weight: 500;">Status:</span>' .
                                          '<span class="text-danger fw-semibold text-end">Akun Dihapus</span>' .
                                          '</div>' .
                                          '<div class="d-flex justify-content-between align-items-center">' .
                                          '<span class="text-muted" style="font-weight: 500;">ID Teknisi:</span>' .
                                          '<span class="badge bg-secondary text-white fw-bold fs-10" style="letter-spacing: 0.5px;">' . htmlspecialchars($row->teknisi) . '</span>' .
                                          '</div>' .
                                          '</div>';
                                          
                        $popoverTitle = '<span class="text-muted fw-bold fs-13"><i class="fe fe-user me-1"></i>Detail Teknisi (Dihapus)</span>';
                        
                        return htmlspecialchars($row->teknisi_nama) . ' ' .
                            '<span class="text-muted ms-1" style="cursor: pointer;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true" data-bs-placement="top" ' .
                            'title="' . htmlspecialchars($popoverTitle, ENT_QUOTES, 'UTF-8') . '" ' .
                            'data-bs-content="' . htmlspecialchars($popoverContent, ENT_QUOTES, 'UTF-8') . '">' .
                            '<i class="fe fe-info fs-13"></i>' .
                            '</span>';
                    }
                    
                    return $row->teknisi ? htmlspecialchars($row->teknisi) : '-';
                })
                ->rawColumns(['action', 'tgl', 'status', 'teknisi'])
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

            // Validasi stok barang
            $jmlmasuk = \App\Models\Admin\BarangmasukModel::where('barang_kode', $request->barang)
                ->sum('bm_jumlah');

            $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->where('tbl_barangkeluar.barang_kode', '=', $request->barang);

            $jmlkeluar = (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                       + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                           ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                           ->sum('tbl_barangkeluar.bk_jumlah');

            $current_stok = intval($barang->barang_stok) + ($jmlmasuk - $jmlkeluar);

            if ($current_stok <= 0) {
                return response()->json(['error' => 'Stok barang ini sudah habis (0)!'], 400);
            }

            if ($jml > $current_stok) {
                return response()->json(['error' => "Jumlah keluar ({$jml}) melebihi stok yang tersedia ({$current_stok})!"], 400);
            }

            // Parse serial_number from request (can be array or string)
            $sns = $request->serial_number;
            if (is_string($sns)) {
                $sns = array_filter(explode(',', $sns));
            }
            if (!is_array($sns)) {
                $sns = [];
            }
            $sns = array_map('trim', $sns);
            $sns = array_filter($sns);

            // Validasi ketersediaan Serial Number (SN)
            foreach ($sns as $sn) {
                if ($sn && $sn !== '-') {
                    // 1. Cek apakah SN tersebut sedang dipinjam (bk_status = 'Dipinjam')
                    $isBorrowed = BarangkeluarModel::where('serial_number', $sn)
                        ->where('bk_status', 'Dipinjam')
                        ->exists();
                    if ($isBorrowed) {
                        return response()->json(['error' => "Serial Number {$sn} sedang dipinjam dan belum dikembalikan!"], 400);
                    }

                    // 2. Cek apakah SN tersebut adalah barang habis pakai yang sudah pernah digunakan
                    $isConsumed = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                        ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                        ->where('tbl_barangkeluar.serial_number', $sn)
                        ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                        ->exists();
                    if ($isConsumed) {
                        return response()->json(['error' => "Serial Number {$sn} adalah barang habis pakai yang sudah digunakan!"], 400);
                    }
                }
            }

            // Status otomatis: Habis Pakai = Selesai, Kembali = Dipinjam
            $status = (str_contains(strtolower($barang->jenisbarang_nama ?? ''), 'habis')) ? 'Selesai' : 'Dipinjam';

            // Tentukan teknisi: jika login sebagai teknisi, pakai dari session
            $user      = Session::get('user');
            $roleId    = $user->role_id ?? 0;
            $teknisiSN = ($roleId == 3) ? ($user->teknisi_sn ?? '') : $request->teknisi;
            $teknisiNm = ($roleId == 3) ? $user->user_nmlengkap : ($request->tujuan ?? '');

            // Customer/lokasi: dari request
            $customer  = $request->customer ?? $request->tujuan ?? '';

            // Gunakan datetime dari form (waktu device lokal), fallback ke now() jika kosong
            $tglkeluar = $request->tglkeluar
                ? \Carbon\Carbon::parse($request->tglkeluar)
                : now();

            $bk_id_for_notif = null;

            if (!empty($sns)) {
                foreach ($sns as $sn) {
                    // Generate kode BK-MMYY-001 baru untuk setiap SN (karena bk_kode unique per baris)
                    $monthYear = now()->format('my');
                    $lastBK    = BarangkeluarModel::where('bk_kode', 'LIKE', 'BK-' . $monthYear . '-%')
                        ->orderBy('bk_kode', 'DESC')->first();
                    $nextNo    = $lastBK ? str_pad(intval(substr($lastBK->bk_kode, -3)) + 1, 3, '0', STR_PAD_LEFT) : '001';
                    $bk_kode   = "BK-{$monthYear}-{$nextNo}";

                    $bmRow = \App\Models\Admin\BarangmasukModel::where('barang_kode', $request->barang)
                        ->where('serial_number', $sn)
                        ->first();
                    $kbu = $bmRow ? $bmRow->kode_barang_unik : null;

                    $bk = BarangkeluarModel::create([
                        'bk_kode'          => $bk_kode,
                        'barang_kode'      => $request->barang,
                        'kode_barang_unik' => $kbu,
                        'bk_tanggal'       => $tglkeluar->toDateString(),
                        'bk_tujuan'        => $customer,    // nama customer / lokasi
                        'bk_jumlah'        => 1,
                        'bk_status'        => $status,
                        'serial_number'    => $sn,
                        'teknisi'          => $teknisiSN,
                        'teknisi_nama'     => $teknisiNm,
                        'keterangan'       => $request->keterangan,
                        'jam_keluar'       => $tglkeluar,
                    ]);

                    if (!$bk_id_for_notif) {
                        $bk_id_for_notif = $bk->bk_id;
                    }
                }
            } else {
                // Barang tanpa SN: generate kode sekali, simpan satu baris dengan jumlah total
                $monthYear = now()->format('my');
                $lastBK    = BarangkeluarModel::where('bk_kode', 'LIKE', 'BK-' . $monthYear . '-%')
                    ->orderBy('bk_kode', 'DESC')->first();
                $nextNo    = $lastBK ? str_pad(intval(substr($lastBK->bk_kode, -3)) + 1, 3, '0', STR_PAD_LEFT) : '001';
                $bk_kode   = "BK-{$monthYear}-{$nextNo}";

                $bk = BarangkeluarModel::create([
                    'bk_kode'          => $bk_kode,
                    'barang_kode'      => $request->barang,
                    'kode_barang_unik' => null,
                    'bk_tanggal'       => $tglkeluar->toDateString(),
                    'bk_tujuan'        => $customer,    // nama customer / lokasi
                    'bk_jumlah'        => $jml,
                    'bk_status'        => $status,
                    'serial_number'    => '-',
                    'teknisi'          => $teknisiSN,
                    'teknisi_nama'     => $teknisiNm,
                    'keterangan'       => $request->keterangan,
                    'jam_keluar'       => $tglkeluar,
                ]);
                $bk_id_for_notif = $bk->bk_id;
            }

            // Kirim notifikasi ke Owner & Admin Gudang
            if ($roleId == 3) {
                $pesan = $status === 'Dipinjam'
                    ? "{$user->user_nmlengkap} meminjam {$jml} {$barang->barang_nama} untuk customer: {$customer}"
                    : "{$user->user_nmlengkap} mengambil {$jml} {$barang->barang_nama} (habis pakai) untuk customer: {$customer}";

                NotifikasiModel::create([
                    'notif_type'        => ($status === 'Dipinjam') ? 'peminjaman' : 'habis_pakai',
                    'notif_pesan'       => $pesan,
                    'notif_dari'        => $user->user_id,
                    'notif_nama_teknisi'=> $user->user_nmlengkap,
                    'notif_barang'      => $barang->barang_nama,
                    'notif_customer'    => $customer,
                    'bk_id'             => $bk_id_for_notif,
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
        $nmTeknisi = $teknisi ? $teknisi->user_nmlengkap : ($bk->teknisi_nama ?? $bk->teknisi ?? 'Teknisi');

        NotifikasiModel::create([
            'notif_type'        => 'pengembalian',
            'notif_pesan'       => "Barang {$bk->barang_nama} dari {$nmTeknisi} sudah dikembalikan. Kondisi: {$request->kondisi}",
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

            // Validasi stok barang (kecuali transaksi ini sendiri)
            $jmlmasuk = \App\Models\Admin\BarangmasukModel::where('barang_kode', $request->barang)
                ->sum('bm_jumlah');

            $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->where('tbl_barangkeluar.barang_kode', '=', $request->barang)
                ->where('tbl_barangkeluar.bk_id', '!=', $id);

            $jmlkeluar = (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                       + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                           ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                           ->sum('tbl_barangkeluar.bk_jumlah');

            $current_stok = intval($barang->barang_stok) + ($jmlmasuk - $jmlkeluar);

            if ($current_stok <= 0) {
                return response()->json(['error' => 'Stok barang ini sudah habis (0)!'], 400);
            }

            if ($jml > $current_stok) {
                return response()->json(['error' => "Jumlah keluar ({$jml}) melebihi stok yang tersedia ({$current_stok})!"], 400);
            }

            // Validasi ketersediaan Serial Number (SN) saat ubah
            $sn = trim($request->serial_number);
            if ($sn && $sn !== '-') {
                $isBorrowed = BarangkeluarModel::where('serial_number', $sn)
                    ->where('bk_status', 'Dipinjam')
                    ->where('bk_id', '!=', $id)
                    ->exists();
                if ($isBorrowed) {
                    return response()->json(['error' => "Serial Number {$sn} sedang dipinjam dan belum dikembalikan!"], 400);
                }

                $isConsumed = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                    ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                    ->where('tbl_barangkeluar.serial_number', $sn)
                    ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                    ->where('tbl_barangkeluar.bk_id', '!=', $id)
                    ->exists();
                if ($isConsumed) {
                    return response()->json(['error' => "Serial Number {$sn} adalah barang habis pakai yang sudah digunakan!"], 400);
                }
            }

            $tglkeluar = $request->tglkeluar ? Carbon::parse($request->tglkeluar) : now();

            $teknisiUser = UserModel::where('teknisi_sn', $request->teknisi)->first();
            $teknisiNama = $teknisiUser ? $teknisiUser->user_nmlengkap : null;

            $bk->update([
                'bk_kode'       => $request->bkkode,
                'barang_kode'   => $request->barang,
                'bk_tanggal'    => $tglkeluar->toDateString(),
                'bk_tujuan'     => $request->tujuan,
                'bk_jumlah'     => $request->jml,
                'serial_number' => $request->serial_number,
                'teknisi'       => $request->teknisi,
                'teknisi_nama'  => $teknisiNama,
                'keterangan'    => $request->keterangan,
                'jam_keluar'    => $tglkeluar,
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

    public function getAvailableSN($barang_kode)
    {
        // 1. Ambil semua serial number dari tbl_barangmasuk untuk barang_kode ini
        $incomings = \App\Models\Admin\BarangmasukModel::where('barang_kode', $barang_kode)
            ->whereNotNull('serial_number')
            ->where('serial_number', '!=', '')
            ->where('serial_number', '!=', '-')
            ->select('serial_number', 'kode_barang_unik')
            ->get();

        // 2. Cari serial number yang tidak tersedia (sedang dipinjam / habis pakai sudah dipakai)
        $borrowedSNs = BarangkeluarModel::where('barang_kode', $barang_kode)
            ->where('bk_status', 'Dipinjam')
            ->whereNotNull('serial_number')
            ->pluck('serial_number')
            ->toArray();

        $consumedSNs = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->where('tbl_barangkeluar.barang_kode', $barang_kode)
            ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
            ->whereNotNull('tbl_barangkeluar.serial_number')
            ->pluck('tbl_barangkeluar.serial_number')
            ->toArray();

        $unavailableSNs = array_merge($borrowedSNs, $consumedSNs);

        // 3. Filter data ketersediaan
        $available = [];
        foreach ($incomings as $incoming) {
            $sn = $incoming->serial_number;
            if (!in_array($sn, $unavailableSNs)) {
                $available[] = [
                    'serial_number' => $sn,
                    'kode_barang_unik' => $incoming->kode_barang_unik
                ];
            }
        }

        return response()->json($available);
    }
}
