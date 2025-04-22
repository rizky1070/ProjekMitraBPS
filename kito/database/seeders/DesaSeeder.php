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
                'kode_desa' => 'D01', 
                'nama_desa' => 'Desa A',
                'id_kecamatan' => 1 // Sesuaikan dengan ID kecamatan yang ada
            ],
            [
                'kode_desa' => 'D02', 
                'nama_desa' => 'Desa B',
                'id_kecamatan' => 1
            ],
            [
                'kode_desa' => 'D03', 
                'nama_desa' => 'Desa C',
                'id_kecamatan' => 2
            ],
            [
                'kode_desa' => 'D04', 
                'nama_desa' => 'Desa D',
                'id_kecamatan' => 2
            ],
            [
                'kode_desa' => 'D05', 
                'nama_desa' => 'Desa E',
                'id_kecamatan' => 3
            ],
            [
                'kode_desa' => 'D06', 
                'nama_desa' => 'Desa F',
                'id_kecamatan' => 3
            ],
            [
                'kode_desa' => 'D07', 
                'nama_desa' => 'Desa G',
                'id_kecamatan' => 4
            ],
            [
                'kode_desa' => 'D08', 
                'nama_desa' => 'Desa H',
                'id_kecamatan' => 4
            ],
        ]);
    }
}