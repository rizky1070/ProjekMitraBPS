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
            ['nama_posisi' => 'Programmer' ],
            ['nama_posisi' => 'UI/UX Designer' ],
            ['nama_posisi' => 'Data Analyst'],
            ['nama_posisi' => 'System Administrator'],
            ['nama_posisi' => 'Petugas Pendataan Lapangan (PPL Survei)'],
        ];

        // Insert data ke tabel
        DB::table('posisi_mitra')->insert($posisiMitra);
    }
}