<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Budi Santoso',
                'jabatan' => 'Manager',
                'email' => 'budi@example.com',
                'nomer_telepon' => '628983922747',
                'is_admin' => 1,
                'is_leader' => 1,
                'is_hamukti' => 1,
                'is_active' => 1,
                'username' => 'budi123',
                'gambar' => 'budi.jpg',
                'password' => Hash::make('12345678'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Rahma',
                'jabatan' => 'Staff',
                'email' => 'siti@example.com',
                'nomer_telepon' => '628983922747',
                'is_admin' => 0,
                'is_leader' => 0,
                'is_hamukti' => 1,
                'is_active' => 1,
                'username' => 'siti123',
                'gambar' => 'siti.jpg',
                'password' => Hash::make('12345678'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
