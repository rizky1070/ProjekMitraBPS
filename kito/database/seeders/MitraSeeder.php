<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraSeeder extends Seeder
{
    public function run()
    {
        $mitraData = [];

        for ($i = 1; $i <= 20; $i++) {
            $mitraData[] = [
                'id_kecamatan' => rand(1, 4), // Random antara 1 hingga 4
                'id_kabupaten' => rand(1, 4), // Random antara 1 hingga 4
                'id_provinsi' => rand(1, 4), // Random antara 1 hingga 4
                'id_desa' => rand(1, 4), // Random antara 1 hingga 4
                'sobat_id' => 'S' . sprintf('%03d', $i), // Sobat ID: S001, S002, ..., S020
                'nama_lengkap' => 'Mitra ' . chr(64 + $i), // Nama mitra: Mitra A, Mitra B, ..., Mitra T
                'alamat_mitra' => 'Alamat Mitra ' . chr(64 + $i), // Alamat: Alamat Mitra A, Alamat Mitra B, ..., Alamat Mitra T
                'jenis_kelamin' => rand(1, 2) // Jenis kelamin: 1 (Laki-laki) atau 2 (Perempuan)
            ];
        }

        DB::table('mitra')->insert($mitraData);
    }
}