<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            ['code' => '34', 'name' => 'Daerah Istimewa Yogyakarta'],
            ['code' => '33', 'name' => 'Jawa Tengah'],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(['code' => $province['code']], $province);
        }
    }
}