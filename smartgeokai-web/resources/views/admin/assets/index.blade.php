
@extends('layouts.app')

@section('title', 'Daftar Aset')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Aset</h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola seluruh data aset wilayah DAOP 6.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Tombol sampah khusus Super Admin --}}
            @if(auth()->check() && auth()->user()->role === 'superadmin')
                <a href="{{ route('admin.trash.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-700 transition hover:bg-red-100">
                    <i class="fa-solid fa-trash-can"></i>
                    Sampah Aset
                </a>
            @endif

            <a href="{{ route('admin.assets.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                <i class="fa-solid fa-plus"></i>
                Tambah Aset
            </a>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
        <div class="border-b border-gray-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-gray-800">
                <i class="fa-solid fa-filter mr-2"></i>Filter Aset
            </h2>
            <p class="mt-1 text-xs text-gray-400">
                Gunakan filter untuk menemukan aset tertentu.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.assets.index') }}" class="p-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                {{-- Provinsi --}}
                <div>
                    <label for="province_id" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Provinsi
                    </label>
                    <select name="province_id" id="province_id"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}"
                                {{ (string) request('province_id') === (string) $province->id ? 'selected' : '' }}>
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
                    <select name="regency_id" id="regency_id"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                        <option value="">Semua Kabupaten/Kota</option>
                        @foreach($regencies as $regency)
                            <option value="{{ $regency->id }}"
                                {{ (string) request('regency_id') === (string) $regency->id ? 'selected' : '' }}>
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
                    <select name="district_id" id="district_id"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                        <option value="">Semua Kecamatan</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}"
                                {{ (string) request('district_id') === (string) $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipe --}}
                <div>
                    <label for="asset_type" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Tipe Aset
                    </label>
                    <select name="asset_type" id="asset_type"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="Tanah" {{ request('asset_type') === 'Tanah' ? 'selected' : '' }}>
                            Tanah
                        </option>
                        <option value="Bangunan" {{ request('asset_type') === 'Bangunan' ? 'selected' : '' }}>
                            Bangunan
                        </option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Status Aset
                    </label>
                    <select name="status" id="status"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                        <option value="">Semua Status</option>
                        <option value="Clear" {{ request('status') === 'Clear' ? 'selected' : '' }}>
                            Clear
                        </option>
                        <option value="Proses" {{ request('status') === 'Proses' ? 'selected' : '' }}>
                            Proses
                        </option>
                        <option value="Masalah" {{ request('status') === 'Masalah' ? 'selected' : '' }}>
                            Masalah
                        </option>
                    </select>
                </div>

                {{-- Pencarian --}}
                <div class="md:col-span-2">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Pencarian
                    </label>
                    <input type="text" name="search" id="search"
                           value="{{ request('search') }}"
                           placeholder="Cari ID aset atau deskripsi..."
                           class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
                </div>

                {{-- Tombol filter --}}
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                        <i class="fa-solid fa-filter"></i>
                        Terapkan
                    </button>

                    <a href="{{ route('admin.assets.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50">
                        <i class="fa-solid fa-rotate-left"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel aset --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">

        <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Data Aset</h2>
                <p class="text-xs text-gray-400">
                    Daftar aset sesuai filter yang dipilih.
                </p>
            </div>

            <p class="text-sm text-gray-500">
                Total:
                <span class="font-semibold text-gray-800">
                    {{ $assets->total() }}
                </span>
                aset
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="whitespace-nowrap px-5 py-3 font-semibold">ID Aset</th>
                        <th class="whitespace-nowrap px-5 py-3 font-semibold">Tipe</th>
                        <th class="whitespace-nowrap px-5 py-3 font-semibold">Klasifikasi</th>
                        <th class="whitespace-nowrap px-5 py-3 font-semibold">Wilayah</th>
                        <th class="whitespace-nowrap px-5 py-3 font-semibold">Luas</th>
                        <th class="whitespace-nowrap px-5 py-3 font-semibold">Status</th>
                        <th class="whitespace-nowrap px-5 py-3 font-semibold">Diperbarui</th>
                        <th class="whitespace-nowrap px-5 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $asset)
                        <tr class="transition hover:bg-gray-50">

                            <td class="whitespace-nowrap px-5 py-4 font-mono font-semibold text-gray-800">
                                {{ $asset->id_asset }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                {{ $asset->asset_type ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                {{ $asset->classification ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-800">
                                    {{ $asset->district->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $asset->regency->name ?? '-' }},
                                    {{ $asset->province->name ?? '-' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                {{ number_format((float) ($asset->area_m2 ?? 0), 0, ',', '.') }} m²
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if($asset->status === 'Clear')
                                    <span class="inline-flex rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                        Clear
                                    </span>
                                @elseif($asset->status === 'Proses')
                                    <span class="inline-flex rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-semibold text-yellow-700">
                                        Proses
                                    </span>
                                @elseif($asset->status === 'Masalah')
                                    <span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                        Masalah
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                        {{ $asset->status ?? 'Tidak diketahui' }}
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-xs text-gray-500">
                                {{ $asset->updated_at ? $asset->updated_at->format('d/m/Y') : '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Detail --}}
                                    <a href="{{ route('admin.assets.show', $asset) }}"
                                       title="Detail aset"
                                       class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.assets.edit', $asset) }}"
                                       title="Edit aset"
                                       class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-yellow-50 hover:text-yellow-600">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- Hapus / soft delete --}}
                                    <form action="{{ route('admin.assets.destroy', $asset) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus aset ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                title="Pindahkan ke sampah"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-600">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-box-open text-3xl text-gray-300"></i>
                                    <p class="mt-3 font-medium text-gray-600">Tidak ada data aset</p>
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
        @if($assets->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $assets->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province_id');
    const regencySelect = document.getElementById('regency_id');
    const districtSelect = document.getElementById('district_id');

    if (!provinceSelect || !regencySelect || !districtSelect) {
        return;
    }

    const regenciesUrl = @json(route('admin.assets.get-regencies'));
    const districtsUrl = @json(route('admin.assets.get-districts'));

    provinceSelect.addEventListener('change', async function () {
        const provinceId = this.value;

        regencySelect.innerHTML = '<option value="">Semua Kabupaten/Kota</option>';
        districtSelect.innerHTML = '<option value="">Semua Kecamatan</option>';

        if (!provinceId) {
            return;
        }

        try {
            const response = await fetch(
                `${regenciesUrl}?province_id=${encodeURIComponent(provinceId)}`
            );

            if (!response.ok) {
                throw new Error('Gagal mengambil data kabupaten.');
            }

            const data = await response.json();

            data.forEach(function (regency) {
                const option = document.createElement('option');
                option.value = regency.id;
                option.textContent = regency.name;
                regencySelect.appendChild(option);
            });
        } catch (error) {
            console.error(error);
        }
    });

    regencySelect.addEventListener('change', async function () {
        const regencyId = this.value;

        districtSelect.innerHTML = '<option value="">Semua Kecamatan</option>';

        if (!regencyId) {
            return;
        }

        try {
            const response = await fetch(
                `${districtsUrl}?regency_id=${encodeURIComponent(regencyId)}`
            );

            if (!response.ok) {
                throw new Error('Gagal mengambil data kecamatan.');
            }

            const data = await response.json();

            data.forEach(function (district) {
                const option = document.createElement('option');
                option.value = district.id;
                option.textContent = district.name;
                districtSelect.appendChild(option);
            });
        } catch (error) {
            console.error(error);
        }
    });
});
</script>
@endpush
