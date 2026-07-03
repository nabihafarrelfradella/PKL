<?php

use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\BarangkeluarController;
use App\Http\Controllers\Admin\BarangmasukController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JenisBarangController;
use App\Http\Controllers\Admin\LapBarangKeluarController;
use App\Http\Controllers\Admin\LapBarangMasukController;
use App\Http\Controllers\Admin\LapStokBarangController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MerkController;
use App\Http\Controllers\Admin\BarangTrackingController;
use App\Http\Controllers\Master\AksesController;
use App\Http\Controllers\Master\AppreanceController;
use App\Http\Controllers\Master\AuditController;
use App\Http\Controllers\Master\MenuController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// login admin
Route::middleware(['preventBackHistory'])->group(function () {
    Route::get('/admin/login', [LoginController::class, 'index'])->name('login');
    Route::post('/admin/proseslogin', [LoginController::class, 'proseslogin']);
    Route::get('/admin/logout', [LoginController::class, 'logout']);
});

// admin
Route::group(['middleware' => 'userlogin'], function () {

    // Profile
    Route::get('/admin/profile/{user}', [UserController::class, 'profile']);
    Route::post('/admin/updatePassword/{user}', [UserController::class, 'updatePassword']);
    Route::post('/admin/updateProfile/{user}', [UserController::class, 'updateProfile']);

    Route::middleware(['checkRoleUser:/dashboard,menu'])->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/admin', [DashboardController::class, 'index']);
        Route::get('/admin/dashboard', [DashboardController::class, 'index']);
        Route::get('/admin/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
        Route::post('/admin/dashboard/cekResi', [DashboardController::class, 'cekResi'])->name('dashboard.cekResi');
    });

    Route::middleware(['checkRoleUser:/jenisbarang,submenu'])->group(function () {
        // Jenis Barang
        Route::get('/admin/jenisbarang', [JenisBarangController::class, 'index']);
        Route::get('/admin/jenisbarang/show/', [JenisBarangController::class, 'show'])->name('jenisbarang.getjenisbarang');
        Route::post('/admin/jenisbarang/proses_tambah/', [JenisBarangController::class, 'proses_tambah'])->name('jenisbarang.store');
        Route::post('/admin/jenisbarang/proses_ubah/{jenisbarang}', [JenisBarangController::class, 'proses_ubah']);
        Route::post('/admin/jenisbarang/proses_hapus/{jenisbarang}', [JenisBarangController::class, 'proses_hapus']);
    });


    Route::middleware(['checkRoleUser:/merk,submenu'])->group(function () {
        // Merk
        Route::resource('/admin/merk', \App\Http\Controllers\Admin\MerkController::class);
        Route::get('/admin/merk/show/', [MerkController::class, 'show'])->name('merk.getmerk');
        Route::post('/admin/merk/proses_tambah/', [MerkController::class, 'proses_tambah'])->name('merk.store');
        Route::post('/admin/merk/proses_ubah/{merk}', [MerkController::class, 'proses_ubah']);
        Route::post('/admin/merk/proses_hapus/{merk}', [MerkController::class, 'proses_hapus']);
    });

    Route::middleware(['checkRoleUser:/barang,submenu'])->group(function () {
        // Barang
        Route::get('/admin/barang', [BarangController::class, 'index']);
        Route::get('/admin/barang/show/', [BarangController::class, 'show'])->name('barang.getbarang');
        Route::get('/admin/barang/autocomplete', [BarangController::class, 'autocomplete'])->name('barang.autocomplete');
        Route::post('/admin/barang/proses_tambah/', [BarangController::class, 'proses_tambah'])->name('barang.store');
        Route::post('/admin/barang/proses_ubah/{barang}', [BarangController::class, 'proses_ubah']);
        Route::post('/admin/barang/proses_hapus/{id}', [BarangController::class, 'proses_hapus']);
        Route::get('/admin/barang/check-stok', [BarangController::class, 'checkStok'])->name('barang.checkStok');
    });

    Route::middleware(['checkRoleUser:/customer,menu'])->group(function () {
        // Customer
        Route::resource('/admin/customer', \App\Http\Controllers\Admin\CustomerController::class);
        Route::get('/admin/customer/show/', [CustomerController::class, 'show'])->name('customer.getcustomer');
        Route::post('/admin/customer/proses_tambah/', [CustomerController::class, 'proses_tambah'])->name('customer.store');
        Route::post('/admin/customer/proses_ubah/{customer}', [CustomerController::class, 'proses_ubah']);
        Route::post('/admin/customer/proses_hapus/{customer}', [CustomerController::class, 'proses_hapus']);
    });

    Route::middleware(['checkRoleUser:/barang-masuk,submenu'])->group(function () {
        // Barang Masuk
        Route::resource('/admin/barang-masuk', \App\Http\Controllers\Admin\BarangmasukController::class);
        Route::get('/admin/barang-masuk/show/', [BarangmasukController::class, 'show'])->name('barang-masuk.getbarang-masuk');
        Route::post('/admin/barang-masuk/proses_tambah/', [BarangmasukController::class, 'proses_tambah'])->name('barang-masuk.store');
        Route::post('/admin/barang-masuk/proses_ubah/{barangmasuk}', [BarangmasukController::class, 'proses_ubah']);
        Route::post('/admin/barang-masuk/proses_hapus/{barangmasuk}', [BarangmasukController::class, 'proses_hapus']);
        Route::post('/admin/barang-masuk/hapus-kelompok', [BarangmasukController::class, 'hapus_kelompok']);
        Route::post('/admin/barang-masuk/detail-sn/batch', [BarangmasukController::class, 'detail_sn_batch']);
        Route::get('/admin/barang-masuk/detail-sn/all/{barang_kode}', [BarangmasukController::class, 'detailSN'])->name('barang-masuk.detailSN');
        Route::get('/admin/barang/getbarang/{id}', [BarangController::class, 'getbarang']);
        Route::get('/admin/barang/getunit/{id}', [BarangController::class, 'getunit']);
        Route::get('/admin/barang/listbarang/{param}', [BarangController::class, 'listbarang']);
    });

    Route::middleware(['checkRoleUser:/barang-keluar,submenu'])->group(function () {
        // Barang Keluar
        Route::resource('/admin/barang-keluar', \App\Http\Controllers\Admin\BarangkeluarController::class);
        Route::get('/admin/barang-keluar/show/', [BarangkeluarController::class, 'show'])->name('barang-keluar.getbarang-keluar');
        Route::post('/admin/barang-keluar/resolve-map-link', [BarangkeluarController::class, 'resolveMapLink'])->name('barang-keluar.resolve-map-link');
        Route::post('/admin/barang-keluar/proses_tambah/', [BarangkeluarController::class, 'proses_tambah'])->name('barang-keluar.store');
        Route::post('/admin/barang-keluar/proses_ubah/{barangkeluar}', [BarangkeluarController::class, 'proses_ubah']);
        Route::post('/admin/barang-keluar/proses_hapus/{barangkeluar}', [BarangkeluarController::class, 'proses_hapus']);
        Route::post('/admin/barang-keluar/hapus-transaksi/{bk_kode}', [BarangkeluarController::class, 'hapusTransaksi']);
        Route::post('/admin/barang-keluar/proses_kembali/{barangkeluar}', [BarangkeluarController::class, 'proses_kembali']);
        Route::post('/admin/barang-keluar/terima_pinjam/{barangkeluar}', [BarangkeluarController::class, 'terima_pinjam']);
        Route::post('/admin/barang-keluar/tolak_pinjam/{barangkeluar}', [BarangkeluarController::class, 'tolak_pinjam']);
        Route::post('/admin/barang-keluar/terima_kembali/{barangkeluar}', [BarangkeluarController::class, 'terima_kembali']);
        Route::post('/admin/barang-keluar/tolak_kembali/{barangkeluar}', [BarangkeluarController::class, 'tolak_kembali']);
        Route::get('/admin/barang-keluar/detail-sn/all/{barang_kode}', [BarangkeluarController::class, 'detailSN'])->name('barang-keluar.detailSN');
        Route::get('/admin/barang/get-available-sn/{barang_kode}', [BarangkeluarController::class, 'getAvailableSN']);
        // Teknisi lookup (needed for form dropdowns by Owner & Admin Gudang)
        Route::get('/admin/user-management/teknisi/get/{id}', [BarangkeluarController::class, 'getTeknisi']);
        Route::get('/admin/user-management/teknisi/get-by-sn/{sn}', [BarangkeluarController::class, 'getTeknisiBySn']);
        // Barang lookup routes (also needed here for Teknisi who may not have barang-masuk access)
        Route::get('/admin/barang/getbarang/{id}', [BarangController::class, 'getbarang']);
        Route::get('/admin/barang/getunit/{id}', [BarangController::class, 'getunit']);
        Route::get('/admin/barang/listbarang/{param}', [BarangController::class, 'listbarang']);
    });

    Route::middleware(['checkRoleUser:/lap-barang-masuk,submenu'])->group(function () {
        // Laporan Barang Masuk
        Route::resource('/admin/lap-barang-masuk', \App\Http\Controllers\Admin\LapBarangMasukController::class);
        Route::get('/admin/lapbarangmasuk/print/', [LapBarangMasukController::class, 'print'])->name('lap-bm.print');
        Route::get('/admin/lapbarangmasuk/pdf/', [LapBarangMasukController::class, 'pdf'])->name('lap-bm.pdf');
        Route::get('/admin/lapbarangmasuk/excel/', [LapBarangMasukController::class, 'excel'])->name('lap-bm.excel');
        Route::get('/admin/lap-barang-masuk/show/', [LapBarangMasukController::class, 'show'])->name('lap-bm.getlap-bm');
    });

    Route::middleware(['checkRoleUser:/lap-barang-keluar,submenu'])->group(function () {
        // Laporan Barang Keluar
        Route::resource('/admin/lap-barang-keluar', \App\Http\Controllers\Admin\LapBarangKeluarController::class);
        Route::get('/admin/lapbarangkeluar/print/', [LapBarangKeluarController::class, 'print'])->name('lap-bk.print');
        Route::get('/admin/lapbarangkeluar/pdf/', [LapBarangKeluarController::class, 'pdf'])->name('lap-bk.pdf');
        Route::get('/admin/lapbarangkeluar/excel/', [LapBarangKeluarController::class, 'excel'])->name('lap-bk.excel');
        Route::get('/admin/lap-barang-keluar/show/', [LapBarangKeluarController::class, 'show'])->name('lap-bk.getlap-bk');
    });

    Route::middleware(['checkRoleUser:/lap-stok-barang,submenu'])->group(function () {
        // Laporan Stok Barang
        Route::resource('/admin/lap-stok-barang', \App\Http\Controllers\Admin\LapStokBarangController::class);
        Route::get('/admin/lapstokbarang/print/', [LapStokBarangController::class, 'print'])->name('lap-sb.print');
        Route::get('/admin/lapstokbarang/pdf/', [LapStokBarangController::class, 'pdf'])->name('lap-sb.pdf');
        Route::get('/admin/lapstokbarang/excel/', [LapStokBarangController::class, 'excel'])->name('lap-sb.excel');
        Route::get('/admin/lap-stok-barang/show/', [LapStokBarangController::class, 'show'])->name('lap-sb.getlap-sb');
    });

    // Barang Tracking — Owner & Admin Gudang saja (role_id 1 & 2)
    Route::middleware(['checkRoleUser:/barang-tracking,submenu'])->group(function () {
        Route::get('/admin/barang-tracking', [BarangTrackingController::class, 'index'])->name('barang-tracking.index');
        Route::get('/admin/barang-tracking/show', [BarangTrackingController::class, 'show'])->name('barang-tracking.show');
    });


    // =========================================================
    // USER MANAGEMENT — Hanya Owner (role_id == 1)
    // =========================================================
    Route::middleware(['checkOwnerOnly'])->group(function () {

        // Audit Trail
        #Route::get('/admin/audit', [AuditController::class, 'index'])->name('audit.index');
        #Route::get('/admin/audit/show', [AuditController::class, 'show'])->name('audit.getaudit');

        // Role Management
        Route::resource('/admin/role', \App\Http\Controllers\Master\RoleController::class);
        Route::get('/admin/role/show/', [RoleController::class, 'show'])->name('role.getrole');
        Route::post('/admin/role/hapus', [RoleController::class, 'hapus']);

        // User Management (generic — full CRUD for Owner)
        Route::resource('/admin/user', \App\Http\Controllers\Master\UserController::class);
        Route::get('/admin/user/show/', [UserController::class, 'show'])->name('user.getuser');
        Route::post('/admin/user/hapus', [UserController::class, 'hapus']);

        // Teknisi CRUD — Owner manages Pegawai Teknisi accounts
        Route::get('/admin/user-management/teknisi', [UserController::class, 'teknisiIndex'])->name('user-mgmt.teknisi');
        Route::get('/admin/user-management/teknisi/show', [UserController::class, 'teknisiShow'])->name('user-mgmt.teknisi.show');
        Route::post('/admin/user-management/teknisi/store', [UserController::class, 'teknisiStore'])->name('user-mgmt.teknisi.store');
        Route::post('/admin/user-management/teknisi/update/{user}', [UserController::class, 'teknisiUpdate'])->name('user-mgmt.teknisi.update');
        Route::post('/admin/user-management/teknisi/destroy/{user}', [UserController::class, 'teknisiDestroy'])->name('user-mgmt.teknisi.destroy');

        // Admin Gudang — View + Edit only (1 akun, tidak bisa ditambah/dihapus)
        Route::get('/admin/user-management/admin-gudang', [UserController::class, 'adminGudangIndex'])->name('user-mgmt.admin-gudang');
        Route::post('/admin/user-management/admin-gudang/update/{user}', [UserController::class, 'adminGudangUpdate'])->name('user-mgmt.admin-gudang.update');

        // Access Control — Interactive toggle RBAC
        Route::get('/admin/user-management/access-control', [UserController::class, 'accessControl'])->name('user-mgmt.access-control');
        Route::post('/admin/user-management/access-control/toggle', [UserController::class, 'accessControlToggle'])->name('user-mgmt.access-control.toggle');

        // Akses
        Route::get('/admin/akses/{role}', [AksesController::class, 'index']);
        Route::get('/admin/akses/addAkses/{idmenu}/{idrole}/{type}/{akses}', [AksesController::class, 'addAkses']);
        Route::get('/admin/akses/removeAkses/{idmenu}/{idrole}/{type}/{akses}', [AksesController::class, 'removeAkses']);
        Route::get('/admin/akses/setAll/{role}', [AksesController::class, 'setAllAkses']);
        Route::get('/admin/akses/unsetAll/{role}', [AksesController::class, 'unsetAllAkses']);
    });

    // Notifikasi (untuk Owner & Admin Gudang, tidak dibatasi checkOwnerOnly)
    Route::get('/admin/notifikasi', [\App\Http\Controllers\Admin\NotifikasiController::class, 'getNotifikasi'])->name('notifikasi.get');
    Route::post('/admin/notifikasi/read/{id}', [\App\Http\Controllers\Admin\NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/admin/notifikasi/read-all', [\App\Http\Controllers\Admin\NotifikasiController::class, 'markAllRead'])->name('notifikasi.read-all');

});
