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

            {{-- Tombol Sampah khusus Super Admin --}}
            @if(auth()->check() && auth()->user()->role === 'superadmin')
                <a href="{{ route('admin.trash.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-700 transition hover:bg-red-100">
                    <i class="fa-solid fa-trash-can"></i>
                    Sampah Aset
                </a>
            @endif

            {{-- Tombol Import khusus Admin --}}
            @if(auth()->check() && auth()->user()->role === 'admin')
                <button type="button"
                        @click="$dispatch('open-import')"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50">
                    <i class="fa-solid fa-file-import text-brand-500"></i>
                    Import Data
                </button>
            @endif

            {{-- Tambah Aset --}}
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

                {{-- Tombol Filter --}}
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

    {{-- Tabel Aset --}}
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

                                    {{-- Hapus / Soft Delete --}}
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

{{-- Modal Import CSV khusus Admin --}}
@if(auth()->check() && auth()->user()->role === 'admin')
<div x-data="importModal()"
     @open-import.window="openModal()"
     x-cloak>

    <div x-show="open"
         x-transition.opacity
         class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/50 p-4">

        <div @click.outside="!loading && !result && close()"
             class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white shadow-theme-lg">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-800">
                    Import Data Aset
                </h3>

                <button type="button"
                        @click="close()"
                        :disabled="loading"
                        class="text-gray-400 hover:text-gray-600 disabled:opacity-50">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- STATE 1: Form Upload --}}
            <div x-show="!result" class="space-y-4 p-6">

                <div @dragover.prevent="drag = true"
                     @dragleave.prevent="drag = false"
                     @drop.prevent="onDrop($event)"
                     @click="$refs.file.click()"
                     :class="drag ? 'border-brand-500 bg-brand-50' : 'border-gray-300 bg-gray-50'"
                     class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-4 py-10 text-center transition">

                    <i class="fa-solid fa-cloud-arrow-up mb-3 text-3xl text-brand-500"></i>

                    <template x-if="!file">
                        <div>
                            <p class="text-sm font-medium text-gray-700">
                                Tarik file CSV ke sini
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                atau klik untuk memilih file (maks. 5 MB)
                            </p>
                        </div>
                    </template>

                    <template x-if="file">
                        <div>
                            <p class="text-sm font-semibold text-gray-800" x-text="file.name"></p>
                            <p class="mt-1 text-xs text-gray-400"
                               x-text="(file.size / 1024).toFixed(1) + ' KB'"></p>
                        </div>
                    </template>

                    <input type="file"
                           x-ref="file"
                           accept=".csv,text/csv"
                           class="hidden"
                           @change="setFile($event.target.files[0])">
                </div>

                <p x-show="error"
                   x-text="error"
                   class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                </p>

                <a href="{{ route('admin.import.template') }}"
                   class="inline-flex items-center gap-2 text-xs font-medium text-brand-500 hover:text-brand-600">
                    <i class="fa-solid fa-download"></i>
                    Unduh template CSV
                </a>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            @click="close()"
                            :disabled="loading"
                            class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                        Batal
                    </button>

                    <button type="button"
                            @click="submit()"
                            :disabled="!file || loading"
                            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid"
                           :class="loading ? 'fa-spinner fa-spin' : 'fa-file-import'"></i>
                        <span x-text="loading ? 'Memproses...' : 'Mulai Import'"></span>
                    </button>
                </div>
            </div>

            {{-- STATE 2: Hasil Import --}}
            <div x-show="result" class="space-y-4 p-6">

                {{-- Berhasil --}}
                <div x-show="result && result.success_count > 0"
                     class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4">
                    <i class="fa-solid fa-circle-check mt-0.5 text-lg text-green-600"></i>

                    <div>
                        <p class="text-sm font-semibold text-green-800">
                            Import berhasil
                        </p>
                        <p class="text-sm text-green-700">
                            Berhasil menambahkan
                            <b x-text="result?.success_count"></b>
                            data aset.
                        </p>
                    </div>
                </div>

                {{-- Tidak Ada yang Berhasil --}}
                <div x-show="result && result.success_count === 0"
                     class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4">
                    <i class="fa-solid fa-circle-xmark mt-0.5 text-lg text-red-600"></i>
                    <p class="text-sm font-semibold text-red-800">
                        Tidak ada data yang berhasil ditambahkan.
                    </p>
                </div>

                {{-- Daftar Data Gagal --}}
                <div x-show="result && result.failure_count > 0"
                     class="overflow-hidden rounded-xl border border-red-200">

                    <div class="bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-800">
                        <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
                        <span x-text="result?.failure_count"></span>
                        data gagal diimpor
                    </div>

                    <ul class="max-h-64 divide-y divide-gray-100 overflow-y-auto text-sm">
                        <template x-for="f in (result?.failures ?? [])"
                                  :key="f.row">
                            <li class="px-4 py-2.5">
                                <p class="text-gray-800">
                                    Aset
                                    <span class="font-mono font-semibold"
                                          x-text="f.id_asset"></span>

                                    <span class="text-xs text-gray-400">
                                        (baris <span x-text="f.row"></span>)
                                    </span>

                                    tidak berhasil ditambahkan
                                </p>

                                <p class="mt-0.5 text-xs text-red-600"
                                   x-text="f.reasons"></p>
                            </li>
                        </template>
                    </ul>
                </div>

                {{-- Tombol Hasil --}}
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            @click="reset()"
                            class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Import lagi
                    </button>

                    <button type="button"
                            @click="finish()"
                            class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')

{{-- Script Modal Import --}}
@if(auth()->check() && auth()->user()->role === 'admin')
<script>
function importModal() {
    return {
        open: false,
        drag: false,
        loading: false,
        file: null,
        error: '',
        result: null,

        openModal() {
            this.reset();
            this.open = true;
        },

        close() {
            if (!this.loading) {
                this.open = false;
            }
        },

        reset() {
            this.file = null;
            this.error = '';
            this.result = null;
            this.drag = false;

            if (this.$refs.file) {
                this.$refs.file.value = '';
            }
        },

        finish() {
            const added = this.result && this.result.success_count > 0;

            this.open = false;

            if (added) {
                window.location.reload();
            }
        },

        onDrop(e) {
            this.drag = false;
            this.setFile(e.dataTransfer.files[0]);
        },

        setFile(f) {
            this.error = '';

            if (!f) return;

            if (!/\.csv$/i.test(f.name)) {
                this.file = null;
                this.error = 'File harus berformat .csv';
                return;
            }

            if (f.size > 5 * 1024 * 1024) {
                this.file = null;
                this.error = 'Ukuran file maksimal 5 MB.';
                return;
            }

            this.file = f;
        },

        async submit() {
            if (!this.file || this.loading) return;

            this.loading = true;
            this.error = '';

            const fd = new FormData();
            fd.append('csv_file', this.file);

            try {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');

                if (!csrfMeta) {
                    throw new Error('Token CSRF tidak ditemukan.');
                }

                const res = await fetch(@json(route('admin.import.store')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfMeta.content,
                        'Accept': 'application/json',
                    },
                    body: fd,
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    this.error =
                        data.errors?.csv_file?.[0] ||
                        data.message ||
                        'Import gagal diproses.';

                    return;
                }

                this.result = data;

            } catch (e) {
                console.error(e);
                this.error = e.message || 'Tidak dapat terhubung ke server.';

            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endif

{{-- Script Dropdown Wilayah --}}
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
                `${regenciesUrl}?province_id=${encodeURIComponent(provinceId)}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
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
                `${districtsUrl}?regency_id=${encodeURIComponent(regencyId)}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
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
