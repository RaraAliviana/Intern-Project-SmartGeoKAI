@extends('layouts.app')

@section('title', 'Daftar Aset')

@section('content')

<div class="space-y-6">

    {{-- ============================================================
        HEADER
    ============================================================= --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Daftar Aset
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kelola seluruh data aset wilayah DAOP 6.
            </p>
        </div>

        <a
            href="{{ route('admin.assets.create') }}"
            class="inline-flex w-fit items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Aset
        </a>

    </div>


    {{-- ============================================================
        FILTER
    ============================================================= --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs">

        {{-- Header Filter --}}
        <div class="border-b border-gray-100 px-5 py-4">

            <div class="flex items-center gap-2">

                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-500">
                    <i class="fa-solid fa-filter"></i>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-800">
                        Filter Aset
                    </h2>

                    <p class="text-xs text-gray-400">
                        Gunakan filter untuk mencari aset tertentu.
                    </p>
                </div>

            </div>

        </div>


        {{-- Form Filter --}}
        <form
            method="GET"
            action="{{ route('admin.assets.index') }}"
            class="p-5"
        >

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">


                {{-- Provinsi --}}
                <div>
                    <label
                        for="province_id"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Provinsi
                    </label>

                    <select
                        name="province_id"
                        id="province_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10"
                    >

                        <option value="">
                            Semua Provinsi
                        </option>

                        @foreach($provinces as $province)

                            <option
                                value="{{ $province->id }}"
                                {{ request('province_id') == $province->id ? 'selected' : '' }}
                            >
                                {{ $province->name }}
                            </option>

                        @endforeach

                    </select>
                </div>


                {{-- Kabupaten --}}
                <div>
                    <label
                        for="regency_id"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Kabupaten
                    </label>

                    <select
                        name="regency_id"
                        id="regency_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10"
                    >

                        <option value="">
                            Semua Kabupaten
                        </option>

                        @foreach($regencies as $regency)

                            <option
                                value="{{ $regency->id }}"
                                {{ request('regency_id') == $regency->id ? 'selected' : '' }}
                            >
                                {{ $regency->name }}
                            </option>

                        @endforeach

                    </select>
                </div>


                {{-- Kecamatan --}}
                <div>
                    <label
                        for="district_id"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Kecamatan
                    </label>

                    <select
                        name="district_id"
                        id="district_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10"
                    >

                        <option value="">
                            Semua Kecamatan
                        </option>

                        @foreach($districts as $district)

                            <option
                                value="{{ $district->id }}"
                                {{ request('district_id') == $district->id ? 'selected' : '' }}
                            >
                                {{ $district->name }}
                            </option>

                        @endforeach

                    </select>
                </div>


                {{-- Tipe Aset --}}
                <div>
                    <label
                        for="asset_type"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Tipe Aset
                    </label>

                    <select
                        name="asset_type"
                        id="asset_type"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10"
                    >

                        <option value="">
                            Semua Tipe
                        </option>

                        <option
                            value="Tanah"
                            {{ request('asset_type') === 'Tanah' ? 'selected' : '' }}
                        >
                            Tanah
                        </option>

                        <option
                            value="Bangunan"
                            {{ request('asset_type') === 'Bangunan' ? 'selected' : '' }}
                        >
                            Bangunan
                        </option>

                    </select>
                </div>


                {{-- Status --}}
                <div>
                    <label
                        for="status"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Status Aset
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="Clear"
                            {{ request('status') === 'Clear' ? 'selected' : '' }}
                        >
                            Clear
                        </option>

                        <option
                            value="Proses"
                            {{ request('status') === 'Proses' ? 'selected' : '' }}
                        >
                            Proses
                        </option>

                        <option
                            value="Masalah"
                            {{ request('status') === 'Masalah' ? 'selected' : '' }}
                        >
                            Masalah
                        </option>

                    </select>
                </div>


                {{-- Pencarian --}}
                <div class="md:col-span-2">

                    <label
                        for="search"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Pencarian
                    </label>

                    <div class="relative">

                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="{{ request('search') }}"
                            placeholder="Cari ID atau nama/deskripsi aset..."
                            class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10"
                        >

                    </div>

                </div>


                {{-- Tombol --}}
                <div class="flex items-end gap-2">

                    <button
                        type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600"
                    >
                        <i class="fa-solid fa-filter"></i>
                        Terapkan
                    </button>

                    <a
                        href="{{ route('admin.assets.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                    >
                        <i class="fa-solid fa-rotate-left"></i>
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- ============================================================
        TABEL DAFTAR ASET
    ============================================================= --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">

        {{-- Header tabel --}}
        <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="text-base font-semibold text-gray-800">
                    Daftar Aset
                </h2>

                <p class="text-xs text-gray-400">
                    Menampilkan data aset berdasarkan filter yang dipilih.
                </p>

            </div>

            <div class="text-sm text-gray-500">

                Total:
                <span class="font-semibold text-gray-800">
                    {{ $assets->total() }}
                </span>
                aset

            </div>

        </div>


        {{-- Tabel --}}
        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            ID Aset
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Tipe
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Klasifikasi
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Wilayah
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Luas
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Terakhir Diubah
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse($assets as $asset)

                        <tr class="transition hover:bg-gray-50">

                            {{-- ID --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <span class="text-sm font-semibold text-gray-800">
                                    {{ $asset->id_asset }}
                                </span>

                            </td>


                            {{-- Tipe --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <span class="text-sm text-gray-600">
                                    {{ $asset->asset_type }}
                                </span>

                            </td>


                            {{-- Klasifikasi --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <span class="text-sm text-gray-600">
                                    {{ $asset->classification ?? '-' }}
                                </span>

                            </td>


                            {{-- Wilayah --}}
                            <td class="px-5 py-4">

                                <div class="min-w-[180px]">

                                    <p class="text-sm font-medium text-gray-700">
                                        {{ $asset->district->name ?? '-' }}
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        {{ $asset->regency->name ?? '-' }},
                                        {{ $asset->province->name ?? '-' }}
                                    </p>

                                </div>

                            </td>


                            {{-- Luas --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <span class="text-sm text-gray-600">
                                    {{ number_format($asset->area_m2 ?? 0, 0, ',', '.') }}
                                    m²
                                </span>

                            </td>


                            {{-- Status --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                @php

                                    $statusClass = match($asset->status) {

                                        'Clear'
                                            => 'bg-success-50 text-success-600',

                                        'Proses'
                                            => 'bg-warning-50 text-warning-600',

                                        'Masalah'
                                            => 'bg-error-50 text-error-600',

                                        default
                                            => 'bg-gray-100 text-gray-600',

                                    };

                                @endphp


                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}"
                                >

                                    {{ $asset->status }}

                                </span>

                            </td>


                            {{-- Updated --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <span class="text-sm text-gray-500">
                                    {{ $asset->updated_at?->format('d/m/Y') ?? '-' }}
                                </span>

                            </td>


                            {{-- Aksi --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- Detail --}}
                                    <a
                                        href="{{ route('admin.assets.show', $asset) }}"
                                        title="Lihat detail"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-500"
                                    >
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </a>


                                    {{-- Edit --}}
                                    <a
                                        href="{{ route('admin.assets.edit', $asset) }}"
                                        title="Edit aset"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-warning-200 hover:bg-warning-50 hover:text-warning-600"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>


                                    {{-- Hapus --}}
                                    <form
                                        action="{{ route('admin.assets.destroy', $asset) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus aset {{ $asset->id_asset }}?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Hapus aset"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-error-200 hover:bg-error-50 hover:text-error-600"
                                        >
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-5 py-12 text-center"
                            >

                                <div class="flex flex-col items-center justify-center">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">

                                        <i class="fa-solid fa-box-open text-xl"></i>

                                    </div>

                                    <p class="mt-4 text-sm font-medium text-gray-600">
                                        Tidak ada data aset
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Belum ada aset yang sesuai dengan filter.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if($assets->hasPages() || $assets->total() > 0)

            <div class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <p class="text-sm text-gray-500">

                    Menampilkan

                    <span class="font-medium text-gray-700">
                        {{ $assets->firstItem() ?? 0 }}
                    </span>

                    –

                    <span class="font-medium text-gray-700">
                        {{ $assets->lastItem() ?? 0 }}
                    </span>

                    dari

                    <span class="font-medium text-gray-700">
                        {{ $assets->total() }}
                    </span>

                    aset

                </p>


                <div>
                    {{ $assets->links() }}
                </div>

            </div>

        @endif

    </div>

</div>

@endsection


{{-- ================================================================
    JAVASCRIPT DROPDOWN WILAYAH
================================================================ --}}
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const provinceSelect = document.getElementById('province_id');
    const regencySelect = document.getElementById('regency_id');
    const districtSelect = document.getElementById('district_id');

    // MENGGUNAKAN NAME ROUTE YANG SUDAH TERDAFTAR DI WEB.PHP
    const regenciesUrl = '{{ route("admin.assets.get-regencies") }}';
    const districtsUrl = '{{ route("admin.assets.get-districts") }}';


    // ============================================================
    // PROVINSI -> KABUPATEN
    // ============================================================

    if (provinceSelect) {

        provinceSelect.addEventListener('change', function () {

            const provinceId = this.value;

            regencySelect.innerHTML =
                '<option value="">Semua Kabupaten</option>';

            districtSelect.innerHTML =
                '<option value="">Semua Kecamatan</option>';

            if (!provinceId) {
                return;
            }

            fetch(`${regenciesUrl}?province_id=${provinceId}`)

                .then(response => {

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data kabupaten.');
                    }

                    return response.json();

                })

                .then(data => {

                    data.forEach(regency => {

                        const option =
                            document.createElement('option');

                        option.value = regency.id;
                        option.textContent = regency.name;

                        regencySelect.appendChild(option);

                    });

                })

                .catch(error => {

                    console.error(error);

                });

        });

    }


    // ============================================================
    // KABUPATEN -> KECAMATAN
    // ============================================================

    if (regencySelect) {

        regencySelect.addEventListener('change', function () {

            const regencyId = this.value;

            districtSelect.innerHTML =
                '<option value="">Semua Kecamatan</option>';

            if (!regencyId) {
                return;
            }

            fetch(`${districtsUrl}?regency_id=${regencyId}`)

                .then(response => {

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data kecamatan.');
                    }

                    return response.json();

                })

                .then(data => {

                    data.forEach(district => {

                        const option =
                            document.createElement('option');

                        option.value = district.id;
                        option.textContent = district.name;

                        districtSelect.appendChild(option);

                    });

                })

                .catch(error => {

                    console.error(error);

                });

        });

    }

});

</script>

@endpush