<?php

namespace Database\Seeders;

use App\Models\Regency;
use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        // ===================== DI YOGYAKARTA (semua, 78 kecamatan) =====================

        $this->seedDistricts('3471', [
            'Danurejan', 'Gedongtengen', 'Gondokusuman', 'Gondomanan', 'Jetis',
            'Kotagede', 'Kraton', 'Mantrijeron', 'Mergangsan', 'Ngampilan',
            'Pakualaman', 'Tegalrejo', 'Umbulharjo', 'Wirobrajan',
        ]);

        $this->seedDistricts('3404', [
            'Berbah', 'Cangkringan', 'Depok', 'Gamping', 'Godean', 'Kalasan',
            'Minggir', 'Mlati', 'Moyudan', 'Ngaglik', 'Ngemplak', 'Pakem',
            'Prambanan', 'Seyegan', 'Sleman', 'Tempel', 'Turi',
        ]);

        $this->seedDistricts('3402', [
            'Bambanglipuro', 'Banguntapan', 'Bantul', 'Dlingo', 'Imogiri', 'Jetis',
            'Kasihan', 'Kretek', 'Pajangan', 'Pandak', 'Piyungan', 'Pleret',
            'Pundong', 'Sanden', 'Sedayu', 'Sewon', 'Srandakan',
        ]);

        $this->seedDistricts('3401', [
            'Galur', 'Girimulyo', 'Kalibawang', 'Kokap', 'Lendah', 'Nanggulan',
            'Panjatan', 'Pengasih', 'Samigaluh', 'Sentolo', 'Temon', 'Wates',
        ]);

        $this->seedDistricts('3403', [
            'Gedangsari', 'Girisubo', 'Karangmojo', 'Ngawen', 'Nglipar', 'Paliyan',
            'Panggang', 'Patuk', 'Playen', 'Ponjong', 'Purwosari', 'Rongkop',
            'Saptosari', 'Semanu', 'Semin', 'Tanjungsari', 'Tepus', 'Wonosari',
        ]);

        // ===================== JAWA TENGAH (semua kecamatan per kabupaten/kota) =====================

        // Kabupaten Sragen (20 kecamatan)
        $this->seedDistricts('3314', [
            'Gemolong', 'Gesi', 'Gondang', 'Jenar', 'Kalijambe', 'Karangmalang',
            'Kedawung', 'Masaran', 'Miri', 'Mondokan', 'Ngrampal', 'Plupuh',
            'Sambirejo', 'Sambungmacan', 'Sidoharjo', 'Sragen', 'Sukodono',
            'Sumberlawang', 'Tangen', 'Tanon',
        ]);

        // Kabupaten Grobogan (19 kecamatan)
        $this->seedDistricts('3315', [
            'Brati', 'Gabus', 'Geyer', 'Godong', 'Grobogan', 'Gubug', 'Karangrayung',
            'Kedungjati', 'Klambu', 'Kradenan', 'Ngaringan', 'Penawangan', 'Pulokulon',
            'Purwodadi', 'Tanggungharjo', 'Tawangharjo', 'Tegowanu', 'Toroh', 'Wirosari',
        ]);

        // Kota Surakarta (5 kecamatan)
        $this->seedDistricts('3372', [
            'Laweyan', 'Serengan', 'Pasar Kliwon', 'Jebres', 'Banjarsari',
        ]);

        // Kabupaten Sukoharjo (12 kecamatan)
        $this->seedDistricts('3311', [
            'Baki', 'Bendosari', 'Bulu', 'Gatak', 'Grogol', 'Kartasura',
            'Mojolaban', 'Nguter', 'Polokarto', 'Sukoharjo', 'Tawangsari', 'Weru',
        ]);

        // Kabupaten Wonogiri (25 kecamatan)
        $this->seedDistricts('3312', [
            'Baturetno', 'Batuwarno', 'Bulukerto', 'Eromoko', 'Girimarto', 'Giritontro',
            'Giriwoyo', 'Jatipurno', 'Jatiroto', 'Jatisrono', 'Karangtengah', 'Kismantoro',
            'Manyaran', 'Ngadirojo', 'Nguntoronadi', 'Paranggupito', 'Pracimantoro',
            'Puhpelem', 'Purwantoro', 'Selogiri', 'Sidoharjo', 'Slogohimo', 'Tirtomoyo',
            'Wonogiri', 'Wuryantoro',
        ]);

        // Kabupaten Magelang (21 kecamatan)
        $this->seedDistricts('3308', [
            'Bandongan', 'Borobudur', 'Candimulyo', 'Dukun', 'Grabag', 'Kajoran',
            'Kaliangkrik', 'Mertoyudan', 'Mungkid', 'Muntilan', 'Ngablak', 'Ngluwar',
            'Pakis', 'Salam', 'Salaman', 'Sawangan', 'Secang', 'Srumbung', 'Tegalrejo',
            'Tempuran', 'Windusari',
        ]);

        // Kota Magelang (3 kecamatan)
        $this->seedDistricts('3371', [
            'Magelang Utara', 'Magelang Tengah', 'Magelang Selatan',
        ]);
    }

    private function seedDistricts(string $regencyCode, array $districtNames): void
    {
        $regency = Regency::where('code', $regencyCode)->first();

        if (! $regency) {
            $this->command->warn("Regency dengan code {$regencyCode} tidak ditemukan, dilewati.");
            return;
        }

        foreach ($districtNames as $index => $name) {
            District::updateOrCreate(
                ['regency_id' => $regency->id, 'name' => $name],
                ['regency_id' => $regency->id, 'code' => $regencyCode . str_pad($index + 1, 2, '0', STR_PAD_LEFT), 'name' => $name]
            );
        }
    }
}