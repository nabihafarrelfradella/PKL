<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangModel;
use App\Models\Admin\JenisBarangModel;
use App\Models\Admin\MerkModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        $data["title"] = "Barang";
        $data["hakTambah"] = (Session::get('user')->role_id == 1 || Session::get('user')->role_id == 2) ? 1 : 0;
        $data["jenisbarang"] = JenisBarangModel::whereIn('jenisbarang_nama', ['Barang Habis Pakai', 'Barang Kembali'])
            ->get()
            ->unique('jenisbarang_nama');
        $data["merk"] = MerkModel::orderBy('merk_id', 'DESC')->get();
        return view('Admin.Barang.index', $data);
    }

    public function getbarang($id)
    {
        $data = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')->where('tbl_barang.barang_kode', '=', $id)->get();
        return json_encode($data);
    }

    public function getunit($id)
    {
        // Cari di tbl_barangmasuk berdasarkan kode_barang_unik atau serial_number
        $data = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
            ->where('tbl_barangmasuk.kode_barang_unik', '=', $id)
            ->orWhere('tbl_barangmasuk.serial_number', '=', $id)
            ->select('tbl_barangmasuk.*', 'tbl_barang.*', 'tbl_jenisbarang.jenisbarang_nama', 'tbl_merk.merk_nama')
            ->get();
            
        return json_encode($data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $query = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
                ->orderBy('barang_id', 'DESC');

            if ($request->filter_nama) {
                $query->where('tbl_barang.barang_nama', 'LIKE', '%' . $request->filter_nama . '%');
            }

            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('img', function ($row) {
                    $array = array(
                        "barang_gambar" => $row->barang_gambar,
                    );
                    if ($row->barang_gambar == "image.png") {
                        $img = '<a data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Gmodaldemo8" onclick=gambar(' . json_encode($array) . ')><span class="avatar avatar-lg cover-image" style="background: url(&quot;' . url('/assets/default/barang') . '/' . $row->barang_gambar . '&quot;) center center;"></span></a>';
                    } else {
                        $img = '<a data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Gmodaldemo8" onclick=gambar(' . json_encode($array) . ')><span class="avatar avatar-lg cover-image" style="background: url(&quot;' . asset('storage/barang/' . $row->barang_gambar) . '&quot;) center center;"></span></a>';
                    }

                    return $img;
                })
                ->addColumn('jenisbarang', function ($row) {
                    $jenisbarang = $row->jenisbarang_id == '' ? '-' : $row->jenisbarang_nama;

                    return $jenisbarang;
                })
                ->addColumn('satuan', function ($row) {
                    $satuan = $row->satuan_id == '' ? '-' : $row->satuan_id;

                    return $satuan;
                })
                ->addColumn('merk', function ($row) {
                    $merk = $row->merk_id == '' ? '-' : $row->merk_nama;

                    return $merk;
                })

                ->addColumn('totalstok', function ($row) use ($request) {
                    if ($request->tglawal == '') {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->leftJoin('tbl_customer', 'tbl_customer.customer_id', '=', 'tbl_barangmasuk.customer_id')->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    } else {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->leftJoin('tbl_customer', 'tbl_customer.customer_id', '=', 'tbl_barangmasuk.customer_id')->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir])->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    }

                    // Hanya hitung keluar yang benar-benar mengurangi stok:
                    // 1. Masih dipinjam (Dipinjam) — semua jenis
                    // 2. Selesai TAPI barang Habis Pakai (tidak kembali)
                    if ($request->tglawal) {
                        $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                            ->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])
                            ->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode);
                    } else {
                        $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                            ->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode);
                    }
                    $jmlkeluar = (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                               + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                                   ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                                   ->sum('tbl_barangkeluar.bk_jumlah');

                    $totalstok = $row->barang_stok + ($jmlmasuk - $jmlkeluar);
                    $satuan = $row->satuan_id == '' ? '' : ' ' . $row->satuan_id;
                    if($totalstok == 0){
                        $result = '<span class="">'.$totalstok.$satuan.'</span>';
                    }else if($totalstok > 0){
                        $result = '<span class="text-success">'.$totalstok.$satuan.'</span>';
                    }else{
                        $result = '<span class="text-danger">'.$totalstok.$satuan.'</span>';
                    }
                    

                    return $result;
                })
                ->addColumn('tipe', function ($row) {
                    $tipe = $row->jenisbarang_keterangan == '' ? '-' : $row->jenisbarang_keterangan;

                    return $tipe;
                })
                ->addColumn('action', function ($row) {
                    $array = array(
                        "barang_id" => $row->barang_id,
                        "jenisbarang_id" => $row->jenisbarang_id,
                        "jenisbarang_nama" => $row->jenisbarang_nama, // Tambahkan ini
                        "satuan_id" => $row->satuan_id,
                        "merk_id" => $row->merk_id,
                        "barang_kode" => $row->barang_kode,
                        "barang_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->barang_nama)),
                        "barang_stok" => $row->barang_stok,
                        "barang_gambar" => $row->barang_gambar,
                        "tipe_barang" => $row->tipe_barang,
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
                ->rawColumns(['action', 'img', 'jenisbarang', 'satuan', 'merk', 'totalstok', 'tipe'])->make(true);
        }
    }

    public function listbarang(Request $request)
    {
        if ($request->ajax()) {
            $data = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')->orderBy('barang_id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('img', function ($row) {
                    if ($row->barang_gambar == "image.png") {
                        $img = '<span class="avatar avatar-lg cover-image" style="background: url(&quot;' . url('/assets/default/barang') . '/' . $row->barang_gambar . '&quot;) center center;"></span>';
                    } else {
                        $img = '<span class="avatar avatar-lg cover-image" style="background: url(&quot;' . asset('storage/barang/' . $row->barang_gambar) . '&quot;) center center;"></span>';
                    }

                    return $img;
                })
                ->addColumn('jenisbarang', function ($row) {
                    $jenisbarang = $row->jenisbarang_id == '' ? '-' : $row->jenisbarang_nama;

                    return $jenisbarang;
                })
                ->addColumn('satuan', function ($row) {
                    $satuan = $row->satuan_id == '' ? '-' : $row->satuan_id;

                    return $satuan;
                })
                ->addColumn('merk', function ($row) {
                    $merk = $row->merk_id == '' ? '-' : $row->merk_nama;

                    return $merk;
                })

                ->addColumn('totalstok', function ($row) use ($request) {
                    if ($request->tglawal == '') {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->leftJoin('tbl_customer', 'tbl_customer.customer_id', '=', 'tbl_barangmasuk.customer_id')->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    } else {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->leftJoin('tbl_customer', 'tbl_customer.customer_id', '=', 'tbl_barangmasuk.customer_id')->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir])->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    }

                    // Hanya hitung keluar yang benar-benar mengurangi stok:
                    // 1. Masih dipinjam (Dipinjam) — semua jenis
                    // 2. Selesai TAPI barang Habis Pakai (tidak kembali)
                    if ($request->tglawal) {
                        $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                            ->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])
                            ->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode);
                    } else {
                        $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                            ->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode);
                    }
                    $jmlkeluar = (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                               + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                                   ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                                   ->sum('tbl_barangkeluar.bk_jumlah');

                    $totalstok = $row->barang_stok + ($jmlmasuk - $jmlkeluar);
                    $satuan = $row->satuan_id == '' ? '' : ' ' . $row->satuan_id;
                    if($totalstok == 0){
                        $result = '<span class="">'.$totalstok.$satuan.'</span>';
                    }else if($totalstok > 0){
                        $result = '<span class="text-success">'.$totalstok.$satuan.'</span>';
                    }else{
                        $result = '<span class="text-danger">'.$totalstok.$satuan.'</span>';
                    }
                    

                    return $result;
                })
                ->addColumn('tipe', function ($row) {
                    $tipe = $row->jenisbarang_keterangan == '' ? '-' : $row->jenisbarang_keterangan;

                    return $tipe;
                })
                ->addColumn('action', function ($row) use ($request) {
                    $array = array(
                        "barang_kode" => $row->barang_kode,
                        "barang_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->barang_nama)),
                        "satuan_nama" => $row->satuan_id,
                        "jenisbarang_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->jenisbarang_nama)),
                        "tipe_barang" => $row->tipe_barang,
                    );
                    $button = '';
                    if ($request->get('param') == 'tambah') {
                        $button .= '
                        <div class="g-2">
                            <a class="btn btn-primary btn-sm" href="javascript:void(0)" onclick="pilihBarang(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')">Pilih</a>
                        </div>
                        ';
                    } else {
                        $button .= '
                    <div class="g-2">
                        <a class="btn btn-success btn-sm" href="javascript:void(0)" onclick="pilihBarangU(' . htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8') . ')">Pilih</a>
                    </div>
                    ';
                    }

                    return $button;
                })
                ->rawColumns(['action', 'img', 'jenisbarang', 'satuan', 'merk', 'currency', 'totalstok', 'tipe'])->make(true);
        }
    }

    public function proses_tambah(Request $request)
    {
        $request->validate([
            'nama'          => 'required',
            'jenisbarang'   => 'required|in:bk,hp', 
            'satuan'        => 'required',
            'merk'          => 'required',
            'stok'          => 'required|numeric',
        ]);

        $img = "";
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->nama)));

        //upload image
        if ($request->file('foto') == null) {
            $img = "image.png";
        } else {
            $image = $request->file('foto');
            $filename = $image->hashName();
            $destinationPath = public_path('storage/barang');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $filename);
            $img = $filename;
        }

        // Generate KODE BARANG: [BK/HP]-[MMYY]-[001]
        $prefix = strtoupper($request->jenisbarang);
        $monthYear = now()->format('my'); // format MMYY
        
        $lastBarang = BarangModel::where('barang_kode', 'LIKE', $prefix . '-' . $monthYear . '-%')
            ->orderBy('barang_kode', 'DESC')
            ->first();

        if ($lastBarang) {
            $lastNo = intval(substr($lastBarang->barang_kode, -3));
            $nextNo = str_pad($lastNo + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNo = '001';
        }

        $barang_kode = $prefix . '-' . $monthYear . '-' . $nextNo;

        // Map "bk/hp" to jenisbarang_id via jenisbarang_keterangan
        $jenisName = ($request->jenisbarang == 'bk') ? 'Barang Kembali' : 'Barang Habis Pakai';
        $jenis = JenisBarangModel::where('jenisbarang_keterangan', $jenisName)->first();
        $jenis_id = $jenis ? $jenis->jenisbarang_id : null;

        $stok = intval($request->stok);

        //create
        BarangModel::create([
            'barang_gambar'  => $img,
            'jenisbarang_id' => $jenis_id,
            'satuan_id'      => $request->satuan,
            'merk_id'        => $request->merk,
            'barang_kode'    => $barang_kode,
            'barang_nama'    => $request->nama,
            'barang_slug'    => $slug,
            'barang_stok'    => 0, // Set to 0 so total stock is calculated from transactions
            'tipe_barang'    => $jenisName,
            'barang_harga'   => '0',
            'serial_number'  => '-',
        ]);

        if ($stok > 0) {
            $prefix_sn = strtoupper(substr($barang_kode, 0, 2));
            $date_now  = now()->format('Ymd');
            
            // Generate ONE BM Code for all initial stock units
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

            for ($i = 1; $i <= $stok; $i++) {
                $loop_index   = str_pad($i, 2, '0', STR_PAD_LEFT);
                $random_code  = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
                $serial_number = "{$prefix_sn}-{$date_now}-{$random_code}-{$loop_index}";
                $kode_barang_unik = 'BRG-' . now()->timestamp . '-' . $loop_index;

                BarangmasukModel::create([
                    'bm_tanggal'       => now()->toDateString(),
                    'bm_kode'          => $bm_kode,
                    'barang_kode'      => $barang_kode,
                    'bm_jumlah'        => 1,
                    'serial_number'    => $serial_number,
                    'kode_barang_unik' => $kode_barang_unik,
                    'jam_masuk'        => now(),
                    'customer_id'      => 0,
                ]);
            }
        }

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_ubah(Request $request, BarangModel $barang)
    {
        $request->validate([
            'nama'          => 'required',
            'jenisbarang'   => 'required|in:bk,hp', 
            'satuan'        => 'required',
            'merk'          => 'required',
            'stok'          => 'required|numeric',
        ]);

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->nama)));

        // Map "bk/hp" to jenisbarang_id via jenisbarang_keterangan
        $jenisName = ($request->jenisbarang == 'bk') ? 'Barang Kembali' : 'Barang Habis Pakai';
        $jenis = JenisBarangModel::where('jenisbarang_keterangan', $jenisName)->first();
        $jenis_id = $jenis ? $jenis->jenisbarang_id : null;

        $updateData = [
            'jenisbarang_id' => $jenis_id,
            'satuan_id'      => $request->satuan,
            'merk_id'        => $request->merk,
            'barang_nama'    => $request->nama,
            'barang_slug'    => $slug,
            'barang_stok'    => $request->stok,
            'tipe_barang'    => $jenisName,
        ];

        //check if image is uploaded
        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $filename = $image->hashName();
            $destinationPath = public_path('storage/barang');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $filename);
            if ($barang->barang_gambar != 'image.png') {
                $oldPath = public_path('storage/barang/' . $barang->barang_gambar);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $updateData['barang_gambar'] = $filename;
        } else if ($request->hapus_foto == '1') {
            if ($barang->barang_gambar != 'image.png') {
                $oldPath = public_path('storage/barang/' . $barang->barang_gambar);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $updateData['barang_gambar'] = 'image.png';
        }

        $barang->update($updateData);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_hapus(Request $request, $id)
    {
        try {
            $barang = BarangModel::find($id);
            if (!$barang) {
                return response()->json(['error' => 'Data tidak ditemukan!'], 404);
            }

            // Check if barang has related records in masuk or keluar
            $cekMasuk = BarangmasukModel::where('barang_kode', $barang->barang_kode)->count();
            $cekKeluar = BarangkeluarModel::where('barang_kode', $barang->barang_kode)->count();

            if ($cekMasuk > 0 || $cekKeluar > 0) {
                return response()->json(['error' => 'Data tidak bisa dihapus karena sudah ada riwayat transaksi!'], 400);
            }

            //delete image
            if ($barang->barang_gambar != 'image.png') {
                $oldPath = public_path('storage/barang/' . $barang->barang_gambar);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            //delete
            $barang->delete();

            return response()->json(['success' => 'Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function checkStok()
    {
        $data = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
                ->get();
        
        $lowStockItems = [];
        
        foreach ($data as $row) {
            $jmlmasuk = BarangmasukModel::where('barang_kode', '=', $row->barang_kode)->sum('bm_jumlah');
            $jmlkeluar = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode)
                ->where(function($q) {
                    $q->where('tbl_barangkeluar.bk_status', 'Dipinjam')
                      ->orWhere(function($q2) {
                          $q2->where('tbl_barangkeluar.bk_status', 'Selesai')
                             ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%');
                      });
                })
                ->sum('tbl_barangkeluar.bk_jumlah');
            $totalstok = $row->barang_stok + ($jmlmasuk - $jmlkeluar);
            
            if ($totalstok < 5) {
                $lowStockItems[] = [
                    'kode' => $row->barang_kode,
                    'nama' => $row->barang_nama,
                    'stok' => $totalstok
                ];
            }
        }
        
        return response()->json($lowStockItems);
    }
}
