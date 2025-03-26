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
                'kode_kabupaten' => 'K001', 
                'nama_kabupaten' => 'Kabupaten A',
                'id_provinsi' => 1 // Sesuaikan dengan ID provinsi yang ada
            ],
            [
                'kode_kabupaten' => 'K002', 
                'nama_kabupaten' => 'Kabupaten B',
                'id_provinsi' => 1
            ],
            [
                'kode_kabupaten' => 'K003', 
                'nama_kabupaten' => 'Kabupaten C',
                'id_provinsi' => 2
            ],
            [
                'kode_kabupaten' => 'K004', 
                'nama_kabupaten' => 'Kabupaten D',
                'id_provinsi' => 2
            ],
        ]);
    }
}