<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosisiMitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posisiMitra = [
            ['nama_posisi' => 'Programmer', 'rate_honor' => 10000],
            ['nama_posisi' => 'UI/UX Designer', 'rate_honor' => 20000],
            ['nama_posisi' => 'Data Analyst', 'rate_honor' => 30000],
            ['nama_posisi' => 'System Administrator', 'rate_honor' => 40000],
        ];

        // Insert data ke tabel
        DB::table('posisi_mitra')->insert($posisiMitra);
    }
}
