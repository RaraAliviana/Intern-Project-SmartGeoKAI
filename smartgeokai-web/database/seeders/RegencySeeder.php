<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Regency;
use Illuminate\Database\Seeder;

class RegencySeeder extends Seeder
{
    public function run(): void
    {
        $diy = Province::where('code', '34')->firstOrFail();
        $jateng = Province::where('code', '33')->firstOrFail();

        $regencies = [
            // DIY
            ['province_id' => $diy->id, 'code' => '3471', 'name' => 'Kota Yogyakarta'],
            ['province_id' => $diy->id, 'code' => '3404', 'name' => 'Kabupaten Sleman'],
            ['province_id' => $diy->id, 'code' => '3402', 'name' => 'Kabupaten Bantul'],
            ['province_id' => $diy->id, 'code' => '3401', 'name' => 'Kabupaten Kulon Progo'],
            ['province_id' => $diy->id, 'code' => '3403', 'name' => 'Kabupaten Gunungkidul'],

            // Jawa Tengah
            ['province_id' => $jateng->id, 'code' => '3314', 'name' => 'Kabupaten Sragen'],
            ['province_id' => $jateng->id, 'code' => '3315', 'name' => 'Kabupaten Grobogan'],
            ['province_id' => $jateng->id, 'code' => '3372', 'name' => 'Kota Surakarta'],
            ['province_id' => $jateng->id, 'code' => '3311', 'name' => 'Kabupaten Sukoharjo'],
            ['province_id' => $jateng->id, 'code' => '3312', 'name' => 'Kabupaten Wonogiri'],
            ['province_id' => $jateng->id, 'code' => '3308', 'name' => 'Kabupaten Magelang'],
            ['province_id' => $jateng->id, 'code' => '3371', 'name' => 'Kota Magelang'],
        ];

        foreach ($regencies as $regency) {
            Regency::updateOrCreate(
                ['code' => $regency['code']],
                $regency
            );
        }
    }
}