<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    public function run()
    {
        DB::table('kecamatan')->insert([
            ['kode_kecamatan' => 'KC001', 'nama_kecamatan' => 'Kecamatan A'],
            ['kode_kecamatan' => 'KC002', 'nama_kecamatan' => 'Kecamatan B'],
            ['kode_kecamatan' => 'KC003', 'nama_kecamatan' => 'Kecamatan C'],
            ['kode_kecamatan' => 'KC004', 'nama_kecamatan' => 'Kecamatan D'],
        ]);
    }
}
