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
                'kode_kecamatan' => 'KC1', 
                'nama_kecamatan' => 'Kecamatan A',
                'id_kabupaten' => 16 // Sesuaikan dengan ID kabupaten yang ada
            ],
            [
                'kode_kecamatan' => 'KC2', 
                'nama_kecamatan' => 'Kecamatan B',
                'id_kabupaten' => 16
            ],
            [
                'kode_kecamatan' => 'KC3', 
                'nama_kecamatan' => 'Kecamatan C',
                'id_kabupaten' => 16
            ],
            [
                'kode_kecamatan' => 'KC4', 
                'nama_kecamatan' => 'Kecamatan D',
                'id_kabupaten' => 16
            ],
        ]);
    }
}