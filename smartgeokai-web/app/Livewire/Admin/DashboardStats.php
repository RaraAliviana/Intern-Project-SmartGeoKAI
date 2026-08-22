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

        return view('livewire.admin.dashboard-stats', [
            'totalAssets' => $totalAssets,
            'clearCount' => $pieData['Clear'],
            'prosesCount' => $pieData['Proses'],
            'masalahCount' => $pieData['Masalah'],
            'totalPetugasAktif' => $totalPetugasAktif,
            'pieData' => $pieData,
            'provinceLabels' => $provinceLabels,
            'provinceData' => $provinceData,
            // Hash data untuk wire:key — berubah setiap kali angkanya berubah,
            // memaksa Livewire re-render elemen chart (Alpine x-init ikut jalan ulang)
            'pieHash' => md5(json_encode($pieData)),
            'barHash' => md5(json_encode([$provinceLabels, $provinceData])),
        ]);
    }
}