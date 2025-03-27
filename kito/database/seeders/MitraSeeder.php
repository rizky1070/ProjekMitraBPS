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

            $mitraData[] = [
                'id_kecamatan' => rand(1, 4), // Random antara 1 hingga 4
                'id_kabupaten' => '16', // Random antara 1 hingga 4
                'id_provinsi' => '35', // Random antara 1 hingga 4
                'id_desa' => rand(1, 4), // Random antara 1 hingga 4
                'sobat_id' => 'S' . sprintf('%03d', $i), // Sobat ID: S001, S002, ..., S020
                'nama_lengkap' => 'Mitra ' . chr(64 + $i), // Nama mitra: Mitra A, Mitra B, ..., Mitra T
                'alamat_mitra' => 'Alamat Mitra ' . chr(64 + $i), // Alamat: Alamat Mitra A, Alamat Mitra B, ..., Alamat Mitra T
                'jenis_kelamin' => rand(1, 2), // Jenis kelamin: 1 (Laki-laki) atau 2 (Perempuan)
                'no_hp_mitra' => $faker->phoneNumber,
                'email_mitra' => 'mitra' . strtolower(chr(64 + $i)) . '@example.com',
                'tahun' => $tahun . '-' . sprintf('%02d', $bulan) . '-' . sprintf('%02d', $tanggal),            ];
        }

        DB::table('mitra')->insert($mitraData);
    }
}