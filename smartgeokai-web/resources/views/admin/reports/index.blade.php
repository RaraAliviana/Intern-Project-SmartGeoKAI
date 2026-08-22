@extends('layouts.app')

@section('title', 'Laporan Aset')

@section('breadcrumb')
    <li class="breadcrumb-item active">Laporan Aset</li>
@endsection

@section('content')

<div class="space-y-6">

    {{-- ============================================================
        HEADER
    ============================================================= --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Laporan Data Aset
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Filter, rekapitulasi, dan unduh laporan data aset wilayah DAOP 6
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">

            {{-- Export CSV --}}
            <a href="{{ route('admin.reports.export.csv', request()->query()) }}"
               class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-green-700">
                <i class="fa-solid fa-file-csv"></i>
                <span>Export CSV</span>
            </a>

            {{-- Export PDF --}}
            <a href="{{ route('admin.reports.export.pdf', request()->query()) }}"
               class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-red-700">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Export PDF</span>
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
                        Total Laporan Aset
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($totalAssets, 0, ',', '.') }}
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
                        {{ number_format($totalClear, 0, ',', '.') }}
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
                        {{ number_format($totalProses, 0, ',', '.') }}
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
                        {{ number_format($totalMasalah, 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                </div>
            </div>
        </div>

    </div>


    {{-- ============================================================
        FILTER LAPORAN
    ============================================================= --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-theme-xs">

        <div class="mb-4 flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-500">
                <i class="fa-solid fa-filter"></i>
            </div>

            <div>
                <h2 class="text-base font-semibold text-gray-800">
                    Filter Laporan
                </h2>

                <p class="text-xs text-gray-500">
                    Gunakan kriteria di bawah ini untuk menyaring laporan aset
                </p>
            </div>
        </div>


        <form method="GET"
              action="{{ route('admin.reports.index') }}"
              class="space-y-4">

            {{-- BARIS 1 --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- Provinsi --}}
                <div>
                    <label for="province_id" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Provinsi
                    </label>

                    <select name="province_id"
                            id="province_id"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Provinsi --
                        </option>

                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}"
                                {{ request('province_id') == $province->id ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- Kabupaten --}}
                <div>
                    <label for="regency_id" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kabupaten/Kota
                    </label>

                    <select name="regency_id"
                            id="regency_id"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Kabupaten/Kota --
                        </option>

                        @foreach($regencies as $regency)
                            <option value="{{ $regency->id }}"
                                {{ request('regency_id') == $regency->id ? 'selected' : '' }}>
                                {{ $regency->name }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- Kecamatan --}}
                <div>
                    <label for="district_id" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kecamatan
                    </label>

                    <select name="district_id"
                            id="district_id"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Kecamatan --
                        </option>

                        @foreach($districts as $district)
                            <option value="{{ $district->id }}"
                                {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

            </div>


            {{-- BARIS 2 --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                {{-- Status --}}
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Status Aset
                    </label>

                    <select name="status"
                            id="status"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Status --
                        </option>

                        @foreach($statuses as $status)
                            <option value="{{ $status }}"
                                {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- Jenis Aset --}}
                <div>
                    <label for="asset_type" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Jenis Aset
                    </label>

                    <select name="asset_type"
                            id="asset_type"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">

                        <option value="">
                            -- Semua Jenis --
                        </option>

                        @foreach($assetTypes as $type)
                            <option value="{{ $type }}"
                                {{ request('asset_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- Tanggal Mulai --}}
                <div>
                    <label for="start_date" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Dari Tanggal
                    </label>

                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ request('start_date') }}"
                           class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">
                </div>


                {{-- Tanggal Akhir --}}
                <div>
                    <label for="end_date" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Sampai Tanggal
                    </label>

                    <input type="date"
                           name="end_date"
                           id="end_date"
                           value="{{ request('end_date') }}"
                           class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10">
                </div>

            </div>


            {{-- BUTTONS FILTER & EXPORT --}}
            <div class="flex flex-wrap items-center gap-2 pt-1">

                <button type="submit"
                        class="inline-flex h-10 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    <i class="fa-solid fa-filter"></i>
                    Terapkan Filter
                </button>


                <a href="{{ route('admin.reports.index') }}"
                   class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                    <i class="fa-solid fa-rotate-left"></i>
                    Reset
                </a>


                <a href="{{ route('admin.reports.export.csv', request()->query()) }}"
                   class="inline-flex h-10 items-center gap-2 rounded-lg bg-green-600 px-4 text-sm font-medium text-white shadow-theme-xs transition hover:bg-green-700">
                    <i class="fa-solid fa-file-csv"></i>
                    Export CSV
                </a>


                <a href="{{ route('admin.reports.export.pdf', request()->query()) }}"
                   class="inline-flex h-10 items-center gap-2 rounded-lg bg-red-600 px-4 text-sm font-medium text-white shadow-theme-xs transition hover:bg-red-700">
                    <i class="fa-solid fa-file-pdf"></i>
                    Export PDF
                </a>

            </div>

        </form>

    </div>


    {{-- ============================================================
        TABEL HASIL LAPORAN
    ============================================================= --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-theme-xs">

        <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">
                    Data Aset Laporan
                </h2>

                <p class="mt-0.5 text-xs text-gray-500">
                    Menampilkan total {{ number_format($assets->total(), 0, ',', '.') }} aset yang sesuai dengan filter
                </p>
            </div>
        </div>


        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-5 py-3.5 font-medium">ID Asset</th>
                        <th class="px-5 py-3.5 font-medium">Status</th>
                        <th class="px-5 py-3.5 font-medium">Provinsi</th>
                        <th class="px-5 py-3.5 font-medium">Kabupaten/Kota</th>
                        <th class="px-5 py-3.5 font-medium">Kecamatan</th>
                        <th class="px-5 py-3.5 font-medium">Jenis Aset</th>
                        <th class="px-5 py-3.5 font-medium">Luas m²</th>
                        <th class="px-5 py-3.5 font-medium">Tanggal Dibuat</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                    @forelse($assets as $asset)
                        <tr class="transition hover:bg-gray-50/50">
                            <td class="px-5 py-4 font-semibold text-gray-800">
                                {{ $asset->id_asset }}
                            </td>

                            <td class="px-5 py-4">
                                @if($asset->status === 'Clear')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Clear
                                    </span>
                                @elseif($asset->status === 'Proses')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-semibold text-yellow-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                        Proses
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        {{ $asset->status }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                {{ $asset->province_name ?? ($asset->province?->name ?? '-') }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $asset->regency_name ?? ($asset->regency?->name ?? '-') }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $asset->district_name ?? ($asset->district?->name ?? '-') }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $asset->asset_type ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                {{ number_format($asset->area_m2 ?? 0, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4 text-xs text-gray-500">
                                {{ $asset->created_at ? \Carbon\Carbon::parse($asset->created_at)->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-folder-open text-3xl text-gray-300"></i>
                                    <p class="mt-2 font-medium text-gray-700">Tidak ada data aset</p>
                                    <p class="text-xs text-gray-400">Tidak ada laporan aset berdasarkan filter yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($assets->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $assets->links() }}
            </div>
        @endif

    </div>

</div>

@endsection


{{-- ================================================================
     JAVASCRIPT DROPDOWN DINAMIS WILAYAH
================================================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const provinceSelect = document.getElementById('province_id');
    const regencySelect = document.getElementById('regency_id');
    const districtSelect = document.getElementById('district_id');

    if (provinceSelect) {
        provinceSelect.addEventListener('change', function () {
            const provinceId = this.value;

            regencySelect.innerHTML = '<option value="">-- Semua Kabupaten/Kota --</option>';
            districtSelect.innerHTML = '<option value="">-- Semua Kecamatan --</option>';

            if (!provinceId) return;

            fetch(`{{ route('admin.assets.get-regencies') }}?province_id=${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(function (regency) {
                        const option = document.createElement('option');
                        option.value = regency.id;
                        option.textContent = regency.name;
                        regencySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Gagal mengambil data kabupaten:', error));
        });
    }

    if (regencySelect) {
        regencySelect.addEventListener('change', function () {
            const regencyId = this.value;

            districtSelect.innerHTML = '<option value="">-- Semua Kecamatan --</option>';

            if (!regencyId) return;

            fetch(`{{ route('admin.assets.get-districts') }}?regency_id=${regencyId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(function (district) {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Gagal mengambil data kecamatan:', error));
        });
    }

});
</script>
@endpush