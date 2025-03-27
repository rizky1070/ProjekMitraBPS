<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinsiSeeder extends Seeder
{
    public function run()
    {
        DB::table('provinsi')->insert([
            ['id_provinsi' => '35', 'kode_provinsi' => 'P001', 'nama_provinsi' => 'Jawa Timur']
        ]);
    }
}
