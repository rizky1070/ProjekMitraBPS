<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinksTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('links')->insert([
            [
                'id' => 3,
                'user_id' => 1,
                'category_user_id' => 3,
                'link' => 'https://docs.google.com/spreadsheets/d/1n2eN9AH_f6clqBj_9AxYLMORl-Cg26MsRb7G1sVJ8qs/edit#gid=1429059724',
                'name' => 'Update SLS/ RT',
                'created_at' => '2022-09-10 22:35:17',
                'updated_at' => '2022-09-10 22:35:17',
                'status' => 1,
                'priority' => 0
            ],
            [
                'id' => 9,
                'user_id' => 1,
                'category_user_id' => 7,
                'link' => 'https://sites.google.com/view/regsoseklink/link-regsosek-jatim',
                'name' => 'Link Regsosek Provinsi',
                'created_at' => '2022-11-23 02:13:18',
                'updated_at' => '2023-12-20 05:39:58',
                'status' => 1,
                'priority' => 0
            ],
            [
                'id' => 10,
                'user_id' => 1,
                'category_user_id' => 8,
                'link' => 'https://linktr.ee/AdobeBPS2023',
                'name' => 'Adobe',
                'created_at' => '2022-12-18 20:06:00',
                'updated_at' => '2023-12-16 05:17:03',
                'status' => 1,
                'priority' => 0
            ],
            [
                'id' => 11,
                'user_id' => 1,
                'category_user_id' => 7,
                'link' => 'https://124.158.151.234/share.cgi?ssid=902b85e195b844cdb624a0dcf3c8c3e3',
                'name' => 'Upload Backup Regsosek',
                'created_at' => '2023-01-24 01:27:56',
                'updated_at' => '2024-01-02 22:56:55',
                'status' => 1,
                'priority' => 0
            ],
            [
                'id' => 13,
                'user_id' => 1,
                'category_user_id' => 8,
                'link' => 's.bps.go.id/2023_daftarsampel',
                'name' => 'DSBS dan Prelist Survei Rutin',
                'created_at' => '2023-08-14 04:21:47',
                'updated_at' => '2023-08-14 04:21:47',
                'status' => 1,
                'priority' => 0
            ]
        ]);
    }
}