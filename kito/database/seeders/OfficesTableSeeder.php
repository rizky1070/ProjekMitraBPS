<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('offices')->insert([
            [
                'id' => 1,
                'category_id' => 2,
                'name' => 'Progress Pemutakhiran Sakernas',
                'link' => 'https://docs.google.com/spreadsheets/d/1MJ5gvlr3bcVmcP6GEcAa7jSpknzR9HXyE6G6LQv671Q/edit#gid=282287300',
                'created_at' => '2022-07-20 17:43:32',
                'updated_at' => '2024-01-10 23:15:25',
                'status' => 0,
                'priority' => 0
            ],
            [
                'id' => 5,
                'category_id' => 3,
                'name' => 'MOHAK - Monitoring Pelaporan LHKASN dan LHKPN',
                'link' => 'https://docs.google.com/forms/d/e/1FAIpQLSdwrGs6lnmJ1hmiPV0pDCq7j9V2-HpcgdpCp8yAcaz2_fK7eg/viewform',
                'created_at' => '2022-07-21 20:58:43',
                'updated_at' => '2022-07-21 20:58:43',
                'status' => 1,
                'priority' => 0
            ],
            [
                'id' => 7,
                'category_id' => 3,
                'name' => 'Jadwal Petugas Apel',
                'link' => 'https://docs.google.com/spreadsheets/d/1XzxrdwEPRPFwQdAIFr6IA6z2UzajQp3B6AQQpR7Y2z8/edit#gid=1877801027',
                'created_at' => '2022-07-22 03:14:57',
                'updated_at' => '2025-02-25 03:14:36',
                'status' => 0,
                'priority' => 0
            ],
            [
                'id' => 8,
                'category_id' => 3,
                'name' => 'Rekap Keterlambatan dan KJK',
                'link' => 'https://docs.google.com/spreadsheets/d/1gctMUBcjDe2vCv7ZY5oZ-qcK41RmS5qk/edit?usp=sharing&ouid=105973754965351275286&rtpof=true&sd=true',
                'created_at' => '2022-07-22 03:15:28',
                'updated_at' => '2024-04-03 20:16:34',
                'status' => 0,
                'priority' => 0
            ],
            [
                'id' => 9,
                'category_id' => 3,
                'name' => 'Upload Sertifikat',
                'link' => 's.bps.go.id/sertifikat-3516',
                'created_at' => '2022-07-22 03:16:28',
                'updated_at' => '2024-12-06 12:16:09',
                'status' => 1,
                'priority' => 0
            ]
        ]);
    }
}