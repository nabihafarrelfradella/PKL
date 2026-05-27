<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

echo "=== tbl_user columns ===\n";
foreach(DB::select('SHOW COLUMNS FROM tbl_user') as $c)
    echo "  {$c->Field} | {$c->Type} | Null:{$c->Null}\n";

echo "\n=== tbl_user data ===\n";
foreach(DB::table('tbl_user')
    ->join('tbl_role','tbl_role.role_id','=','tbl_user.role_id')
    ->select('tbl_user.*','tbl_role.role_title')
    ->get() as $u)
    echo "  [{$u->role_title}] {$u->user_nmlengkap} | {$u->user_nama}\n";
