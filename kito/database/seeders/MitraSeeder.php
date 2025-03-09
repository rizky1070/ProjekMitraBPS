<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraSeeder extends Seeder
{
    public function run()
    {
        DB::table('mitra')->insert([
            // Data Mitra 1
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
            // Data Mitra 2
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
            // Data Mitra 3
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
            // Data Mitra 4
            [
                'id_kecamatan' => 4,
                'id_kabupaten' => 4,
                'id_provinsi' => 4,
                'id_desa' => 4,
                'sobat_id' => 'S004',
                'nama_lengkap' => 'Mitra D',
                'alamat_mitra' => 'Alamat Mitra D',
                'jenis_kelamin' => 2
            ],
            // Data Mitra 5
            [
                'id_kecamatan' => 3,
                'id_kabupaten' => 3,
                'id_provinsi' => 3,
                'id_desa' => 3,
                'sobat_id' => 'S005',
                'nama_lengkap' => 'Mitra E',
                'alamat_mitra' => 'Alamat Mitra E',
                'jenis_kelamin' => 1
            ],
            // Data Mitra 6
            [
                'id_kecamatan' => 4,
                'id_kabupaten' => 4,
                'id_provinsi' => 4,
                'id_desa' => 4,
                'sobat_id' => 'S006',
                'nama_lengkap' => 'Mitra F',
                'alamat_mitra' => 'Alamat Mitra F',
                'jenis_kelamin' => 2
            ],
            // Data Mitra 7
            [
                'id_kecamatan' => 1,
                'id_kabupaten' => 1,
                'id_provinsi' => 1,
                'id_desa' => 1,
                'sobat_id' => 'S007',
                'nama_lengkap' => 'Mitra G',
                'alamat_mitra' => 'Alamat Mitra G',
                'jenis_kelamin' => 1
            ],
            // Data Mitra 8
            [
                'id_kecamatan' => 2,
                'id_kabupaten' => 2,
                'id_provinsi' => 2,
                'id_desa' => 2,
                'sobat_id' => 'S008',
                'nama_lengkap' => 'Mitra H',
                'alamat_mitra' => 'Alamat Mitra H',
                'jenis_kelamin' => 2
            ],
            // Data Mitra 9
            [
                'id_kecamatan' => 3,
                'id_kabupaten' => 3,
                'id_provinsi' => 3,
                'id_desa' => 3,
                'sobat_id' => 'S009',
                'nama_lengkap' => 'Mitra I',
                'alamat_mitra' => 'Alamat Mitra I',
                'jenis_kelamin' => 1
            ],
            // Data Mitra 10
            [
                'id_kecamatan' => 4,
                'id_kabupaten' => 4,
                'id_provinsi' => 4,
                'id_desa' => 4,
                'sobat_id' => 'S010',
                'nama_lengkap' => 'Mitra J',
                'alamat_mitra' => 'Alamat Mitra J',
                'jenis_kelamin' => 2
            ]
        ]);
    }
}

