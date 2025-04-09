<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MitraSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID'); // Indonesian locale
        $mitraData = [];

        for ($i = 1; $i <= 20; $i++) {
            $tahun = rand(2018, 2025); // Tahun acak antara 2018-2025
            $bulan = rand(1, 12);
            $tanggal = rand(1, 28); // Membatasi maksimal 28 untuk menghindari masalah bulan
            
            $startDate = $tahun . '-' . sprintf('%02d', $bulan) . '-' . sprintf('%02d', $tanggal);
            $endMonth = $bulan + rand(1, 2); // Tambah 1-2 bulan
            
            // Handle year overflow if end month exceeds December
            if ($endMonth > 12) {
                $endMonth -= 12;
                $tahun++;
            }
            
            $endDate = $tahun . '-' . sprintf('%02d', $endMonth) . '-' . sprintf('%02d', $tanggal);

            $mitraData[] = [
                'id_kecamatan' => rand(1, 4),
                'id_kabupaten' => '16',
                'id_provinsi' => '35',
                'id_desa' => rand(1, 4),
                'sobat_id' => 'S' . sprintf('%03d', $i),
                'nama_lengkap' => 'Mitra ' . chr(64 + $i),
                'alamat_mitra' => 'Alamat Mitra ' . chr(64 + $i),
                'jenis_kelamin' => rand(1, 2),
                'no_hp_mitra' => $faker->phoneNumber,
                'email_mitra' => 'mitra' . strtolower(chr(64 + $i)) . '@example.com',
                'tahun' => $startDate,
                'tahun_selesai' => $endDate,
            ];
        }

        DB::table('mitra')->insert($mitraData);
    }
}