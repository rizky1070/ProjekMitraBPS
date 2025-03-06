<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinsiSeeder extends Seeder
{
    public function run()
    {
        DB::table('provinsi')->insert([
            ['kode_provinsi' => 'P001', 'nama_provinsi' => 'Provinsi A'],
            ['kode_provinsi' => 'P002', 'nama_provinsi' => 'Provinsi B'],
            ['kode_provinsi' => 'P003', 'nama_provinsi' => 'Provinsi C'],
            ['kode_provinsi' => 'P004', 'nama_provinsi' => 'Provinsi D'],
        ]);
    }
}
