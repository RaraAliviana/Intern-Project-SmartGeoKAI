<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman laporan aset.
     */
    public function index(Request $request)
    {
        $query = Asset::query()
            ->leftJoin(
                'provinces',
                'assets.province_id',
                '=',
                'provinces.id'
            )
            ->leftJoin(
                'regencies',
                'assets.regency_id',
                '=',
                'regencies.id'
            )
            ->leftJoin(
                'districts',
                'assets.district_id',
                '=',
                'districts.id'
            )
            ->leftJoin(
                'users as creator',
                'assets.created_by',
                '=',
                'creator.id'
            )
            ->leftJoin(
                'users as updater',
                'assets.updated_by',
                '=',
                'updater.id'
            )
            ->select([
                'assets.*',

                'provinces.name as province_name',
                'regencies.name as regency_name',
                'districts.name as district_name',

                'creator.full_name as creator_name',
                'updater.full_name as updater_name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        // Provinsi
        if ($request->filled('province_id')) {
            $query->where(
                'assets.province_id',
                $request->province_id
            );
        }

        // Kabupaten/Kota
        if ($request->filled('regency_id')) {
            $query->where(
                'assets.regency_id',
                $request->regency_id
            );
        }

        // Kecamatan
        if ($request->filled('district_id')) {
            $query->where(
                'assets.district_id',
                $request->district_id
            );
        }

        // Status
        if ($request->filled('status')) {
            $query->where(
                'assets.status',
                $request->status
            );
        }

        // Jenis aset
        if ($request->filled('asset_type')) {
            $query->where(
                'assets.asset_type',
                $request->asset_type
            );
        }

        // Tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate(
                'assets.created_at',
                '>=',
                $request->start_date
            );
        }

        // Tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate(
                'assets.created_at',
                '<=',
                $request->end_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $totalAssets = (clone $query)
            ->count('assets.id');

        $totalClear = (clone $query)
            ->where('assets.status', 'Clear')
            ->count('assets.id');

        $totalProses = (clone $query)
            ->where('assets.status', 'Proses')
            ->count('assets.id');

        $totalMasalah = (clone $query)
            ->where('assets.status', 'Masalah')
            ->count('assets.id');

        /*
        |--------------------------------------------------------------------------
        | DATA LAPORAN
        |--------------------------------------------------------------------------
        */

        $assets = $query
            ->orderBy('assets.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | DROPDOWN FILTER
        |--------------------------------------------------------------------------
        */

        $provinces = Province::orderBy('name')
            ->get();

        $regencies = collect();

        if ($request->filled('province_id')) {
            $regencies = Regency::where(
                    'province_id',
                    $request->province_id
                )
                ->orderBy('name')
                ->get();
        }

        $districts = collect();

        if ($request->filled('regency_id')) {
            $districts = District::where(
                    'regency_id',
                    $request->regency_id
                )
                ->orderBy('name')
                ->get();
        }

        $statuses = [
            'Clear',
            'Proses',
            'Masalah',
        ];

        $assetTypes = Asset::query()
            ->whereNotNull('asset_type')
            ->where('asset_type', '!=', '')
            ->distinct()
            ->orderBy('asset_type')
            ->pluck('asset_type');

        return view(
            'admin.reports.index',
            compact(
                'assets',
                'provinces',
                'regencies',
                'districts',
                'statuses',
                'assetTypes',
                'totalAssets',
                'totalClear',
                'totalProses',
                'totalMasalah'
            )
        );
    }


    /**
     * Export laporan aset ke CSV.
     *
     * Filter yang digunakan sama dengan halaman laporan:
     * - Provinsi
     * - Kabupaten/Kota
     * - Kecamatan
     * - Status
     * - Jenis Aset
     * - Tanggal mulai
     * - Tanggal akhir
     */
    public function exportCsv(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | QUERY DATA
        |--------------------------------------------------------------------------
        */

        $query = Asset::query()
            ->leftJoin(
                'provinces',
                'assets.province_id',
                '=',
                'provinces.id'
            )
            ->leftJoin(
                'regencies',
                'assets.regency_id',
                '=',
                'regencies.id'
            )
            ->leftJoin(
                'districts',
                'assets.district_id',
                '=',
                'districts.id'
            )
            ->leftJoin(
                'users as creator',
                'assets.created_by',
                '=',
                'creator.id'
            )
            ->leftJoin(
                'users as updater',
                'assets.updated_by',
                '=',
                'updater.id'
            )
            ->select([
                'assets.*',

                'provinces.name as province_name',
                'regencies.name as regency_name',
                'districts.name as district_name',

                'creator.full_name as creator_name',
                'updater.full_name as updater_name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        // Provinsi
        if ($request->filled('province_id')) {
            $query->where(
                'assets.province_id',
                $request->province_id
            );
        }

        // Kabupaten/Kota
        if ($request->filled('regency_id')) {
            $query->where(
                'assets.regency_id',
                $request->regency_id
            );
        }

        // Kecamatan
        if ($request->filled('district_id')) {
            $query->where(
                'assets.district_id',
                $request->district_id
            );
        }

        // Status
        if ($request->filled('status')) {
            $query->where(
                'assets.status',
                $request->status
            );
        }

        // Jenis aset
        if ($request->filled('asset_type')) {
            $query->where(
                'assets.asset_type',
                $request->asset_type
            );
        }

        // Tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate(
                'assets.created_at',
                '>=',
                $request->start_date
            );
        }

        // Tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate(
                'assets.created_at',
                '<=',
                $request->end_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA DATA
        |--------------------------------------------------------------------------
        |
        | Tidak menggunakan paginate karena CSV harus berisi
        | seluruh hasil laporan sesuai filter.
        |
        */

        $assets = $query
            ->orderBy('assets.created_at', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | NAMA FILE
        |--------------------------------------------------------------------------
        */

        $fileName = 'laporan-aset-' .
            now()->format('Y-m-d-His') .
            '.csv';

        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD CSV
        |--------------------------------------------------------------------------
        */

        return response()->streamDownload(
            function () use ($assets) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                /*
                |--------------------------------------------------------------------------
                | UTF-8 BOM
                |--------------------------------------------------------------------------
                |
                | Membantu Microsoft Excel membaca karakter
                | bahasa Indonesia dengan benar.
                |
                */

                fwrite(
                    $handle,
                    "\xEF\xBB\xBF"
                );

                /*
                |--------------------------------------------------------------------------
                | HEADER CSV
                |--------------------------------------------------------------------------
                */

                fputcsv(
                    $handle,
                    [
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
                        'Input Oleh',
                        'Tanggal Dibuat',
                        'Terakhir Diupdate',
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | DATA CSV
                |--------------------------------------------------------------------------
                */

                foreach ($assets as $asset) {

                    fputcsv(
                        $handle,
                        [
                            // ID Asset
                            $asset->id_asset ?? '-',

                            // Status
                            $asset->status ?? '-',

                            // Provinsi
                            $asset->province_name ?? '-',

                            // Kabupaten/Kota
                            $asset->regency_name ?? '-',

                            // Kecamatan
                            $asset->district_name ?? '-',

                            // Jenis Aset
                            $asset->asset_type ?? '-',

                            // Klasifikasi
                            $asset->classification ?? '-',

                            // Kelompok
                            $asset->asset_group ?? '-',

                            // Luas
                            $asset->area_m2 ?? '0',

                            // Latitude
                            $asset->latitude ?? '-',

                            // Longitude
                            $asset->longitude ?? '-',

                            // Keterangan
                            $asset->description ?? '-',

                            // Google Maps
                            $asset->gmaps_url ?? '-',

                            // Input Oleh
                            $asset->creator_name ?? '-',

                            // Tanggal Dibuat
                            $asset->created_at
                                ? $asset->created_at
                                    ->format('d/m/Y H:i')
                                : '-',

                            // Terakhir Diupdate
                            $asset->updated_at
                                ? $asset->updated_at
                                    ->format('d/m/Y H:i')
                                : '-',
                        ]
                    );
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' .
                    $fileName .
                    '"',
            ]
        );
    }


    /**
     * Export laporan aset ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = Asset::query()
            ->leftJoin(
                'provinces',
                'assets.province_id',
                '=',
                'provinces.id'
            )
            ->leftJoin(
                'regencies',
                'assets.regency_id',
                '=',
                'regencies.id'
            )
            ->leftJoin(
                'districts',
                'assets.district_id',
                '=',
                'districts.id'
            )
            ->leftJoin(
                'users as creator',
                'assets.created_by',
                '=',
                'creator.id'
            )
            ->select([
                'assets.*',

                'provinces.name as province_name',
                'regencies.name as regency_name',
                'districts.name as district_name',

                'creator.full_name as creator_name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('province_id')) {
            $query->where(
                'assets.province_id',
                $request->province_id
            );
        }

        if ($request->filled('regency_id')) {
            $query->where(
                'assets.regency_id',
                $request->regency_id
            );
        }

        if ($request->filled('district_id')) {
            $query->where(
                'assets.district_id',
                $request->district_id
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'assets.status',
                $request->status
            );
        }

        if ($request->filled('asset_type')) {
            $query->where(
                'assets.asset_type',
                $request->asset_type
            );
        }

        if ($request->filled('start_date')) {
            $query->whereDate(
                'assets.created_at',
                '>=',
                $request->start_date
            );
        }

        if ($request->filled('end_date')) {
            $query->whereDate(
                'assets.created_at',
                '<=',
                $request->end_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $assets = $query
            ->orderBy('assets.created_at', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | INFORMASI FILTER UNTUK PDF
        |--------------------------------------------------------------------------
        */

        $provinceName = 'Semua Provinsi';

        if ($request->filled('province_id')) {
            $provinceName = Province::find(
                $request->province_id
            )?->name ?? '-';
        }

        $regencyName = 'Semua Kabupaten/Kota';

        if ($request->filled('regency_id')) {
            $regencyName = Regency::find(
                $request->regency_id
            )?->name ?? '-';
        }

        $districtName = 'Semua Kecamatan';

        if ($request->filled('district_id')) {
            $districtName = District::find(
                $request->district_id
            )?->name ?? '-';
        }

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'admin.reports.pdf',
            [
                'assets' => $assets,

                'provinceName' => $provinceName,
                'regencyName' => $regencyName,
                'districtName' => $districtName,

                'status' =>
                    $request->status
                    ?: 'Semua Status',

                'assetType' =>
                    $request->asset_type
                    ?: 'Semua Jenis Aset',

                'startDate' =>
                    $request->start_date
                    ?: '-',

                'endDate' =>
                    $request->end_date
                    ?: '-',

                'generatedAt' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | PENGATURAN PDF
        |--------------------------------------------------------------------------
        */

        $pdf->setPaper(
            'a4',
            'landscape'
        );

        return $pdf->download(
            'laporan-aset-' .
            now()->format('Y-m-d-His') .
            '.pdf'
        );
    }
}