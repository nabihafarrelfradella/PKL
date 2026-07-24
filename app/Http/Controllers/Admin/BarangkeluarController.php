<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangModel;
use App\Models\Admin\NotifikasiModel;
use App\Models\Admin\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class BarangkeluarController extends Controller
{
    public function index()
    {
        $data["title"]    = "Barang Keluar";
        $user             = Session::get('user');
        $data["roleId"]   = $user->role_id ?? 0;
        $data["hakTambah"]= $this->checkAccess($data["roleId"], '/barang-keluar', 'create');

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

            $query = BarangkeluarModel::leftJoin('tbl_user', 'tbl_user.teknisi_sn', '=', 'tbl_barangkeluar.teknisi')
                ->select(
                    'tbl_barangkeluar.bk_kode',
                    DB::raw('MAX(tbl_barangkeluar.bk_id) as bk_id'),
                    DB::raw('MAX(tbl_barangkeluar.created_at) as created_at'),
                    DB::raw('COUNT(tbl_barangkeluar.bk_id) as total_unit'),
                    DB::raw('MAX(tbl_barangkeluar.bk_tujuan) as bk_tujuan'),
                    DB::raw('MAX(tbl_barangkeluar.bk_lokasi) as bk_lokasi'),
                    DB::raw('MAX(tbl_barangkeluar.bk_lat) as bk_lat'),
                    DB::raw('MAX(tbl_barangkeluar.bk_lng) as bk_lng'),
                    DB::raw('MAX(tbl_barangkeluar.bk_map_url) as bk_map_url'),
                    DB::raw('MAX(tbl_barangkeluar.teknisi) as teknisi'),
                    DB::raw('MAX(tbl_user.user_nmlengkap) as teknisi_nama'),
                    DB::raw('MAX(tbl_user.user_foto) as teknisi_foto'),
                    DB::raw('MAX(tbl_user.user_phone) as teknisi_phone'),
                    DB::raw('MAX(tbl_user.jenis_kelamin) as teknisi_jk'),
                    DB::raw('MAX(tbl_barangkeluar.keterangan) as bk_keterangan'),
                    DB::raw("GROUP_CONCAT(DISTINCT tbl_barangkeluar.bk_status ORDER BY tbl_barangkeluar.bk_id SEPARATOR ', ') as bk_status_list")
                )
                ->groupBy('tbl_barangkeluar.bk_kode')
                ->orderBy('bk_id', 'DESC');

            // Teknisi hanya lihat punya sendiri (berdasarkan teknisi_sn)
            if ($roleId == 3) {
                $query->where('tbl_barangkeluar.teknisi', $user->teknisi_sn);
            }

            $search = $request->input('search.value');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('tbl_barangkeluar.bk_kode', 'LIKE', "%{$search}%")
                      ->orWhere('tbl_barangkeluar.teknisi', 'LIKE', "%{$search}%")
                      ->orWhere('tbl_user.user_nmlengkap', 'LIKE', "%{$search}%")
                      ->orWhere('tbl_barangkeluar.bk_tujuan', 'LIKE', "%{$search}%")
                      ->orWhere('tbl_barangkeluar.bk_lokasi', 'LIKE', "%{$search}%")
                      ->orWhere('tbl_barangkeluar.barang_kode', 'LIKE', "%{$search}%")
                      ->orWhere('tbl_barangkeluar.serial_number', 'LIKE', "%{$search}%")
                      ->orWhere('tbl_barangkeluar.kode_barang_unik', 'LIKE', "%{$search}%")
                      ->orWhereIn('tbl_barangkeluar.barang_kode', function($subQ) use ($search) {
                          $subQ->select('barang_kode')->from('tbl_barang')->where('barang_nama', 'LIKE', "%{$search}%");
                      });
                });
            }

            $data = $query->get();

            // Clear search string from request to prevent DataTables Collection Engine from filtering the collection again and hiding rows where the search string wasn't explicitly returned in the collection.
            $request->merge(['search' => ['value' => '']]);

            return DataTables::of($data)
                ->filter(function ($query) {
                    // prevent default collection filtering so our query filter takes effect exactly
                })
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    return $row->created_at == '' ? '-' : Carbon::parse($row->created_at)->translatedFormat('d F Y H:i:s');
                })
                ->addColumn('teknisi', function ($row) {
                    $teknisi = $row->teknisi_nama ?? '';
                    $teknisiSN = $row->teknisi ?? '';

                    if ($teknisi) {
                        $jk = $row->teknisi_jk ?? '-';
                        
                        $popupHtml = '<div style="min-width: 150px;">' .
                                     '<div class="d-flex justify-content-between mb-1"><span class="text-muted fs-12">Nama Lengkap:</span><span class="fs-12 fw-semibold text-dark text-end ms-2">'.htmlspecialchars($teknisi).'</span></div>' .
                                     '<div class="d-flex justify-content-between mb-1"><span class="text-muted fs-12">Gender:</span><span class="fs-12 fw-semibold text-dark text-end ms-2">'.htmlspecialchars($jk).'</span></div>' .
                                     '<div class="d-flex justify-content-between"><span class="text-muted fs-12">Kode SN:</span><span class="fs-12 fw-bold text-primary text-end ms-2">'.htmlspecialchars($teknisiSN).'</span></div>' .
                                     '</div>';
                        
                        $popoverBtn = '<i tabindex="0" class="fe fe-info text-primary ms-1" style="cursor: pointer; font-size: 11px; position: relative; top: -2px;" role="button" data-bs-toggle="popover" data-bs-trigger="hover focus" title="<i class=\'fe fe-user\'></i> Detail Teknisi" data-bs-html="true" data-bs-content="'.htmlspecialchars($popupHtml).'"></i>';
                        
                        return '<span class="text-dark fw-bold">' . htmlspecialchars($teknisi) . '</span>' . $popoverBtn;
                    }
                    return '-';
                })
                ->addColumn('tujuan', function ($row) {
                    $tujuan = $row->bk_tujuan ?? '';
                    $lokasi = $row->bk_lokasi ?? '';
                    
                    $html = '';
                    if ($tujuan) {
                        $html .= '<span class="text-dark">' . htmlspecialchars($tujuan) . '</span>';
                    } else {
                        $html .= '-';
                    }

                    if ($lokasi && $lokasi != '-') {
                        $shortLokasi = \Illuminate\Support\Str::limit($lokasi, 35);
                        if (!empty($row->bk_map_url)) {
                            $mapUrl = $row->bk_map_url;
                        } else if (!empty($row->bk_lat) && !empty($row->bk_lng)) {
                            $mapUrl = 'https://www.google.com/maps/place/' . $row->bk_lat . ',' . $row->bk_lng;
                        } else {
                            $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($lokasi);
                        }
                        $html .= '<br><a href="' . $mapUrl . '" target="_blank" class="text-primary fs-12 text-decoration-none" data-bs-toggle="tooltip" title="Buka di Google Maps: ' . htmlspecialchars($lokasi) . '"><i class="fe fe-map-pin me-1"></i>' . htmlspecialchars($shortLokasi) . '</a>';
                    }
                    
                    return $html;
                })
                ->addColumn('total_unit', function ($row) {
                    return $row->total_unit ?? 0;
                })
                ->addColumn('keterangan', function ($row) {
                    $ket = $row->bk_keterangan ?? '';
                    return $ket ? '<span class="text-dark">' . htmlspecialchars($ket) . '</span>' : '<span class="text-muted">-</span>';
                })
                ->addColumn('status', function ($row) {
                    // Tampilkan status dari seluruh unit dalam kelompok
                    $statuses = array_unique(explode(', ', $row->bk_status_list ?? ''));
                    $badges = '';
                    foreach ($statuses as $s) {
                        $s = trim($s);
                        if ($s == 'Dipinjam') {
                            $badges .= '<span class="badge bg-warning text-dark me-1"><i class="fe fe-clock me-1"></i>Dipinjam</span>';
                        } elseif ($s == 'Selesai') {
                            $badges .= '<span class="badge bg-success me-1"><i class="fe fe-check me-1"></i>Selesai</span>';
                        } elseif ($s == 'Ditolak') {
                            $badges .= '<span class="badge bg-danger me-1"><i class="fe fe-x me-1"></i>Ditolak</span>';
                        } elseif ($s == 'Menunggu Persetujuan Pinjam') {
                            $badges .= '<span class="badge bg-info me-1"><i class="fe fe-loader me-1"></i>Menunggu Pinjam</span>';
                        } elseif ($s == 'Menunggu Persetujuan Kembali') {
                            $badges .= '<span class="badge bg-info me-1"><i class="fe fe-loader me-1"></i>Menunggu Kembali</span>';
                        } else {
                            $badges .= '<span class="badge bg-secondary me-1">' . htmlspecialchars($s) . '</span>';
                        }
                    }
                    return $badges;
                })
                ->addColumn('expand', function ($row) {
                    return '<button class="btn btn-sm btn-light btn-expand-bk"
                        data-bk-kode="' . htmlspecialchars($row->bk_kode) . '"
                        title="Lihat Detail Barang & SN">
                        <i class="fe fe-chevron-right"></i>
                    </button>';
                })
                ->addColumn('action', function ($row) use ($roleId) {
                    $hakAksesDelete = $this->checkAccess($roleId, '/barang-keluar', 'delete');
                    $bkKode = htmlspecialchars($row->bk_kode, ENT_QUOTES, 'UTF-8');
                    $btnReturn = '<button class="btn btn-success-light btn-sm" onclick="batchKembaliPerBK(\'' . $bkKode . '\')" title="Batch Pengembalian"><i class="fe fe-rotate-ccw"></i></button>';
                    $btnHapus = $hakAksesDelete ? '<button class="btn btn-danger-light btn-sm ms-1" onclick="hapusTransaksi(\'' . $bkKode . '\')" title="Hapus Transaksi"><i class="fe fe-trash-2"></i></button>' : '';
                    return $btnReturn . $btnHapus;
                })
                ->rawColumns(['action', 'tgl', 'status', 'teknisi', 'tujuan', 'keterangan', 'total_unit', 'expand'])
                ->make(true);
        }
    }

    /**
     * Endpoint: Kembalikan semua SN berdasarkan bk_kode
     */
    public function detailSN($bk_kode)
    {
        $user   = Session::get('user');
        $roleId = $user->role_id ?? 0;

        $query = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
            ->leftJoin('tbl_satuan', 'tbl_satuan.satuan_id', '=', 'tbl_barang.satuan_id')
            ->where('tbl_barangkeluar.bk_kode', $bk_kode)
            ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama', 'tbl_merk.merk_nama', 'tbl_barang.satuan_id')
            ->orderBy('bk_id', 'DESC');

        if ($roleId == 3) {
            $query->where('tbl_barangkeluar.teknisi', $user->teknisi_sn);
        }

        $rows = $query->get();

        $batchCount = $rows->count();

        $result = $rows->map(function ($row) use ($roleId, $batchCount) {
            $barangNamaClean = str_replace(["'", '"'], "", $row->barang_nama);
            $tujuanClean     = str_replace(["'", '"', "\r", "\n"], "", $row->bk_tujuan);
            $teknisiUser     = UserModel::where('teknisi_sn', $row->teknisi)->first();

            $array = [
                "bk_id"           => $row->bk_id,
                "bk_kode"         => $row->bk_kode,
                "barang_kode"     => $row->barang_kode,
                "barang_nama"     => $barangNamaClean,
                "bk_tanggal"      => Carbon::parse($row->created_at)->format('Y-m-d'),
                "bk_tujuan"       => $tujuanClean,
                "bk_lokasi"       => $row->bk_lokasi ?? '-',
                "bk_lat"          => $row->bk_lat,
                "bk_lng"          => $row->bk_lng,
                "bk_jumlah"       => $row->bk_jumlah,
                "bk_status"       => $row->bk_status,
                "serial_number"   => $row->serial_number,
                "kode_barang_unik"=> $row->kode_barang_unik,
                "teknisi"         => $row->teknisi,
                "teknisi_nama"    => $teknisiUser ? $teknisiUser->user_nmlengkap : ($row->teknisi_nama ?? ''),
                "keterangan"      => $row->keterangan,
                "batch_count"     => $batchCount,
                "created_at"      => $row->jam_keluar
                    ? Carbon::parse($row->jam_keluar)->format('Y-m-d H:i:s')
                    : ($row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d H:i:s') : null),
            ];

            $json   = htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8');
            $action = '';

            // Approval buttons
            if (in_array($roleId, [1, 2])) {
                if ($row->bk_status == 'Menunggu Persetujuan Pinjam') {
                    $action .= '<a class="btn text-success btn-sm" href="javascript:void(0)" onclick=\'terimaPinjam(' . $row->bk_id . ')\' title="Setujui"><span class="fe fe-check-circle fs-13"></span></a>';
                    $action .= '<a class="btn text-danger btn-sm" href="javascript:void(0)" onclick=\'tolakPinjam(' . $row->bk_id . ')\' title="Tolak"><span class="fe fe-x-circle fs-13"></span></a>';
                } elseif ($row->bk_status == 'Menunggu Persetujuan Kembali') {
                    $action .= '<a class="btn text-success btn-sm" href="javascript:void(0)" onclick=\'terimaKembali(' . $row->bk_id . ')\' title="Setujui"><span class="fe fe-check-circle fs-13"></span></a>';
                    $action .= '<a class="btn text-danger btn-sm" href="javascript:void(0)" onclick=\'tolakKembali(' . $row->bk_id . ')\' title="Tolak"><span class="fe fe-x-circle fs-13"></span></a>';
                }
            }

            // Kembali
            if (in_array($roleId, [1, 2, 3]) && $row->bk_status == 'Dipinjam') {
                $action .= '<a class="btn text-info btn-sm" data-bs-toggle="modal" href="#Kmodaldemo8" onclick="kembali(' . $json . ')" title="Kembalikan"><span class="fe fe-corner-up-left fs-13"></span></a>';
                $action .= '<a class="btn text-warning btn-sm ms-1" href="javascript:void(0)" onclick="batalPinjam(' . $row->bk_id . ')" title="Batal Pinjam (Barang Cadangan)"><span class="fe fe-rotate-ccw fs-13"></span></a>';
            }

            // Edit & Hapus
            $hakEdit = $this->checkAccess($roleId, '/barang-keluar', 'update');
            $hakDelete = $this->checkAccess($roleId, '/barang-keluar', 'delete');
            
            if (!in_array($row->bk_status, ['Menunggu Persetujuan Pinjam', 'Menunggu Persetujuan Kembali'])) {
                if ($hakEdit > 0) {
                    $action .= '<a class="btn text-success btn-sm" data-bs-toggle="modal" href="#Umodaldemo8" onclick="update(' . $json . ')"><span class="fe fe-edit fs-13"></span></a>';
                }
                if ($hakDelete > 0) {
                    $action .= '<a class="btn text-danger btn-sm" data-bs-toggle="modal" href="#Hmodaldemo8" onclick="hapus(' . $json . ')"><span class="fe fe-trash-2 fs-13"></span></a>';
                }
            }

            // Status badge
            $statusBadge = match($row->bk_status) {
                'Dipinjam'                       => '<span class="badge bg-warning text-dark"><i class="fe fe-clock me-1"></i>Dipinjam</span>',
                'Selesai'                        => '<span class="badge bg-success"><i class="fe fe-check me-1"></i>Selesai</span>',
                'Ditolak'                        => '<span class="badge bg-danger"><i class="fe fe-x me-1"></i>Ditolak</span>',
                'Menunggu Persetujuan Pinjam'    => '<span class="badge bg-info"><i class="fe fe-loader me-1"></i>Menunggu Pinjam</span>',
                'Menunggu Persetujuan Kembali'   => '<span class="badge bg-info"><i class="fe fe-loader me-1"></i>Menunggu Kembali</span>',
                default                          => '<span class="badge bg-secondary">' . $row->bk_status . '</span>',
            };

            return [
                'barang_kode'      => $row->barang_kode ?? '-',
                'barang_nama'      => $row->barang_nama ?? '-',
                'merk_nama'        => $row->merk_nama ?? '-',
                'serial_number'    => empty($row->serial_number) || $row->serial_number == '-' ? '<span class="text-muted">-</span>' : $row->serial_number,
                'kode_barang_unik' => $row->kode_barang_unik ?? '-',
                'bk_jumlah'        => $row->bk_jumlah ?? '-',
                'satuan_id'        => $row->satuan_id ?? '-',
                'status'           => $statusBadge,
                'action'           => $action,
            ];
        });

        return response()->json($result);
    }


    public function proses_tambah(Request $request)
    {
        try {
            // ── Normalize input: support both batch (items[]) and legacy single-item ──
            $items = [];
            if ($request->has('items') && is_array($request->items)) {
                $items = $request->items;
            } elseif ($request->barang && $request->jml) {
                // Legacy
                $sns = [];
                if (is_string($request->serial_number)) {
                    $sns = array_filter(explode(',', $request->serial_number));
                } elseif (is_array($request->serial_number)) {
                    $sns = $request->serial_number;
                }
                $items[] = [
                    'kode' => $request->barang,
                    'jumlah' => intval($request->jml),
                    'sns' => array_filter(array_map('trim', $sns))
                ];
            }

            if (empty($items)) {
                return response()->json(['error' => 'Daftar barang tidak boleh kosong!'], 400);
            }

            $user      = Session::get('user');
            $roleId    = $user->role_id ?? 0;
            $teknisiSN = ($roleId == 3) ? ($user->teknisi_sn ?? '') : $request->teknisi;
            $teknisiUser = UserModel::where('teknisi_sn', $teknisiSN)->first();
            
            // Fix: Fallback for empty target
            $requestTujuan = $request->tujuan ?? '';
            $teknisiNm = $teknisiUser ? $teknisiUser->user_nmlengkap : (($roleId == 3) ? $user->user_nmlengkap : $requestTujuan);

            $customer  = $request->customer ?? $requestTujuan;
            $lokasi    = $request->lokasi ?? '-';
            $tglkeluar = $request->tglkeluar ? \Carbon\Carbon::parse($request->tglkeluar) : now();

            DB::beginTransaction();

            // GENERATE KODE BK (1 TRANSAKSI = 1 KODE)
            // Format: BK-MMYY-001
            $monthYear = $tglkeluar->format('my');
            $lastBK    = BarangkeluarModel::withTrashed()->where('bk_kode', 'LIKE', 'BK-' . $monthYear . '-%')
                ->orderBy('bk_kode', 'DESC')->lockForUpdate()->first();
            $nextNo    = $lastBK ? str_pad(intval(substr($lastBK->bk_kode, -3)) + 1, 3, '0', STR_PAD_LEFT) : '001';
            $bk_kode   = "BK-{$monthYear}-{$nextNo}";

            $bk_id_for_notif = null;
            $first_barang_nama = null;
            $total_jml_all = 0;

            foreach ($items as $item) {
                $kode = $item['kode'] ?? null;
                $jml = intval($item['jumlah'] ?? 0);
                $sns = $item['sns'] ?? [];
                if (!is_array($sns)) {
                    $sns = array_filter(explode(',', $sns));
                }
                $sns = array_filter(array_map('trim', $sns));

                if (!$kode || $jml <= 0) continue;
                $total_jml_all += $jml;

                // Ambil data barang
                $barang = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                    ->where('barang_kode', $kode)
                    ->first();

                if (!$barang) {
                    DB::rollBack();
                    return response()->json(['error' => "Barang dengan kode {$kode} tidak ditemukan"], 404);
                }
                if (!$first_barang_nama) $first_barang_nama = $barang->barang_nama;

                // Validasi stok barang
                $jmlmasuk = \App\Models\Admin\BarangmasukModel::where('barang_kode', $kode)->sum('bm_jumlah');

                $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                    ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                    ->where('tbl_barangkeluar.barang_kode', '=', $kode);

                $jmlkeluar = (clone $baseQuery)
                    ->whereIn('tbl_barangkeluar.bk_status', ['Dipinjam', 'Menunggu Persetujuan Pinjam', 'Menunggu Persetujuan Kembali'])
                    ->sum('tbl_barangkeluar.bk_jumlah')
                    + (clone $baseQuery)
                        ->where('tbl_barangkeluar.bk_status', 'Selesai')
                        ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                        ->sum('tbl_barangkeluar.bk_jumlah')
                    + (clone $baseQuery)
                        ->where('tbl_barangkeluar.bk_status', 'Selesai')
                        ->where('tbl_barangkeluar.bk_kondisi_kembali', 'Rusak Berat')
                        ->sum('tbl_barangkeluar.bk_jumlah');

                $current_stok = intval($barang->barang_stok) + ($jmlmasuk - $jmlkeluar);

                if ($current_stok <= 0) {
                    DB::rollBack();
                    return response()->json(['error' => "Stok barang {$barang->barang_nama} sudah habis (0)!"], 400);
                }
                if ($jml > $current_stok) {
                    DB::rollBack();
                    return response()->json(['error' => "Jumlah keluar ({$jml}) melebihi stok yang tersedia ({$current_stok}) untuk {$barang->barang_nama}!"], 400);
                }

                // Status otomatis: Habis Pakai = Selesai, Kembali = Dipinjam
                $status = (str_contains(strtolower($barang->jenisbarang_nama ?? ''), 'habis')) ? 'Selesai' : 'Dipinjam';
                if ($roleId == 3) {
                    $status = 'Menunggu Persetujuan Pinjam';
                }

                $is_kabel = in_array(strtolower($barang->satuan_id ?? ''), ['meter', 'm', 'mtr']);
                $is_habis = str_contains(strtolower($barang->jenisbarang_nama ?? ''), 'habis');

                // Validasi dan Insert SN
                if (!empty($sns)) {
                    if (count($sns) !== count(array_unique($sns))) {
                        DB::rollBack();
                        return response()->json(['error' => "Terdapat Serial Number yang sama/duplikat pada barang {$barang->barang_nama}!"], 400);
                    }
                    
                    // Jika BUKAN kabel meteran, jumlah SN yang dipilih harus persis sama dengan jumlah keluar
                    if (!$is_kabel && count($sns) !== $jml) {
                        DB::rollBack();
                        return response()->json(['error' => "Jumlah SN tidak cocok dengan jumlah keluar untuk barang {$barang->barang_nama}!"], 400);
                    }

                    foreach ($sns as $sn) {
                        // Jumlah per kode unik: untuk meter ambil dari sn_jumlah map, untuk non-meter = 1
                        $sn_jumlah_map = $item['sn_jumlah'] ?? [];
                        $req_qty_sn = $is_kabel ? intval($sn_jumlah_map[$sn] ?? $jml) : 1;

                        if ($sn && $sn !== '-') {
                            if ($is_habis) {
                                // 1. Cek stok khusus untuk barang habis pakai per Kode Unik
                                $bmRow = \App\Models\Admin\BarangmasukModel::where('barang_kode', $kode)
                                    ->where(function($q) use ($sn) {
                                        $q->where('serial_number', $sn)
                                          ->orWhere('kode_barang_unik', $sn);
                                    })
                                    ->first();
                                    
                                if ($bmRow) {
                                    $stok_awal_sn = $bmRow->bm_jumlah;
                                    
                                    // Hitung total keluar untuk SN ini
                                    $total_keluar_sn = BarangkeluarModel::where('barang_kode', $kode)
                                        ->where('kode_barang_unik', $bmRow->kode_barang_unik)
                                        ->sum('bk_jumlah');
                                        
                                    $sisa_stok_sn = $stok_awal_sn - $total_keluar_sn;
                                    
                                    if ($req_qty_sn > $sisa_stok_sn) {
                                        DB::rollBack();
                                        return response()->json(['error' => "Stok untuk kode unik {$sn} tidak mencukupi! Sisa stok: {$sisa_stok_sn}, diminta: {$req_qty_sn}"], 400);
                                    }
                                } else {
                                    DB::rollBack();
                                    return response()->json(['error' => "Data barang masuk untuk kode {$sn} tidak ditemukan!"], 400);
                                }
                            } else {
                                // 2. Cek apakah SN inventaris sedang dipinjam/menunggu persetujuan
                                $isBorrowed = BarangkeluarModel::where(function($q) use ($sn) {
                                        $q->where('serial_number', $sn)
                                          ->orWhere('kode_barang_unik', $sn);
                                    })
                                    ->whereIn('bk_status', ['Dipinjam', 'Menunggu Persetujuan Pinjam', 'Menunggu Persetujuan Kembali'])
                                    ->exists();
                                if ($isBorrowed) {
                                    DB::rollBack();
                                    return response()->json(['error' => "Barang dengan identitas {$sn} sedang dipinjam/menunggu persetujuan!"], 400);
                                }
                            }
                        }

                        // Get real SN and KBU from BM based on the identifier passed from frontend
                        $bmRow = \App\Models\Admin\BarangmasukModel::where('barang_kode', $kode)
                            ->where(function($q) use ($sn) {
                                $q->where('serial_number', $sn)
                                  ->orWhere('kode_barang_unik', $sn);
                            })
                            ->first();
                        
                        $kbu = $bmRow ? $bmRow->kode_barang_unik : null;
                        $real_sn = $bmRow ? $bmRow->serial_number : $sn; // fallback to $sn if not found

                        // Untuk kabel meter: bk_jumlah = jumlah meter dari kode unik ini
                        // Untuk non-meter: bk_jumlah = 1
                        $bk_jumlah_save = $is_kabel ? $req_qty_sn : 1;

                        $bk = BarangkeluarModel::create([
                            'bk_kode'          => $bk_kode,
                            'barang_kode'      => $kode,
                            'kode_barang_unik' => $kbu,
                            'bk_tanggal'       => $tglkeluar->toDateString(),
                            'bk_tujuan'        => $customer,
                            'bk_lokasi'        => $lokasi,
                            'bk_map_url'       => $request->map_url,
                            'bk_lat'           => $request->lat,
                            'bk_lng'           => $request->lng,
                            'bk_jumlah'        => $bk_jumlah_save,
                            'bk_status'        => $status,
                            'serial_number'    => $real_sn,
                            'teknisi'          => $teknisiSN,
                            'teknisi_nama'     => $teknisiNm,
                            'keterangan'       => $request->keterangan,
                            'jam_keluar'       => $tglkeluar,
                        ]);

                        if (!$bk_id_for_notif) $bk_id_for_notif = $bk->bk_id;
                    }
                } else {
                    // Barang tanpa SN
                    $bk = BarangkeluarModel::create([
                        'bk_kode'          => $bk_kode,
                        'barang_kode'      => $kode,
                        'kode_barang_unik' => '-',
                        'bk_tanggal'       => $tglkeluar->toDateString(),
                        'bk_tujuan'        => $customer,
                        'bk_lokasi'        => $lokasi,
                        'bk_map_url'       => $request->map_url,
                        'bk_lat'           => $request->lat,
                        'bk_lng'           => $request->lng,
                        'bk_jumlah'        => $jml,
                        'bk_status'        => $status,
                        'serial_number'    => '-',
                        'teknisi'          => $teknisiSN,
                        'teknisi_nama'     => $teknisiNm,
                        'keterangan'       => $request->keterangan,
                        'jam_keluar'       => $tglkeluar,
                    ]);
                    if (!$bk_id_for_notif) $bk_id_for_notif = $bk->bk_id;
                }
            }

            // Create notification for admin if technician is requesting
            if ($roleId == 3 && $bk_id_for_notif) {
                $barang_nama_label = count($items) > 1 ? $first_barang_nama . " dkk" : $first_barang_nama;
                $pesan = "{$user->user_nmlengkap} mengajukan peminjaman {$total_jml_all} unit ({$barang_nama_label}) untuk customer: {$customer}";

                NotifikasiModel::create([
                    'notif_type'        => 'peminjaman',
                    'notif_pesan'       => $pesan,
                    'notif_dari'        => $user->user_id,
                    'notif_nama_teknisi'=> $user->user_nmlengkap,
                    'notif_barang'      => $barang_nama_label,
                    'notif_customer'    => $customer,
                    'bk_id'             => $bk_id_for_notif,
                    'is_read_owner'     => 0,
                    'is_read_gudang'    => 0,
                ]);
            }

            DB::commit();
            return response()->json(['success' => 'Data Barang Keluar Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function proses_kembali(Request $request, $id)
    {
        $user = Session::get('user');
        
        $bk = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->where('tbl_barangkeluar.bk_id', $id)
            ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama')
            ->first();

        if (!$bk) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $status = ($user && $user->role_id == 3) ? 'Menunggu Persetujuan Kembali' : 'Selesai';

        BarangkeluarModel::find($id)->update([
            'bk_status'          => $status,
            'bk_tgl_kembali'     => $request->tglkembali,
            'bk_kondisi_kembali' => $request->kondisi,
            'bk_jumlah_kembali'  => $request->jml,
        ]);

        if ($status == 'Selesai' && $bk->kode_barang_unik) {
            \App\Models\Admin\BarangmasukModel::where('kode_barang_unik', $bk->kode_barang_unik)
                ->update(['jam_masuk' => now()]);
        }

        // Notifikasi pengembalian ke Owner & Admin Gudang
        $teknisi = UserModel::where('teknisi_sn', $bk->teknisi)->first();
        $nmTeknisi = $teknisi ? $teknisi->user_nmlengkap : ($bk->teknisi_nama ?? $bk->teknisi ?? 'Teknisi');

        $pesanNotif = ($status == 'Menunggu Persetujuan Kembali') 
            ? "Teknisi {$nmTeknisi} mengajukan pengembalian untuk barang {$bk->barang_nama}. Kondisi: {$request->kondisi}" 
            : "Barang {$bk->barang_nama} dari {$nmTeknisi} sudah dikembalikan. Kondisi: {$request->kondisi}";

        NotifikasiModel::create([
            'notif_type'        => 'pengembalian',
            'notif_pesan'       => $pesanNotif,
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
        $user = Session::get('user');
        if ($user && $user->role_id == 3) {
            return response()->json(['error' => 'Akses ditolak! Teknisi tidak berhak mengubah data transaksi.'], 403);
        }
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
                           ->sum('tbl_barangkeluar.bk_jumlah')
                       + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                           ->where('tbl_barangkeluar.bk_kondisi_kembali', 'Rusak Berat')
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
            $kbu = null;
            if ($sn && $sn !== '-') {
                $isBorrowed = BarangkeluarModel::where('serial_number', $sn)
                    ->whereIn('bk_status', ['Dipinjam', 'Menunggu Persetujuan Pinjam', 'Menunggu Persetujuan Kembali'])
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

                // Fetch new KBU and Real SN
                $bmRow = \App\Models\Admin\BarangmasukModel::where('barang_kode', $request->barang)
                    ->where(function($q) use ($sn) {
                        $q->where('serial_number', $sn)
                          ->orWhere('kode_barang_unik', $sn);
                    })
                    ->first();
                $kbu = $bmRow ? $bmRow->kode_barang_unik : null;
                $real_sn = $bmRow ? $bmRow->serial_number : $sn;
            } else {
                $real_sn = null;
            }

            $tglkeluar = $request->tglkeluar ? Carbon::parse($request->tglkeluar) : now();

            $teknisiUser = UserModel::where('teknisi_sn', $request->teknisi)->first();
            $teknisiNama = $teknisiUser ? $teknisiUser->user_nmlengkap : null;

            $bk->update([
                'bk_kode'          => $request->bkkode,
                'barang_kode'      => $request->barang,
                'kode_barang_unik' => $kbu,
                'bk_tanggal'       => $tglkeluar->toDateString(),
                'bk_tujuan'        => $request->tujuan,
                'bk_lokasi'        => $request->lokasi ?? '-',
                'bk_map_url'       => $request->map_url,
                'bk_lat'           => $request->lat,
                'bk_lng'           => $request->lng,
                'bk_jumlah'        => $request->jml,
                'serial_number'    => $real_sn,
                'teknisi'          => $request->teknisi,
                'teknisi_nama'     => $teknisiNama,
                'keterangan'       => $request->keterangan,
                'jam_keluar'       => $tglkeluar,
            ]);

            if ($request->applyToAll == 1) {
                BarangkeluarModel::where('bk_kode', $request->bkkode)
                    ->where('bk_id', '!=', $bk->bk_id)
                    ->update([
                        'bk_tanggal'   => $tglkeluar->toDateString(),
                        'bk_tujuan'    => $request->tujuan,
                        'bk_lokasi'    => $request->lokasi ?? '-',
                        'bk_lat'       => $request->lat,
                        'bk_lng'       => $request->lng,
                        'teknisi'      => $request->teknisi,
                        'teknisi_nama' => $teknisiNama,
                        'jam_keluar'   => $tglkeluar,
                    ]);
            }

            return response()->json(['success' => 'Berhasil']);
        }
        return response()->json(['error' => 'Gagal'], 404);
    }

    public function proses_hapus($id)
    {
        $user = Session::get('user');
        if ($user && $user->role_id == 3) {
            return response()->json(['error' => 'Akses ditolak! Teknisi tidak berhak menghapus data transaksi.'], 403);
        }
        $bk = BarangkeluarModel::find($id);
        if ($bk) {
            $bk->delete();
            return response()->json(['success' => 'Berhasil']);
        }
        return response()->json(['error' => 'Gagal'], 404);
    }

    public function hapusTransaksi($bk_kode)
    {
        $user = Session::get('user');
        if ($user && $user->role_id == 3) {
            return response()->json(['error' => 'Akses ditolak! Teknisi tidak berhak menghapus data transaksi.'], 403);
        }
        $deleted = BarangkeluarModel::where('bk_kode', $bk_kode)->delete();
        if ($deleted) {
            return response()->json(['success' => 'Transaksi berhasil dihapus!']);
        }
        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }

    public function getAvailableSN($barang_kode)
    {
        $barang = \App\Models\Admin\BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->where('barang_kode', $barang_kode)
            ->first();
            
        $is_meter = false;
        $is_habis = false;
        if ($barang) {
            $is_meter = in_array(strtolower($barang->satuan_id ?? ''), ['m', 'mtr', 'meter']);
            $is_habis = str_contains(strtolower($barang->jenisbarang_nama ?? ''), 'habis');
        }

        // 1. Ambil barang masuk dan jumlah awal
        $incomings = \App\Models\Admin\BarangmasukModel::where('barang_kode', $barang_kode)
            ->select('serial_number', 'kode_barang_unik', 'bm_jumlah')
            ->get();

        // 2. Hitung total yang sudah terpakai per kode unik (untuk barang habis pakai)
        $consumedAmounts = DB::table('tbl_barangkeluar')
            ->select('kode_barang_unik', DB::raw('SUM(bk_jumlah) as total_keluar'))
            ->where('barang_kode', $barang_kode)
            ->whereNull('deleted_at')
            ->groupBy('kode_barang_unik')
            ->pluck('total_keluar', 'kode_barang_unik')
            ->toArray();

        // 3. Cari yang sedang dipinjam (untuk barang inventaris)
        $borrowed = BarangkeluarModel::where('barang_kode', $barang_kode)
            ->whereIn('bk_status', ['Dipinjam', 'Menunggu Persetujuan Pinjam', 'Menunggu Persetujuan Kembali'])
            ->pluck('kode_barang_unik')
            ->filter()
            ->toArray();

        $unavailableKBUs = array_unique($borrowed);

        // Cari kondisi terakhir dari masing-masing SN
        $latestConditions = DB::table('tbl_barangkeluar')
            ->select('kode_barang_unik', 'bk_kondisi_kembali')
            ->whereNotNull('bk_kondisi_kembali')
            ->whereNull('deleted_at')
            ->whereIn('bk_id', function($query) {
                $query->select(DB::raw('MAX(bk_id)'))
                      ->from('tbl_barangkeluar')
                      ->whereNull('deleted_at')
                      ->groupBy('kode_barang_unik');
            })
            ->pluck('bk_kondisi_kembali', 'kode_barang_unik')
            ->toArray();

        // 4. Filter data ketersediaan
        $available = [];
        foreach ($incomings as $incoming) {
            $identifier = $incoming->kode_barang_unik;
            
            if ($identifier && !in_array($identifier, $unavailableKBUs)) {
                $kondisi = $latestConditions[$identifier] ?? 'Baik';
                
                if ($kondisi == 'Rusak Berat') {
                    continue;
                }
                
                $label = $kondisi;
                // Hanya tampilkan sisa untuk barang habis pakai yang satuannya meter
                if ($is_habis && $is_meter) {
                    $total_keluar = $consumedAmounts[$identifier] ?? 0;
                    $sisa = $incoming->bm_jumlah - $total_keluar;
                    
                    if ($sisa <= 0) {
                        continue; // Habis total, jangan tampilkan di dropdown
                    }
                    
                    $label .= ' (Sisa: ' . $sisa . ' meter)';
                } else if ($is_habis) {
                    // Jika barang habis pakai tapi BUKAN meter (contoh: Pcs, Lembar),
                    // tetap cek sisa stoknya agar jika sudah 0, tidak muncul di dropdown
                    $total_keluar = $consumedAmounts[$identifier] ?? 0;
                    $sisa = $incoming->bm_jumlah - $total_keluar;
                    
                    if ($sisa <= 0) {
                        continue; // Habis total
                    }
                }
                
                $available[] = [
                    'serial_number' => $identifier,
                    'kode_barang_unik' => $incoming->kode_barang_unik,
                    'kondisi' => $label
                ];
            }
        }

        return response()->json($available);
    }

    public function terima_pinjam(Request $request, $id)
    {
        $bk = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->where('tbl_barangkeluar.bk_id', $id)
            ->first();
        if (!$bk) return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $status = (str_contains(strtolower($bk->jenisbarang_nama ?? ''), 'habis')) ? 'Selesai' : 'Dipinjam';
        BarangkeluarModel::find($id)->update([
            'bk_status'  => $status,
            'jam_keluar' => now(),
        ]);
        return response()->json(['success' => 'Peminjaman disetujui!']);
    }

    public function tolak_pinjam(Request $request, $id)
    {
        $bk = BarangkeluarModel::find($id);
        if (!$bk) return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $bk->update(['bk_status' => 'Ditolak']);
        return response()->json(['success' => 'Peminjaman ditolak!']);
    }

    public function terima_kembali(Request $request, $id)
    {
        $bk = BarangkeluarModel::find($id);
        if (!$bk) return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $bk->update(['bk_status' => 'Selesai']);

        if ($bk->kode_barang_unik) {
            \App\Models\Admin\BarangmasukModel::where('kode_barang_unik', $bk->kode_barang_unik)
                ->update(['jam_masuk' => now()]);
        }

        return response()->json(['success' => 'Pengembalian disetujui!']);
    }

    public function tolak_kembali(Request $request, $id)
    {
        $bk = BarangkeluarModel::find($id);
        if (!$bk) return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $bk->update(['bk_status' => 'Dipinjam']);
        return response()->json(['success' => 'Pengembalian ditolak! Status dikembalikan ke Dipinjam.']);
    }

    public function proses_batal(Request $request, $id)
    {
        try {
            $bk = BarangkeluarModel::find($id);
            if (!$bk) return response()->json(['error' => 'Data tidak ditemukan'], 404);

            if ($bk->bk_status != 'Dipinjam') {
                return response()->json(['error' => 'Hanya barang yang sedang dipinjam yang dapat dibatalkan.'], 400);
            }

            // Hapus record (soft delete) agar stok otomatis kembali dan tidak tampil di laporan
            $bk->delete();
            
            return response()->json(['success' => 'Peminjaman barang berhasil dibatalkan.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function resolveMapLink(Request $request)
    {
        $inputUrl = $request->input('url');
        if (!$inputUrl) return response()->json(['error' => 'URL kosong'], 400);

        // Extract the actual URL if there is text around it (e.g., from mobile share)
        preg_match('/(https?:\/\/[^\s]+)/', $inputUrl, $urlMatches);
        $url = $urlMatches[1] ?? $inputUrl;

        try {
            // Using cURL to follow redirect and get the final URL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5); 
            
            // Adding a user agent is important for some Google redirect endpoints
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
            
            $response = curl_exec($ch);
            $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);

            // Extract lat lng from the final URL
            $lat = null;
            $lng = null;

            if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $lat = $matches[1];
                $lng = $matches[2];
            } elseif (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $lat = $matches[1];
                $lng = $matches[2];
            } elseif (preg_match('/search\/(-?\d+\.\d+),\+?(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $lat = $matches[1];
                $lng = $matches[2];
            } elseif (preg_match('/q=(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $lat = $matches[1];
                $lng = $matches[2];
            } elseif (preg_match('/ll=(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $lat = $matches[1];
                $lng = $matches[2];
            }

            if ($lat && $lng) {
                return response()->json([
                    'success' => true,
                    'lat' => $lat,
                    'lng' => $lng,
                    'final_url' => $finalUrl
                ]);
            }
            
            return response()->json(['error' => 'Gagal mengekstrak koordinat. Pastikan link Google Maps menunjukkan lokasi spesifik (titik pin/alamat rinci), bukan link area atau kota.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function unreturnedItems($bk_kode)
    {
        $items = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->where('tbl_barangkeluar.bk_kode', $bk_kode)
            ->whereIn('tbl_barangkeluar.bk_status', ['Dipinjam', 'Menunggu Persetujuan Pinjam', 'Menunggu Persetujuan Kembali'])
            ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama')
            ->get();

        return response()->json($items);
    }

    /**
     * Batch Kembali: Kembalikan semua barang dalam satu kode BK sekaligus
     */
    public function batchKembali(Request $request, $bk_kode)
    {
        try {
            $itemsData = $request->input('items_data', []);
            $tglKembali = $request->input('tglkembali', now());
            
            if (empty($itemsData)) {
                return response()->json(['error' => 'Tidak ada barang yang dipilih untuk dikembalikan.'], 400);
            }

            $rows = BarangkeluarModel::where('bk_kode', $bk_kode)
                ->whereIn('bk_id', array_keys($itemsData))
                ->whereIn('bk_status', ['Dipinjam', 'Menunggu Persetujuan Pinjam'])
                ->get();

            if ($rows->isEmpty()) {
                return response()->json(['error' => 'Tidak ada barang dengan status Dipinjam pada transaksi ini.'], 400);
            }

            $count = 0;
            foreach ($rows as $row) {
                $kondisi = $itemsData[$row->bk_id] ?? 'Baik';
                $row->bk_status       = 'Selesai';
                $row->bk_tgl_kembali  = $tglKembali;
                $row->bk_kondisi_kembali = $kondisi;
                $row->save();

                // Notifikasi pengembalian bisa ditambahkan jika perlu (opsional)

                $count++;
            }

            return response()->json(['success' => $count . ' barang berhasil dikembalikan pada transaksi ' . $bk_kode . '.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
