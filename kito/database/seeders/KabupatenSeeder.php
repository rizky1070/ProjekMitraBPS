<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KabupatenSeeder extends Seeder
{
    public function run()
    {
        DB::table('kabupaten')->insert([
            ['kode_kabupaten' => 'K001', 'nama_kabupaten' => 'Kabupaten A'],
            ['kode_kabupaten' => 'K002', 'nama_kabupaten' => 'Kabupaten B'],
            ['kode_kabupaten' => 'K003', 'nama_kabupaten' => 'Kabupaten C'],
            ['kode_kabupaten' => 'K004', 'nama_kabupaten' => 'Kabupaten D'],
        ]);
    }
}
