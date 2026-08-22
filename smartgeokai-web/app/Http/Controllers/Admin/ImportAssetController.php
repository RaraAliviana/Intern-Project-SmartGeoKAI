<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportAssetRequest;
use App\Models\Asset;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportAssetController extends Controller
{
    /**
     * Menampilkan halaman upload import aset.
     */
    public function index()
    {
        return view('admin.import.index');
    }

    /**
     * Mengunduh template CSV untuk import data aset.
     */
    public function downloadTemplate()
    {
        $filename = 'template-import-aset.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            // Header sesuai format contoh
            fputcsv($file, [
                'ID Asset',
                'Status',
                'Provinsi',
                'Kabupaten/Kota',
                'Kecamatan',
                'Jenis Aset',
                'Klasifikasi',
                'Kelompok',
                'Luas m2',
                'Latitude',
                'Longitude',
                'Keterangan',
                'Google Maps',
            ]);

            // Contoh baris dummy
            fputcsv($file, [
                '17.02.0001',
                'Clear',
                'Daerah Istimewa Yogyakarta',
                'Kabupaten Bantul',
                'Srandakan',
                'Tanah',
                'ROW',
                'Produksi',
                '2323',
                '-7.923456',
                '110.234567',
                'Aset tanah di pinggir rel',
                'https://maps.google.com/?q=-7.923456,110.234567',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Memproses file CSV yang diunggah.
     */
    public function store(ImportAssetRequest $request)
    {
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Lewati BOM jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Ambil baris header
        $header = fputcsv_get_array($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong atau format tidak valid.');
        }

        $successCount = 0;
        $failures = [];
        $rowNum = 1; // Baris 1 adalah header

        DB::beginTransaction();
        try {
            while (($row = fputcsv_get_array($handle)) !== false) {
                $rowNum++;

                // Lewati baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                $idAsset       = trim($row[0] ?? '');
                $status        = trim($row[1] ?? '');
                $provinceName  = trim($row[2] ?? '');
                $regencyName   = trim($row[3] ?? '');
                $districtName  = trim($row[4] ?? '');
                $assetType     = trim($row[5] ?? '');
                $classification= trim($row[6] ?? '');
                $assetGroup    = trim($row[7] ?? '');
                $areaM2        = trim($row[8] ?? 0);
                $latitude      = trim($row[9] ?? null);
                $longitude     = trim($row[10] ?? null);
                $description   = trim($row[11] ?? null);
                $gmapsUrl      = trim($row[12] ?? null);

                // --- VALIDASI TIAP BARIS ---
                $errors = [];

                if (empty($idAsset)) {
                    $errors[] = 'ID Asset wajib diisi.';
                } else {
                    // Cek keunikan ID Asset
                    $exists = Asset::where('id_asset', $idAsset)->exists();
                    if ($exists) {
                        $errors[] = "ID Asset '$idAsset' sudah terdaftar di database.";
                    }
                }

                if (!in_array($status, ['Clear', 'Proses', 'Masalah'])) {
                    $errors[] = "Status '$status' tidak valid (harus: Clear, Proses, atau Masalah).";
                }

                // Cari Provinsi
                $province = null;
                if (!empty($provinceName)) {
                    $province = Province::where('name', 'LIKE', "%$provinceName%")->first();
                    if (!$province) {
                        $errors[] = "Provinsi '$provinceName' tidak ditemukan di database.";
                    }
                } else {
                    $errors[] = 'Provinsi wajib diisi.';
                }

                // Cari Kabupaten/Kota
                $regency = null;
                if (!empty($regencyName) && $province) {
                    $regency = Regency::where('province_id', $province->id)
                        ->where('name', 'LIKE', "%$regencyName%")
                        ->first();
                    if (!$regency) {
                        $errors[] = "Kabupaten/Kota '$regencyName' tidak ditemukan pada provinsi $provinceName.";
                    }
                } elseif (empty($regencyName)) {
                    $errors[] = 'Kabupaten/Kota wajib diisi.';
                }

                // Cari Kecamatan
                $district = null;
                if (!empty($districtName) && $regency) {
                    $district = District::where('regency_id', $regency->id)
                        ->where('name', 'LIKE', "%$districtName%")
                        ->first();
                    if (!$district) {
                        $errors[] = "Kecamatan '$districtName' tidak ditemukan pada kabupaten $regencyName.";
                    }
                } elseif (empty($districtName)) {
                    $errors[] = 'Kecamatan wajib diisi.';
                }

                // Bersihkan nilai area_m2
                $areaM2Clean = str_replace(['.', ','], ['', '.'], $areaM2);
                if (!is_numeric($areaM2Clean)) {
                    $errors[] = 'Luas (m2) harus berupa angka.';
                }

                // Jika ada error pada baris ini, catat dan lanjut ke baris berikutnya
                if (!empty($errors)) {
                    $failures[] = [
                        'row'      => $rowNum,
                        'id_asset' => $idAsset ?: '-',
                        'reasons'  => implode(' ', $errors),
                    ];
                    continue;
                }

                // --- SIMPAN BARIS VALID ---
                Asset::create([
                    'id_asset'       => $idAsset,
                    'status'         => $status,
                    'province_id'    => $province->id,
                    'regency_id'     => $regency->id,
                    'district_id'    => $district->id,
                    'asset_type'     => $assetType ?: null,
                    'classification' => $classification ?: null,
                    'asset_group'    => $assetGroup ?: null,
                    'area_m2'        => (float) $areaM2Clean,
                    'latitude'       => $latitude ?: null,
                    'longitude'      => $longitude ?: null,
                    'description'    => $description ?: null,
                    'gmaps_url'      => $gmapsUrl ?: null,
                    'created_by'     => Auth::id(),
                ]);

                $successCount++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Terjadi kesalahan sistem saat mengimpor data: ' . $e->getMessage());
        }

        fclose($handle);

        return back()->with([
            'import_summary' => [
                'success_count' => $successCount,
                'failure_count' => count($failures),
                'failures'      => $failures,
            ],
        ]);
    }
}

/**
 * Helper pembacaan baris CSV aman.
 */
function fputcsv_get_array($handle)
{
    return fgetcsv($handle, 2000, ',');
}