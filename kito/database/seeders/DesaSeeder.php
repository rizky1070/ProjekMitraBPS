<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesaSeeder extends Seeder
{
    public function run()
    {
        DB::table('desa')->insert([
            ['kode_desa' => 'D001', 'nama_desa' => 'Desa A'],
            ['kode_desa' => 'D002', 'nama_desa' => 'Desa B'],
            ['kode_desa' => 'D003', 'nama_desa' => 'Desa C'],
            ['kode_desa' => 'D004', 'nama_desa' => 'Desa D'],
        ]);
    }
}
