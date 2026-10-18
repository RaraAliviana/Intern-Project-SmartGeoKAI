<?php

namespace App\Livewire\Admin;

use App\Models\Asset;
use App\Models\User;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        // ===== Stat cards =====
        $totalAssets = Asset::count();

        $statusCounts = Asset::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalPetugasAktif = User::where('role', 'petugas')
            ->where('is_active', true)
            ->count();

        // ===== Pie chart: distribusi status aset =====
        $pieData = [
            'Clear' => $statusCounts['Clear'] ?? 0,
            'Proses' => $statusCounts['Proses'] ?? 0,
            'Masalah' => $statusCounts['Masalah'] ?? 0,
        ];

        // ===== Bar chart: jumlah aset per provinsi =====
        $assetsPerProvince = Asset::join('provinces', 'assets.province_id', '=', 'provinces.id')
            ->selectRaw('provinces.name as province_name, count(*) as total')
            ->groupBy('provinces.name')
            ->orderByDesc('total')
            ->get();

        $provinceLabels = $assetsPerProvince->pluck('province_name');
        $provinceData = $assetsPerProvince->pluck('total');

        // ===== Peta: marker seluruh aset yang punya koordinat =====
        $mapMarkers = Asset::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            // Dibatasi wilayah Jawa Tengah & DIY (sama seperti peta sebelumnya)
            ->whereBetween('latitude', [-8.80, -6.30])
            ->whereBetween('longitude', [108.50, 111.60])
            ->limit(2000)
            ->get(['id', 'id_asset', 'latitude', 'longitude', 'status', 'asset_type', 'description'])
            ->map(function (Asset $asset) {
                return [
                    'id' => $asset->id,
                    'idAsset' => $asset->id_asset,
                    'name' => $asset->description ?? $asset->asset_type,
                    'lat' => (float) $asset->latitude,
                    'lng' => (float) $asset->longitude,
                    'status' => $asset->status,
                    'color' => match ($asset->status) {
                        'Clear' => '#12b76a',
                        'Proses' => '#f79009',
                        'Masalah' => '#f04438',
                        default => '#667085',
                    },
                    'url' => route('admin.assets.show', $asset->id),
                ];
            })
            ->values();

        return view('livewire.admin.dashboard-stats', [
            'totalAssets' => $totalAssets,
            'clearCount' => $pieData['Clear'],
            'prosesCount' => $pieData['Proses'],
            'masalahCount' => $pieData['Masalah'],
            'totalPetugasAktif' => $totalPetugasAktif,
            'pieData' => $pieData,
            'provinceLabels' => $provinceLabels,
            'provinceData' => $provinceData,
            'pieHash' => md5(json_encode($pieData)),
            'barHash' => md5(json_encode([$provinceLabels, $provinceData])),
            'mapMarkers' => $mapMarkers,
        ]);
    }
}
