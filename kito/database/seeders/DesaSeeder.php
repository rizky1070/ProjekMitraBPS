<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesaSeeder extends Seeder
{
    public function run()
    {
        DB::table('desa')->insert([
            [
                'kode_desa' => 'D001', 
                'nama_desa' => 'Desa A',
                'id_kecamatan' => 1 // Sesuaikan dengan ID kecamatan yang ada
            ],
            [
                'kode_desa' => 'D002', 
                'nama_desa' => 'Desa B',
                'id_kecamatan' => 1
            ],
            [
                'kode_desa' => 'D003', 
                'nama_desa' => 'Desa C',
                'id_kecamatan' => 2
            ],
            [
                'kode_desa' => 'D004', 
                'nama_desa' => 'Desa D',
                'id_kecamatan' => 3
            ],
        ]);
    }
}