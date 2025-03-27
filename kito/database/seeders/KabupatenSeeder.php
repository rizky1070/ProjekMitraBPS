<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KabupatenSeeder extends Seeder
{
    public function run()
    {
        DB::table('kabupaten')->insert([
            [
                'id_kabupaten' => '16',
                'kode_kabupaten' => 'K001', 
                'nama_kabupaten' => 'Mojokerto',
                'id_provinsi' => 35 // Sesuaikan dengan ID provinsi yang ada
            ]
        ]);
    }
}