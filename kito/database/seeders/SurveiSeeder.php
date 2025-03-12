<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveiSeeder extends Seeder
{
    public function run()
    {
        DB::table('survei')->insert([
            [
                'id_provinsi' => 1,
                'id_kabupaten' => 1,
                'id_kecamatan' => 1,
                'id_desa' => 1,
                'nama_survei' => 'Survei A',
                'lokasi_survei' => 'Lokasi A',
                'kro' => 'Kro A',
                'jadwal_kegiatan' => '2025-04-01',
                'status_survei' => 1
            ],
            [
                'id_provinsi' => 2,
                'id_kabupaten' => 2,
                'id_kecamatan' => 2,
                'id_desa' => 2,
                'nama_survei' => 'Survei B',
                'lokasi_survei' => 'Lokasi B',
                'kro' => 'Kro B',
                'jadwal_kegiatan' => '2025-04-02',
                'status_survei' => 2
            ],
            [
                'id_provinsi' => 3,
                'id_kabupaten' => 3,
                'id_kecamatan' => 3,
                'id_desa' => 3,
                'nama_survei' => 'Survei C',
                'lokasi_survei' => 'Lokasi C',
                'kro' => 'Kro C',
                'jadwal_kegiatan' => '2025-04-03',
                'status_survei' => 3
            ],
            [
                'id_provinsi' => 4,
                'id_kabupaten' => 4,
                'id_kecamatan' => 4,
                'id_desa' => 4,
                'nama_survei' => 'Survei D',
                'lokasi_survei' => 'Lokasi D',
                'kro' => 'Kro D',
                'jadwal_kegiatan' => '2025-04-04',
                'status_survei' => 3
            ],
            [
                'id_provinsi' => 4,
                'id_kabupaten' => 4,
                'id_kecamatan' => 4,
                'id_desa' => 4,
                'nama_survei' => 'Survei E',
                'lokasi_survei' => 'Lokasi E',
                'kro' => 'Kro D',
                'jadwal_kegiatan' => '2024-04-04',
                'status_survei' => 3
            ],
        ]);
    }
}
