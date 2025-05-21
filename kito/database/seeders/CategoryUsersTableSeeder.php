<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryUsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('category_users')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'name' => 'Anggaran',
                'created_at' => '2022-07-23 11:18:15',
                'updated_at' => '2022-07-23 11:18:25'
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'name' => 'Regsosek',
                'created_at' => '2022-09-10 22:33:38',
                'updated_at' => '2022-09-10 22:33:38'
            ],
            [
                'id' => 7,
                'user_id' => 1,
                'name' => 'Regsosek',
                'created_at' => '2022-11-23 02:06:32',
                'updated_at' => '2022-11-23 02:06:32'
            ],
            [
                'id' => 8,
                'user_id' => 1,
                'name' => 'Serba Serbi',
                'created_at' => '2022-12-18 20:05:41',
                'updated_at' => '2022-12-18 20:05:41'
            ],
            [
                'id' => 9,
                'user_id' => 1,
                'name' => 'TIM PROKSI',
                'created_at' => '2023-11-12 17:20:07',
                'updated_at' => '2023-11-12 17:20:07'
            ]
        ]);
    }
}