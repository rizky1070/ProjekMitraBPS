<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveiSeeder extends Seeder
{
    public function run()
    {
        $surveiData = [];

        for ($i = 1; $i <= 20; $i++) {
            $surveiData[] = [
                'id_provinsi' => rand(1, 4), // Random antara 1 hingga 4
                'id_kabupaten' => rand(1, 4), // Random antara 1 hingga 4
                'id_kecamatan' => rand(1, 4), // Random antara 1 hingga 4
                'id_desa' => rand(1, 4), // Random antara 1 hingga 4
                'nama_survei' => 'Survei ' . chr(64 + $i), // Nama survei: Survei A, Survei B, ..., Survei T
                'lokasi_survei' => 'Lokasi ' . chr(64 + $i), // Lokasi: Lokasi A, Lokasi B, ..., Lokasi T
                'kro' => 'Kro ' . chr(64 + $i), // KRO: Kro A, Kro B, ..., Kro T
                'jadwal_kegiatan' => '2025-' . sprintf('%02d', rand(1, 12)) . '-' . sprintf('%02d', rand(1, 28)), // Tanggal acak di tahun 2025
                'status_survei' => rand(1, 3) // Status survei: 1, 2, atau 3
            ];
        }

        DB::table('survei')->insert($surveiData);
    }
}