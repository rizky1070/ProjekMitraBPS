<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    public function run()
    {
        DB::table('kecamatan')->insert([
            [
                'kode_kecamatan' => 'KC001', 
                'nama_kecamatan' => 'Kecamatan A',
                'id_kabupaten' => 16 // Sesuaikan dengan ID kabupaten yang ada
            ],
            [
                'kode_kecamatan' => 'KC002', 
                'nama_kecamatan' => 'Kecamatan B',
                'id_kabupaten' => 16
            ],
            [
                'kode_kecamatan' => 'KC003', 
                'nama_kecamatan' => 'Kecamatan C',
                'id_kabupaten' => 16
            ],
            [
                'kode_kecamatan' => 'KC004', 
                'nama_kecamatan' => 'Kecamatan D',
                'id_kabupaten' => 16
            ],
        ]);
    }
}