<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

// Cek role_id untuk setiap role
echo "=== tbl_role ===\n";
foreach(DB::table('tbl_role')->get() as $r)
    echo "  role_id:{$r->role_id} | {$r->role_title}\n";

// User yang mungkin salah role
echo "\n=== tbl_user current state ===\n";
foreach(DB::table('tbl_user')
    ->join('tbl_role','tbl_role.role_id','=','tbl_user.role_id')
    ->select('tbl_user.*','tbl_role.role_title')
    ->get() as $u) {
    echo "  user_id:{$u->user_id} | role:{$u->role_id}({$u->role_title}) | nama:{$u->user_nmlengkap} | username:{$u->user_nama} | teknisi_sn:".($u->teknisi_sn ?? 'NULL')."\n";
}

// Fix: user "Staff Gudang" dengan username 'staff' seharusnya role 2 (Admin Gudang), bukan 3
$staffUser = DB::table('tbl_user')->where('user_nama', 'staff')->first();
if ($staffUser && $staffUser->role_id == 3) {
    DB::table('tbl_user')->where('user_id', $staffUser->user_id)->update(['role_id' => 2]);
    echo "\n✓ Fixed: user '{$staffUser->user_nama}' dipindah dari role 3 ke role 2 (Admin Gudang)\n";
} else {
    echo "\n- User 'staff' sudah benar atau tidak ditemukan\n";
}

// Pastikan teknisi punya SN
$teknisis = DB::table('tbl_user')->where('role_id', 3)->get();
foreach ($teknisis as $t) {
    if (empty($t->teknisi_sn)) {
        $sn = 'TK-' . strtoupper(substr(md5($t->user_id . $t->user_nama), 0, 6));
        DB::table('tbl_user')->where('user_id', $t->user_id)->update(['teknisi_sn' => $sn]);
        echo "✓ Generated SN for teknisi {$t->user_nmlengkap}: {$sn}\n";
    } else {
        echo "- Teknisi {$t->user_nmlengkap} sudah punya SN: {$t->teknisi_sn}\n";
    }
}

echo "\n=== tbl_user AFTER FIX ===\n";
foreach(DB::table('tbl_user')
    ->join('tbl_role','tbl_role.role_id','=','tbl_user.role_id')
    ->select('tbl_user.*','tbl_role.role_title')
    ->get() as $u) {
    echo "  [{$u->role_title}] {$u->user_nmlengkap} | username:{$u->user_nama} | SN:".($u->teknisi_sn ?? '-')."\n";
}
