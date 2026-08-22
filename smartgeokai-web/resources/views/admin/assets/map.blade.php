@extends('layouts.app')

@section('title', 'Peta Aset')

@section('breadcrumb')
    <li class="breadcrumb-item active">Peta Aset</li>
@endsection

@section('content')

<div class="space-y-6">

    {{-- ============================================================
        HEADER
    ============================================================= --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Peta Aset
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Peta interaktif seluruh aset wilayah DAOP 6 (Jawa Tengah & DIY)
            </p>
        </div>

        <div class="flex items-center gap-2">

            {{-- Daftar Aset --}}
            <a href="{{ route('admin.assets.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">

                <i class="fa-solid fa-list"></i>

                <span>Daftar Aset</span>

            </a>

            {{-- Tambah Aset --}}
            <a href="{{ route('admin.assets.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">

                <i class="fa-solid fa-plus"></i>

                <span>Tambah Aset</span>

            </a>

        </div>

    </div>


    {{-- ============================================================
        STATISTIK ASET
    ============================================================= --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- TOTAL --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-theme-xs">

            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Aset
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['total'], 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <i class="fa-solid fa-layer-group text-lg"></i>
                </div>

            </div>

        </div>


        {{-- CLEAR --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-theme-xs">

            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Aset Clear
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['clear'], 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-600">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>

            </div>

        </div>


        {{-- PROSES --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-theme-xs">

            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Aset Proses
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['proses'], 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-yellow-50 text-yellow-600">
                    <i class="fa-solid fa-spinner text-lg"></i>
                </div>

            </div>

        </div>


        {{-- MASALAH --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-theme-xs">

            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Aset Masalah
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['masalah'], 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
        FILTER
    ============================================================= --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-theme-xs">

        <div class="mb-4 flex items-center gap-2">

            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-500">
                <i class="fa-solid fa-filter"></i>
            </div>

            <div>
                <h2 class="text-base font-semibold text-gray-800">
                    Filter Aset
                </h2>

                <p class="text-xs text-gray-500">
                    Gunakan filter untuk menampilkan aset tertentu pada peta
                </p>
            </div>

        </div>


        <form method="GET"
              action="{{ route('admin.assets.map') }}"
              class="space-y-4">

            {{-- BARIS 1 --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- Provinsi --}}
                <div>

                    <label for="province_id"
                           class="mb-1.5 block text-sm font-medium text-gray-700">
                        Provinsi
                    </label>

                    <select
                        name="province_id"
                        id="province_id"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Provinsi --
                        </option>

                        @foreach($provinces as $province)

                            <option
                                value="{{ $province->id }}"
                                {{ request('province_id') == $province->id ? 'selected' : '' }}>

                                {{ $province->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Kabupaten --}}
                <div>

                    <label for="regency_id"
                           class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kabupaten
                    </label>

                    <select
                        name="regency_id"
                        id="regency_id"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Kabupaten --
                        </option>

                        @foreach($regencies as $regency)

                            <option
                                value="{{ $regency->id }}"
                                {{ request('regency_id') == $regency->id ? 'selected' : '' }}>

                                {{ $regency->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Kecamatan --}}
                <div>

                    <label for="district_id"
                           class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kecamatan
                    </label>

                    <select
                        name="district_id"
                        id="district_id"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Kecamatan --
                        </option>

                        @foreach($districts as $district)

                            <option
                                value="{{ $district->id }}"
                                {{ request('district_id') == $district->id ? 'selected' : '' }}>

                                {{ $district->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- BARIS 2 --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- Tipe --}}
                <div>

                    <label for="asset_type"
                           class="mb-1.5 block text-sm font-medium text-gray-700">
                        Tipe Aset
                    </label>

                    <select
                        name="asset_type"
                        id="asset_type"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Tipe --
                        </option>

                        <option
                            value="Tanah"
                            {{ request('asset_type') == 'Tanah' ? 'selected' : '' }}>
                            Tanah
                        </option>

                        <option
                            value="Bangunan"
                            {{ request('asset_type') == 'Bangunan' ? 'selected' : '' }}>
                            Bangunan
                        </option>

                    </select>

                </div>


                {{-- Status --}}
                <div>

                    <label for="status"
                           class="mb-1.5 block text-sm font-medium text-gray-700">
                        Status Aset
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Status --
                        </option>

                        <option
                            value="Clear"
                            {{ request('status') == 'Clear' ? 'selected' : '' }}>
                            Clear
                        </option>

                        <option
                            value="Proses"
                            {{ request('status') == 'Proses' ? 'selected' : '' }}>
                            Proses
                        </option>

                        <option
                            value="Masalah"
                            {{ request('status') == 'Masalah' ? 'selected' : '' }}>
                            Masalah
                        </option>

                    </select>

                </div>


                {{-- Pencarian --}}
                <div>

                    <label for="search"
                           class="mb-1.5 block text-sm font-medium text-gray-700">
                        Pencarian
                    </label>

                    <div class="relative">

                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="{{ request('search') }}"
                            placeholder="Cari ID atau nama aset..."
                            class="h-11 w-full rounded-lg border border-gray-200 bg-white pl-10 pr-3 text-sm text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                    </div>

                </div>

            </div>


            {{-- BUTTON --}}
            <div class="flex flex-wrap items-center gap-2 pt-1">

                <button
                    type="submit"
                    class="inline-flex h-10 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">

                    <i class="fa-solid fa-filter"></i>

                    Terapkan Filter

                </button>


                <a
                    href="{{ route('admin.assets.map') }}"
                    class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-600 transition hover:bg-gray-50">

                    <i class="fa-solid fa-rotate-left"></i>

                    Reset

                </a>

            </div>

        </form>

    </div>


    {{-- ============================================================
        PETA
    ============================================================= --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-theme-xs">

        {{-- Header peta --}}
        <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="text-base font-semibold text-gray-800">
                    Peta Persebaran Aset
                </h2>

                <p class="mt-0.5 text-xs text-gray-500">
                    Menampilkan {{ number_format($assets->count(), 0, ',', '.') }} aset
                    sesuai filter
                </p>

            </div>


            {{-- Legend --}}
            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600">

                <div class="flex items-center gap-1.5">
                    <span class="h-3 w-3 rounded-full bg-green-500"></span>
                    Clear
                </div>

                <div class="flex items-center gap-1.5">
                    <span class="h-3 w-3 rounded-full bg-yellow-400"></span>
                    Proses
                </div>

                <div class="flex items-center gap-1.5">
                    <span class="h-3 w-3 rounded-full bg-red-500"></span>
                    Masalah
                </div>

            </div>

        </div>


        {{-- Container Leaflet --}}
        <div
            id="assetMap"
            class="h-[600px] w-full">
        </div>

    </div>

</div>

@endsection


{{-- ================================================================
     LEAFLET CSS
================================================================ --}}
@push('styles')

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
/>

<style>
    #assetMap {
        z-index: 1;
    }

    .asset-popup {
        min-width: 220px;
    }

    .asset-popup-title {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #1f2937;
    }

    .asset-popup-info {
        font-size: 12px;
        line-height: 1.6;
        color: #6b7280;
    }

    .asset-popup-status {
        display: inline-block;
        margin-top: 8px;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-clear {
        background: #dcfce7;
        color: #15803d;
    }

    .status-proses {
        background: #fef3c7;
        color: #a16207;
    }

    .status-masalah {
        background: #fee2e2;
        color: #dc2626;
    }
</style>

@endpush


{{-- ================================================================
     LEAFLET JAVASCRIPT
================================================================ --}}
@push('scripts')

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin="">
</script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const mapContainer = document.getElementById('assetMap');
    if (!mapContainer) return;

    // ============================================================
    // DATA ASET DARI LARAVEL
    // ============================================================

    const assets = @json($assets);


    // ============================================================
    // BATAS LOKASI JAWA TENGAH & DIY
    // ============================================================

    const jatengDIYBounds = L.latLngBounds(
        L.latLng(-8.80, 108.50), // Barat Daya
        L.latLng(-6.30, 111.60)  // Timur Laut
    );


    // ============================================================
    // INISIALISASI PETA DENGAN CANVAS & BATAS AREA TEGAS
    // ============================================================

    const map = L.map('assetMap', {
        preferCanvas: true,
        maxBounds: jatengDIYBounds,
        maxBoundsViscosity: 1.0,
        minZoom: 8,
        maxZoom: 18
    }).setView([-7.5, 110.2], 8);


    // ============================================================
    // TILE MAP (SATELIT ESRI HYBRID)
    // ============================================================

    // Layer 1: Citra Satelit
    L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            bounds: jatengDIYBounds,
            maxZoom: 18,
            attribution: 'Tiles &copy; Esri'
        }
    ).addTo(map);

    // Layer 2: Label Jalan & Nama Wilayah
    L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
        {
            bounds: jatengDIYBounds,
            maxZoom: 18,
            attribution: ''
        }
    ).addTo(map);


    // ============================================================
    // DYNAMIC RESIZE OBSERVER (AUTO FIX BLANK MAP)
    // ============================================================

    const resizeObserver = new ResizeObserver(() => {
        map.invalidateSize();
    });
    resizeObserver.observe(mapContainer);


    // ============================================================
    // LAYER UNTUK MARKER
    // ============================================================

    const markerLayer = L.layerGroup().addTo(map);


    // ============================================================
    // WARNA MARKER
    // ============================================================

    function getMarkerColor(status) {

        switch (status) {

            case 'Clear':
                return '#22c55e';

            case 'Proses':
                return '#facc15';

            case 'Masalah':
                return '#ef4444';

            default:
                return '#6b7280';
        }

    }


    // ============================================================
    // ICON MARKER
    // ============================================================

    function createMarkerIcon(status) {

        const color = getMarkerColor(status);

        return L.divIcon({

            className: 'custom-asset-marker',

            html: `
                <div
                    style="
                        width: 18px;
                        height: 18px;
                        background: ${color};
                        border: 3px solid white;
                        border-radius: 50%;
                        box-shadow: 0 2px 6px rgba(0,0,0,.35);
                    ">
                </div>
            `,

            iconSize: [18, 18],

            iconAnchor: [9, 9],

            popupAnchor: [0, -10]

        });

    }


    // ============================================================
    // TAMBAHKAN MARKER DENGAN VALIDASI AREA
    // ============================================================

    const validMarkers = [];


    assets.forEach(function (asset) {

        if (
            asset.latitude === null ||
            asset.longitude === null ||
            asset.latitude === undefined ||
            asset.longitude === undefined
        ) {
            return;
        }


        const latitude = parseFloat(asset.latitude);
        const longitude = parseFloat(asset.longitude);


        if (
            Number.isNaN(latitude) ||
            Number.isNaN(longitude)
        ) {
            return;
        }

        // Cek apakah koordinat aset berada di dalam batas wilayah Jateng & DIY
        const point = L.latLng(latitude, longitude);
        if (!jatengDIYBounds.contains(point)) {
            return;
        }


        const statusClass =
            asset.status === 'Clear'
                ? 'status-clear'
                : asset.status === 'Proses'
                    ? 'status-proses'
                    : asset.status === 'Masalah'
                        ? 'status-masalah'
                        : '';


        const description =
            asset.description
                ? asset.description
                : 'Tidak ada deskripsi';

        const detailUrl = `{{ route('admin.assets.show', ':id') }}`.replace(':id', asset.id);

        const popupContent = `

            <div class="asset-popup">

                <div class="asset-popup-title">
                    ${asset.id_asset ?? '-'}
                </div>

                <div class="asset-popup-info">

                    <strong>Tipe:</strong>
                    ${asset.asset_type ?? '-'}
                    <br>

                    <strong>Wilayah:</strong>
                    ${asset.district?.name ?? '-'},
                    ${asset.regency?.name ?? '-'}
                    <br>

                    <strong>Status:</strong>

                    <span class="asset-popup-status ${statusClass}">
                        ${asset.status ?? '-'}
                    </span>

                    <br>

                    <strong>Deskripsi:</strong>
                    ${description}

                </div>

                <div style="margin-top:10px;">

                    <a
                        href="${detailUrl}"
                        style="
                            font-size:12px;
                            color:#465fff;
                            font-weight:600;
                            text-decoration:none;
                        ">

                        Lihat Detail Aset →
                    </a>

                </div>

            </div>

        `;


        const marker = L.marker(
            [latitude, longitude],
            {
                icon: createMarkerIcon(asset.status)
            }
        );


        marker.bindPopup(popupContent);


        marker.addTo(markerLayer);


        validMarkers.push(marker);

    });


    // ============================================================
    // FIT MAP KE MARKER YANG ADA (DENGAN MAX ZOOM AMAN)
    // ============================================================

    if (validMarkers.length > 0) {

        const group = L.featureGroup(validMarkers);

        map.fitBounds(
            group.getBounds(),
            {
                padding: [40, 40],
                maxZoom: 14
            }
        );

    }


    // ============================================================
    // DROPDOWN PROVINSI
    // ============================================================

    const provinceSelect =
        document.getElementById('province_id');

    const regencySelect =
        document.getElementById('regency_id');

    const districtSelect =
        document.getElementById('district_id');


    if (provinceSelect) {

        provinceSelect.addEventListener(
            'change',
            function () {

                const provinceId = this.value;


                regencySelect.innerHTML =
                    '<option value="">-- Semua Kabupaten --</option>';

                districtSelect.innerHTML =
                    '<option value="">-- Semua Kecamatan --</option>';


                if (!provinceId) {
                    return;
                }


                fetch(
                    `{{ route('admin.assets.get-regencies') }}?province_id=${provinceId}`
                )

                .then(response => response.json())

                .then(data => {

                    data.forEach(function (regency) {

                        const option =
                            document.createElement('option');

                        option.value = regency.id;

                        option.textContent = regency.name;

                        regencySelect.appendChild(option);

                    });

                })

                .catch(error => {

                    console.error(
                        'Gagal mengambil data kabupaten:',
                        error
                    );

                });

            }
        );

    }


    // ============================================================
    // DROPDOWN KABUPATEN
    // ============================================================

    if (regencySelect) {

        regencySelect.addEventListener(
            'change',
            function () {

                const regencyId = this.value;


                districtSelect.innerHTML =
                    '<option value="">-- Semua Kecamatan --</option>';


                if (!regencyId) {
                    return;
                }


                fetch(
                    `{{ route('admin.assets.get-districts') }}?regency_id=${regencyId}`
                )

                .then(response => response.json())

                .then(data => {

                    data.forEach(function (district) {

                        const option =
                            document.createElement('option');

                        option.value = district.id;

                        option.textContent = district.name;

                        districtSelect.appendChild(option);

                    });

                })

                .catch(error => {

                    console.error(
                        'Gagal mengambil data kecamatan:',
                        error
                    );

                });

            }
        );

    }

});

</script>

@endpush