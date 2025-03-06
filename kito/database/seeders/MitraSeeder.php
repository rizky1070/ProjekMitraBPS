<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraSeeder extends Seeder
{
    public function run()
    {
        DB::table('mitra')->insert([
            [
                'id_kecamatan' => 1,
                'id_kabupaten' => 1,
                'id_provinsi' => 1,
                'id_desa' => 1,
                'sobat_id' => 'S001',
                'nama_lengkap' => 'Mitra A',
                'alamat_mitra' => 'Alamat Mitra A',
                'jenis_kelamin' => 1
            ],
            [
                'id_kecamatan' => 2,
                'id_kabupaten' => 2,
                'id_provinsi' => 2,
                'id_desa' => 2,
                'sobat_id' => 'S002',
                'nama_lengkap' => 'Mitra B',
                'alamat_mitra' => 'Alamat Mitra B',
                'jenis_kelamin' => 2
            ],
            [
                'id_kecamatan' => 3,
                'id_kabupaten' => 3,
                'id_provinsi' => 3,
                'id_desa' => 3,
                'sobat_id' => 'S003',
                'nama_lengkap' => 'Mitra C',
                'alamat_mitra' => 'Alamat Mitra C',
                'jenis_kelamin' => 1
            ],
        ]);
    }
}
