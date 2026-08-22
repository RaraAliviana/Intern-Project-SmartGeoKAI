<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'Admin SmartGeoKAI',
                'username' => 'admin',
                'nip' => '19890101202001',
                'role' => 'admin',
                'is_active' => true,
                'province_id' => null, // null = akses semua wilayah
                'password' => Hash::make('password123'),
            ]
        );
    }
}