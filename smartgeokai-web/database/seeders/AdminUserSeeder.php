<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // NIPP di bawah ini placeholder (angka, maks 10 digit). Ganti dengan NIPP asli.
        // Password diambil dari .env bila ada; fallback hanya untuk development.
        $accounts = [
            [
                'username'    => 'superadmin',
                'full_name'   => 'Super Administrator',
                'nipp'        => '10006',
                'role'        => 'superadmin',
                'password'    => env('SEED_SUPERADMIN_PASSWORD', 'password123'),
                'is_active'   => true,
                'province_id' => null, // null = akses semua wilayah
            ],
            [
                'username'    => 'admin',
                'full_name'   => 'Admin SmartGeoKAI',
                'nipp'        => '10001',
                'role'        => 'admin',
                'password'    => env('SEED_ADMIN_PASSWORD', 'password123'),
                'is_active'   => true,
                'province_id' => null,
            ],
        ];

        foreach ($accounts as $account) {
            // firstOrCreate: dibuat hanya jika username belum ada.
            // Akun yang sudah ada (termasuk password/NIPP yang sudah kamu ubah) tidak ditimpa.
            User::firstOrCreate(
                ['username' => $account['username']],
                $account
            );
        }
    }
}