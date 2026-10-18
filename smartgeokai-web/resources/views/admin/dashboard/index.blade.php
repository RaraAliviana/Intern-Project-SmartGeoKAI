@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="mb-6 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-500">Ringkasan kondisi sistem SmartGeoKAI secara keseluruhan.</p>
        </div>
        <p class="text-sm text-gray-400">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    @livewire('admin.dashboard-stats')

@endsection


{{-- ================================================================
     LEAFLET CSS (untuk peta) — dimuat di sini, BUKAN di dalam
     component Livewire, supaya component tetap punya 1 root element.
================================================================ --}}
@push('styles')
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
/>

<style>
    #dashboardAssetMap { background: #e5e7eb; }
    .asset-popup { min-width: 200px; font-family: 'Outfit', sans-serif; }
    .asset-popup-title { font-size: 13px; font-weight: 700; margin-bottom: 4px; color: #1f2937; }
    .asset-popup-info { font-size: 12px; color: #6b7280; }
    .asset-popup-status {
        display: inline-block; margin-top: 6px; padding: 2px 8px;
        border-radius: 999px; font-size: 11px; font-weight: 600;
    }
</style>
@endpush


{{-- ================================================================
     LEAFLET JS + CHART.JS + FUNGSI WIDGET
================================================================ --}}
@push('scripts')
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin="">
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
    function dashboardMapWidget(markers) {
        return {
            map: null,

            init() {
                const JATENG_DIY_BOUNDS = L.latLngBounds(
                    L.latLng(-8.80, 108.50),
                    L.latLng(-6.30, 111.60)
                );

                this.map = L.map('dashboardAssetMap', {
                    preferCanvas: true,
                    maxBounds: JATENG_DIY_BOUNDS,
                    maxBoundsViscosity: 1.0,
                    minZoom: 8,
                    maxZoom: 18,
                }).setView([-7.55, 110.35], 9);

                L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    {
                        maxZoom: 18,
                        minZoom: 8,
                        bounds: JATENG_DIY_BOUNDS,
                        noWrap: true,
                        attribution: 'Tiles &copy; Esri — Source: Esri, Maxar, Earthstar Geographics, GIS User Community',
                    }
                ).addTo(this.map);

                L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
                    {
                        maxZoom: 18,
                        minZoom: 8,
                        bounds: JATENG_DIY_BOUNDS,
                        noWrap: true,
                        attribution: '',
                    }
                ).addTo(this.map);

                const markerLayer = L.layerGroup().addTo(this.map);
                const validPoints = [];

                markers.forEach((m) => {
                    if (m.lat === null || m.lng === null) return;

                    const icon = L.divIcon({
                        className: 'custom-asset-marker',
                        html: `<div style="width:14px;height:14px;background:${m.color};border:2px solid white;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>`,
                        iconSize: [14, 14],
                        iconAnchor: [7, 7],
                        popupAnchor: [0, -7],
                    });

                    const marker = L.marker([m.lat, m.lng], { icon });

                    marker.bindPopup(`
                        <div class="asset-popup">
                            <div class="asset-popup-title">${m.idAsset ?? '-'}</div>
                            <div class="asset-popup-info">${m.name ?? ''}</div>
                            <span class="asset-popup-status" style="background:${m.color}22;color:${m.color};">${m.status}</span>
                            <br><a href="${m.url}" style="display:inline-block;margin-top:8px;font-size:12px;color:#465fff;font-weight:600;">Lihat Detail →</a>
                        </div>
                    `);

                    marker.addTo(markerLayer);
                    validPoints.push([m.lat, m.lng]);
                });

                if (validPoints.length > 0) {
                    this.map.fitBounds(L.latLngBounds(validPoints), { padding: [30, 30], maxZoom: 13 });
                }

                const container = document.getElementById('dashboardAssetMap');
                if (window.ResizeObserver) {
                    new ResizeObserver(() => this.map.invalidateSize()).observe(container);
                }
                window.addEventListener('resize', () => this.map.invalidateSize());
                setTimeout(() => this.map.invalidateSize(), 300);
            },
        };
    }

    function pieChartWidget(dataObj) {
        return {
            render() {
                new Chart(this.$refs.canvas, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(dataObj),
                        datasets: [{
                            data: Object.values(dataObj),
                            backgroundColor: ['#12b76a', '#f79009', '#f04438'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        cutout: '65%',
                    },
                });
            },
        };
    }

    function barChartWidget(labels, data) {
        return {
            render() {
                new Chart(this.$refs.canvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Aset',
                            data: data,
                            backgroundColor: '#465fff',
                            borderRadius: 6,
                            maxBarThickness: 48,
                        }],
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                        },
                    },
                });
            },
        };
    }
</script>
@endpush
