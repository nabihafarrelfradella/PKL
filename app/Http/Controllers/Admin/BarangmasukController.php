<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class BarangmasukController extends Controller
{
    public function index()
    {
        $data["title"] = "Barang Masuk";
        $user = Session::get('user');
        $data["hakTambah"] = $this->checkAccess($user->role_id ?? 0, '/barang-masuk', 'create');
        return view('Admin.BarangMasuk.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $searchTerm = $request->search_term ?? '';

            // Kelompokkan HANYA per bm_kode (1 baris = 1 transaksi penerimaan barang/surat jalan)
            $query = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                ->select(
                    'tbl_barangmasuk.bm_kode',
                    DB::raw('MAX(tbl_barangmasuk.bm_id) as bm_id'),
                    DB::raw('MAX(tbl_barangmasuk.jam_masuk) as jam_masuk'),
                    DB::raw('MAX(tbl_barangmasuk.bm_tanggal) as bm_tanggal'),
                    DB::raw('COUNT(tbl_barangmasuk.bm_id) as total_unit')
                );

            if ($searchTerm) {
                // Filter by bm_kode, barang_nama, or kode_barang_unik within the group
                $query->where(function($q) use ($searchTerm) {
                    $q->where('tbl_barangmasuk.bm_kode', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('tbl_barang.barang_nama', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('tbl_barangmasuk.kode_barang_unik', 'LIKE', "%{$searchTerm}%");
                });
            }

            $data = $query->groupBy('tbl_barangmasuk.bm_kode')->orderBy('bm_id', 'DESC');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    $datetime = $row->jam_masuk ?? $row->bm_tanggal;
                    return $datetime ? Carbon::parse($datetime)->translatedFormat('d F Y H:i') : '-';
                })
                ->addColumn('chk', function ($row) {
                    return '<input type="checkbox" class="parent-checkbox" data-bm-kode="' . htmlspecialchars($row->bm_kode) . '">';
                })

                ->addColumn('serial_number', function ($row) {
                    return $row->total_unit . ' unit';
                })
                ->addColumn('expand', function ($row) {
                    return '<button class="btn btn-sm btn-light btn-expand-bm"
                        data-bm-kode="' . htmlspecialchars($row->bm_kode) . '"
                        title="Lihat Detail Serial Number">
                        <i class="fe fe-chevron-right"></i>
                    </button>';
                })
                ->addColumn('action', function ($row) {
                    $user = Session::get('user');
                    $hakAkses = $this->checkAccess($user->role_id ?? 0, '/barang-masuk', 'delete');
                    
                    $bmKode = htmlspecialchars($row->bm_kode, ENT_QUOTES, 'UTF-8');
                    $btnHapus = $hakAkses ? '<button class="btn btn-danger-light btn-sm" onclick="hapusSemuaBM(\'' . $bmKode . '\')" title="Hapus Transaksi"><i class="fe fe-trash-2"></i></button>' : '';
                    
                    $action = '<div class="d-flex align-items-center gap-2">
                        <button class="btn btn-info-light btn-sm" onclick="batchPrintQR(\'' . $bmKode . '\')" title="Batch Print QR">
                            <i class="fe fe-printer"></i>
                        </button>
                        ' . $btnHapus . '
                    </div>';
                    return $action;
                })
                ->rawColumns(['action', 'serial_number', 'expand', 'chk'])

                ->make(true);
        }
    }

    /**
     * Endpoint: Kembalikan semua SN berdasarkan barang_kode
     */
    public function detailSN($bm_kode)
    {
        // Filter HANYA berdasarkan bm_kode (semua barang di transaksi tsb)
        $rows = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
            ->leftJoin('tbl_satuan', 'tbl_satuan.satuan_id', '=', 'tbl_barang.satuan_id')
            ->where('tbl_barangmasuk.bm_kode', $bm_kode)
            ->select('tbl_barangmasuk.*', 'tbl_barang.barang_nama', 'tbl_merk.merk_nama', 'tbl_barang.satuan_id')
            ->orderBy('bm_id', 'DESC')
            ->get();

        $user   = Session::get('user');
        $hakEdit = $this->checkAccess($user->role_id ?? 0, '/barang-masuk', 'update');
        $hakDelete = $this->checkAccess($user->role_id ?? 0, '/barang-masuk', 'delete');

        $result = $rows->map(function ($row) use ($hakEdit, $hakDelete) {
            $parts = explode(' - ', $row->barang_nama);
            $namaBarang = trim($parts[0] ?? $row->barang_nama);
            $merkBarang = count($parts) > 1 ? trim($parts[1]) : ($row->merk_nama ?? '-');

            $array = [
                "bm_id"            => $row->bm_id,
                "bm_kode"          => $row->bm_kode,
                "barang_kode"      => $row->barang_kode,
                "barang_nama"      => str_replace(["'", '"'], "", $namaBarang),
                "merk_nama"        => $merkBarang,
                "bm_tanggal"       => $row->bm_tanggal,
                "jam_masuk"        => $row->jam_masuk,
                "bm_jumlah"        => $row->bm_jumlah,
                "serial_number"    => $row->serial_number,
                "kode_barang_unik" => $row->kode_barang_unik,
            ];
            $json   = htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8');
            $action = '';

            // QR
            $action .= '<a class="btn text-info btn-sm" data-bs-toggle="modal" href="#Qmodaldemo8" onclick="showQR(' . $json . ')"><span class="fe fe-printer fs-13"></span></a>';

            if ($hakEdit > 0) {
                $action .= '<a class="btn text-success btn-sm" data-bs-toggle="modal" href="#Umodaldemo8" onclick="update(' . $json . ')"><span class="fe fe-edit fs-13"></span></a>';
            }
            if ($hakDelete > 0) {
                $action .= '<a class="btn text-danger btn-sm" data-bs-toggle="modal" href="#Hmodaldemo8" onclick="hapus(' . $json . ')"><span class="fe fe-trash-2 fs-13"></span></a>';
            }

            $tgl = $row->jam_masuk
                ? Carbon::parse($row->jam_masuk)->translatedFormat('d M Y H:i')
                : ($row->bm_tanggal ? Carbon::parse($row->bm_tanggal)->translatedFormat('d M Y') : '-');

            return [
                'barang_kode'      => $row->barang_kode ?? '-',
                'barang_nama'      => $namaBarang ?? '-',
                'merk_nama'        => $merkBarang ?? '-',
                'serial_number'    => $row->serial_number ?? '-',
                'kode_barang_unik' => $row->kode_barang_unik ?? '-',
                'bm_jumlah'        => $row->bm_jumlah ?? '-',
                'satuan_id'        => $row->satuan_id ?? '-',
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
                // Batch mode: items[] = [{kode, jumlah, sn}, ...]
                foreach ($request->items as $item) {
                    $kode = $item['kode'] ?? null;
                    $jumlah = intval($item['jumlah'] ?? 0);
                    if ($kode && $jumlah > 0) {
                        $items[] = [
                            'kode' => $kode, 
                            'jumlah' => $jumlah, 
                            'sn' => $item['sn'] ?? null
                        ];
                    }
                }
            } elseif ($request->barang && $request->jml) {
                // Legacy single-item mode (backward-compatible)
                $items[] = [
                    'kode' => $request->barang, 
                    'jumlah' => intval($request->jml), 
                    'sn' => $request->serial_number ?? null
                ];
            }

            if (empty($items)) {
                return response()->json(['error' => 'Barang dan Jumlah tidak boleh kosong!'], 400);
            }

            // Gunakan datetime dari form, fallback ke now()
            $tglmasuk = $request->tglmasuk
                ? \Carbon\Carbon::parse($request->tglmasuk)
                : now();

            $totalSaved = 0;

            // Generate ONE bm_kode for the entire transaction batch
            $monthYear = now()->format('my');
            $lastBM = BarangmasukModel::withTrashed()->where('bm_kode', 'LIKE', 'BM-' . $monthYear . '-%')
                ->orderBy('bm_kode', 'DESC')
                ->first();

            if ($lastBM) {
                $lastNo = intval(substr($lastBM->bm_kode, -3));
                $nextNo = str_pad($lastNo + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextNo = '001';
            }
            $bm_kode = "BM-{$monthYear}-{$nextNo}";

            foreach ($items as $item) {
                $barangKode = $item['kode'];
                $jml = $item['jumlah'];

                $barang = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                    ->where('tbl_barang.barang_kode', $barangKode)
                    ->first();

                if (!$barang) {
                    continue; // Skip barang yang tidak ditemukan
                }

                // Cek apakah barang ini bersatuan Meter (disimpan dalam 1 baris, bukan per unit)
                $isMeter = strtolower(trim($barang->satuan_id ?? '')) === 'meter';

                $existingCount = BarangmasukModel::where('barang_kode', $barangKode)->count();

                if ($isMeter) {
                    // Khusus barang Meter: simpan 1 baris dengan bm_jumlah = total panjang (meter)
                    // Kode unik: auto-increment berikutnya
                    $kode_barang_unik = $barangKode . '-' . str_pad($existingCount + 1, 2, '0', STR_PAD_LEFT);

                    $sn_input = !empty($item['sn']) ? $item['sn'] : null;

                    BarangmasukModel::create([
                        'bm_tanggal'       => $tglmasuk->toDateString(),
                        'bm_kode'          => $bm_kode,
                        'barang_kode'      => $barangKode,
                        'bm_jumlah'        => $jml, // Total panjang meter dalam satu roll/entry
                        'serial_number'    => $sn_input,
                        'kode_barang_unik' => $kode_barang_unik,
                        'jam_masuk'        => $tglmasuk,
                        'customer_id'      => $request->customer_id ?? 0,
                    ]);

                    $totalSaved++;
                } else {
                    // Barang NON-Meter: setiap unit = 1 baris terpisah dengan kode unik masing-masing
                    for ($i = 1; $i <= $jml; $i++) {
                        $sn_input = !empty($item['sn']) ? $item['sn'] : null;

                        if ($sn_input) {
                            // SN hanya sebagai catatan
                            $serial_number = ($jml > 1) ? $sn_input . '-' . $i : $sn_input;
                        } else {
                            $serial_number = null;
                        }

                        // Kode unik SELALU menggunakan Kode Barang + Auto Increment (meskipun ada SN)
                        $kode_barang_unik = $barangKode . '-' . str_pad($existingCount + $i, 2, '0', STR_PAD_LEFT);

                        BarangmasukModel::create([
                            'bm_tanggal'       => $tglmasuk->toDateString(),
                            'bm_kode'          => $bm_kode,
                            'barang_kode'      => $barangKode,
                            'bm_jumlah'        => 1, // Setiap baris adalah 1 unit
                            'serial_number'    => $serial_number,
                            'kode_barang_unik' => $kode_barang_unik,
                            'jam_masuk'        => $tglmasuk,
                            'customer_id'      => $request->customer_id ?? 0,
                        ]);

                        $totalSaved++;
                    }
                }
            }

            if ($totalSaved === 0) {
                return response()->json(['error' => 'Tidak ada data barang yang valid untuk disimpan!'], 400);
            }

            $jenisCount = count($items);
            $msg = $jenisCount > 1
                ? "Berhasil menyimpan {$totalSaved} unit dari {$jenisCount} jenis barang."
                : "Berhasil menyimpan {$totalSaved} data barang masuk.";

            return response()->json(['success' => $msg]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal simpan: ' . $e->getMessage()], 500);
        }
    }

    public function proses_ubah(Request $request, $id)
    {
        try {
            $barangmasuk = BarangmasukModel::findOrFail($id);
            $oldSN = $barangmasuk->serial_number;
            $oldKodeUnik = $barangmasuk->kode_barang_unik;

            $newSN = $request->serial_number;
            $kode_barang_unik = !empty($newSN) ? $newSN : $oldKodeUnik;

            $barangmasuk->update([
                'bm_tanggal'       => $request->tglmasuk,
                'bm_kode'          => $request->bmkode,
                'barang_kode'      => $request->barang,
                'bm_jumlah'        => $request->jml,
                'serial_number'    => $newSN,
                'kode_barang_unik' => $kode_barang_unik,
            ]);

            // Cascade update to tbl_barangkeluar if the SN changed
            if ($oldSN && $oldSN !== $request->serial_number) {
                \App\Models\Admin\BarangkeluarModel::where('barang_kode', $barangmasuk->barang_kode)
                    ->where('serial_number', $oldSN)
                    ->update(['serial_number' => $request->serial_number]);
            }

            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function proses_hapus($id)
    {
        try {
            $barangmasuk = BarangmasukModel::findOrFail($id);
            
            if ($barangmasuk->kode_barang_unik) {
                \App\Models\Admin\BarangkeluarModel::where('kode_barang_unik', $barangmasuk->kode_barang_unik)->delete();
            } else if ($barangmasuk->serial_number) {
                \App\Models\Admin\BarangkeluarModel::where('serial_number', $barangmasuk->serial_number)
                    ->where('barang_kode', $barangmasuk->barang_kode)
                    ->delete();
            }

            $barangmasuk->delete();
            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function hapus_kelompok(Request $request)
    {
        try {
            $bm_kodes = $request->bm_kodes ?? [];
            if ($request->bm_kode && empty($bm_kodes)) {
                $bm_kodes[] = $request->bm_kode;
            }

            if (empty($bm_kodes)) {
                return response()->json(['error' => 'Kode BM tidak valid!'], 400);
            }

            $bms = BarangmasukModel::whereIn('bm_kode', $bm_kodes)->get();
            if ($bms->isEmpty()) {
                return response()->json(['error' => 'Data tidak ditemukan!'], 404);
            }
            foreach($bms as $bm) {
                if ($bm->kode_barang_unik) {
                    \App\Models\Admin\BarangkeluarModel::where('kode_barang_unik', $bm->kode_barang_unik)->delete();
                } else if ($bm->serial_number) {
                    \App\Models\Admin\BarangkeluarModel::where('serial_number', $bm->serial_number)
                        ->where('barang_kode', $bm->barang_kode)
                        ->delete();
                }
                $bm->delete();
            }
            return response()->json(['success' => 'Berhasil menghapus ' . count($bm_kodes) . ' transaksi.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function detail_sn_batch(Request $request)
    {
        try {
            $bm_kodes = $request->bm_kodes ?? [];
            if (empty($bm_kodes)) {
                return response()->json([]);
            }

            $rows = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
                ->whereIn('tbl_barangmasuk.bm_kode', $bm_kodes)
                ->select('tbl_barangmasuk.*', 'tbl_barang.barang_nama', 'tbl_merk.merk_nama')
                ->orderBy('bm_id', 'ASC')
                ->get();

            $mappedRows = $rows->map(function($row) {
                $parts = explode(' - ', $row->barang_nama);
                $row->barang_nama = trim($parts[0] ?? $row->barang_nama);
                $row->merk_nama = count($parts) > 1 ? trim($parts[1]) : ($row->merk_nama ?? '-');
                return $row;
            });

            return response()->json($mappedRows);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}