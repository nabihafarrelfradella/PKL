<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed 3 akun default sistem Alfatindo.
     * Password default: 12345678 (md5 — sesuai sistem existing)
     */
    public function run()
    {
        DB::table('tbl_user')->insert([
            'role_id'        => 1,
            'user_nmlengkap' => 'Owner Alfatindo',
            'user_nama'      => 'owner',
            'user_email'     => 'owner@alfatindo.com',
            'user_foto'      => 'undraw_profile.svg',
            'user_password'  => md5('12345678'),
            'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'     => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('tbl_user')->insert([
            'role_id'        => 2,
            'user_nmlengkap' => 'Admin Gudang',
            'user_nama'      => 'admingudang',
            'user_email'     => 'admingudang@alfatindo.com',
            'user_foto'      => 'undraw_profile.svg',
            'user_password'  => md5('12345678'),
            'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'     => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('tbl_user')->insert([
            'role_id'        => 3,
            'user_nmlengkap' => 'Teknisi Contoh',
            'user_nama'      => 'teknisi1',
            'user_email'     => 'teknisi1@alfatindo.com',
            'user_foto'      => 'undraw_profile.svg',
            'user_password'  => md5('12345678'),
            'jenis_kelamin'  => 'M',
            'tanggal_lahir'  => '1995-01-15',
            'teknisi_sn'     => 'M-15-1995',
            'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'     => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
