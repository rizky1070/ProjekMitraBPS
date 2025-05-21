<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'SDI, Pojok Statistik, Humas',
                'created_at' => '2022-07-20 17:42:42',
                'updated_at' => '2024-07-11 07:37:33'
            ],
            [
                'id' => 2,
                'name' => 'Lainnya',
                'created_at' => '2022-07-20 23:12:25',
                'updated_at' => '2024-01-03 12:35:06'
            ],
            [
                'id' => 3,
                'name' => 'Umum',
                'created_at' => '2022-07-20 23:25:39',
                'updated_at' => '2022-07-20 23:25:39'
            ],
            [
                'id' => 4,
                'name' => 'Lajang',
                'created_at' => '2022-08-28 21:11:04',
                'updated_at' => '2024-01-03 12:31:03'
            ],
            [
                'id' => 6,
                'name' => 'Inpek',
                'created_at' => '2022-09-15 23:52:38',
                'updated_at' => '2024-01-03 12:32:24'
            ]
        ]);
    }
}