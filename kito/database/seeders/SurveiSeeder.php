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
            $tahun = rand(2018, 2025);
            $bulan = rand(1, 12);
            $tanggal = rand(1, 28);
            $honor = rand(10000, 50000);
            $vol = rand(1, 10);
            $total_honor = $vol * $honor; // Menghitung total honor
            
            $surveiData[] = [
                'id_provinsi' => '35',
                'id_kabupaten' => '16',
                'id_kecamatan' => rand(1, 4),
                'id_desa' => rand(1, 4),
                'nama_survei' => 'Survei ' . chr(64 + $i),
                'lokasi_survei' => 'Lokasi ' . chr(64 + $i),
                'kro' => 'Kro ' . chr(64 + $i),
                'jadwal_kegiatan' => $tahun . '-' . sprintf('%02d', $bulan) . '-' . sprintf('%02d', $tanggal),            
                'status_survei' => rand(1, 3),
                'tim' => 'tim ' . chr(64 + $i),
                'honor' => $honor,
                'vol' => $vol,
                'total_honor' => $total_honor // Menambahkan total_honor
            ];
        }

        DB::table('survei')->insert($surveiData);
    }
}