<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KetuasTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('ketuas')->insert([
            [
                'id' => 3,
                'category_id' => 3,
                'link' => 'https://docs.google.com/spreadsheets/d/14pV36Rb8QHIIPfzbA2NQftFEp1MNp5td/edit#gid=1677931223',
                'name' => 'Capaian Output dan LDS',
                'created_at' => '2022-09-15 23:54:00',
                'updated_at' => '2024-01-03 12:22:46',
                'status' => 0,
                'priority' => 0
            ],
            [
                'id' => 4,
                'category_id' => 3,
                'link' => 'https://docs.google.com/spreadsheets/d/1Nr073UgtrpXGaZbOw9iV5spprBplmqcf/edit#gid=2065791495',
                'name' => 'FRA 2022',
                'created_at' => '2022-09-15 23:54:42',
                'updated_at' => '2023-12-16 05:22:05',
                'status' => 0,
                'priority' => 0
            ],
            [
                'id' => 5,
                'category_id' => 3,
                'link' => 'https://laci.bps.go.id/s/HLB9vq49oRjKiBd',
                'name' => 'Apel',
                'created_at' => '2022-09-15 23:55:42',
                'updated_at' => '2023-12-15 22:44:59',
                'status' => 1,
                'priority' => 0
            ],
            [
                'id' => 7,
                'category_id' => 3,
                'link' => 'https://drive.google.com/drive/folders/1xe_N-QYxXBPZ5SREAO-NYsS0Ywzq1MaH',
                'name' => 'MPH 2022',
                'created_at' => '2022-09-15 23:57:01',
                'updated_at' => '2023-12-15 08:49:08',
                'status' => 0,
                'priority' => 0
            ],
            [
                'id' => 8,
                'category_id' => 3,
                'link' => 'https://drive.google.com/drive/folders/1Fp5aVKCqmur8xBRrqbRv73WodP76xBwc',
                'name' => 'SKP 2022',
                'created_at' => '2022-09-15 23:57:48',
                'updated_at' => '2024-01-02 20:54:23',
                'status' => 0,
                'priority' => 0
            ]
        ]);
    }
}