
@extends('layouts.app')

@section('title', 'Edit Aset')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Aset</h1>
    <p class="text-sm text-gray-500">
        Perbarui informasi dan foto aset {{ $asset->id_asset }}.
    </p>
</div>

@if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
        <p class="font-semibold text-red-700">Terdapat kesalahan pada form.</p>
        <ul class="mt-2 list-inside list-disc text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">
    <form
        action="{{ route('admin.assets.update', $asset) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            {{-- ID ASET --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    ID Aset <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="id_asset"
                    value="{{ old('id_asset', $asset->id_asset) }}"
                    maxlength="20"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >
                @error('id_asset')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- JENIS ASET --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Jenis Aset <span class="text-red-500">*</span>
                </label>
                <select
                    name="asset_type"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Jenis Aset --</option>
                    <option value="Tanah" @selected(old('asset_type', $asset->asset_type) === 'Tanah')>
                        Tanah
                    </option>
                    <option value="Bangunan" @selected(old('asset_type', $asset->asset_type) === 'Bangunan')>
                        Bangunan
                    </option>
                </select>
                @error('asset_type')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- PROVINSI --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Provinsi <span class="text-red-500">*</span>
                </label>
                <select
                    id="province_id"
                    name="province_id"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach ($provinces as $province)
                        <option
                            value="{{ $province->id }}"
                            @selected(old('province_id', $asset->province_id) == $province->id)
                        >
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
                @error('province_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- KABUPATEN/KOTA --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Kabupaten/Kota <span class="text-red-500">*</span>
                </label>
                <select
                    id="regency_id"
                    name="regency_id"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Kabupaten/Kota --</option>
                    @foreach ($regencies as $regency)
                        <option
                            value="{{ $regency->id }}"
                            @selected(old('regency_id', $asset->regency_id) == $regency->id)
                        >
                            {{ $regency->name }}
                        </option>
                    @endforeach
                </select>
                @error('regency_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- KECAMATAN --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Kecamatan <span class="text-red-500">*</span>
                </label>
                <select
                    id="district_id"
                    name="district_id"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Kecamatan --</option>
                    @foreach ($districts as $district)
                        <option
                            value="{{ $district->id }}"
                            @selected(old('district_id', $asset->district_id) == $district->id)
                        >
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
                @error('district_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- KLASIFIKASI --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Klasifikasi <span class="text-red-500">*</span>
                </label>
                <select
                    name="classification"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Klasifikasi --</option>
                    <option value="ROW" @selected(old('classification', $asset->classification) === 'ROW')>
                        ROW
                    </option>
                    <option value="Fasilitas" @selected(old('classification', $asset->classification) === 'Fasilitas')>
                        Fasilitas
                    </option>
                    <option value="Operasional" @selected(old('classification', $asset->classification) === 'Operasional')>
                        Operasional
                    </option>
                </select>
                @error('classification')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- KELOMPOK ASET --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Kelompok Aset <span class="text-red-500">*</span>
                </label>
                <select
                    name="asset_group"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Kelompok --</option>
                    <option value="Produksi" @selected(old('asset_group', $asset->asset_group) === 'Produksi')>
                        Produksi
                    </option>
                    <option value="Non Produksi" @selected(old('asset_group', $asset->asset_group) === 'Non Produksi')>
                        Non Produksi
                    </option>
                </select>
                @error('asset_group')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- STATUS --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    name="status"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Status --</option>
                    <option value="Clear" @selected(old('status', $asset->status) === 'Clear')>
                        Clear
                    </option>
                    <option value="Proses" @selected(old('status', $asset->status) === 'Proses')>
                        Proses
                    </option>
                    <option value="Masalah" @selected(old('status', $asset->status) === 'Masalah')>
                        Masalah
                    </option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- LUAS ASET --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Luas Aset (m²) <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    name="area_m2"
                    value="{{ old('area_m2', $asset->area_m2) }}"
                    step="any"
                    min="0"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >
                @error('area_m2')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- LATITUDE --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Latitude <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="latitude"
                    name="latitude"
                    value="{{ old('latitude', $asset->latitude) }}"
                    step="any"
                    min="-90"
                    max="90"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >
                @error('latitude')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- LONGITUDE --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Longitude <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="longitude"
                    name="longitude"
                    value="{{ old('longitude', $asset->longitude) }}"
                    step="any"
                    min="-180"
                    max="180"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >
                @error('longitude')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- GOOGLE MAPS URL --}}
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Link Google Maps
                </label>
                <input
                    type="url"
                    name="gmaps_url"
                    value="{{ old('gmaps_url', $asset->gmaps_url) }}"
                    placeholder="https://maps.google.com/..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >
                @error('gmaps_url')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- DESKRIPSI --}}
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Deskripsi / Keterangan
                </label>
                <textarea
                    name="description"
                    rows="4"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >{{ old('description', $asset->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- FOTO LAMA --}}
            <div class="md:col-span-2">
                <label class="mb-3 block text-sm font-medium text-gray-700">
                    Foto Aset Saat Ini
                </label>

                @if ($asset->images->count() > 0)
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                        @foreach ($asset->images as $img)
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <img
                                    src="{{ asset('storage/' . $img->file_path) }}"
                                    alt="{{ $img->file_name ?? 'Foto aset' }}"
                                    class="h-32 w-full object-cover"
                                    onerror="this.style.display='none'"
                                >

                                <label class="flex cursor-pointer items-center gap-2 border-t border-gray-100 p-3 text-sm text-red-600">
                                    <input
                                        type="checkbox"
                                        name="delete_images[]"
                                        value="{{ $img->id }}"
                                        @checked(in_array($img->id, old('delete_images', [])))
                                        class="h-4 w-4 rounded"
                                    >
                                    Hapus foto ini
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Centang foto yang ingin dihapus, lalu simpan perubahan.
                    </p>
                @else
                    <p class="text-sm italic text-gray-400">
                        Belum ada foto untuk aset ini.
                    </p>
                @endif

                @error('delete_images')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('delete_images.*')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- FOTO BARU --}}
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Tambah Foto Baru
                </label>

                <input
                    id="new_images"
                    type="file"
                    name="photos[]"
                    multiple
                    accept="image/jpeg,image/png,image/webp"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >

                <p id="new-image-count" class="mt-1 text-xs text-gray-500">
                    Pilih foto baru jika diperlukan. Tidak wajib jika tidak menambah foto.
                </p>

                <div
                    id="new-image-preview"
                    class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4"
                ></div>

                @error('photos')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('photos.*')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- TOMBOL --}}
        <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
            <a
                href="{{ route('admin.assets.show', $asset) }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Batal
            </a>

            <button
                type="submit"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
            >
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province_id');
    const regencySelect = document.getElementById('regency_id');
    const districtSelect = document.getElementById('district_id');

    const regenciesUrl = @json(route('admin.api.regencies'));
    const districtsUrl = @json(route('admin.api.districts'));

    const selectedRegency = @json(old('regency_id', $asset->regency_id));
    const selectedDistrict = @json(old('district_id', $asset->district_id));

    async function loadRegencies(provinceId, selectedId = '') {
        regencySelect.innerHTML =
            '<option value="">-- Pilih Kabupaten/Kota --</option>';
        districtSelect.innerHTML =
            '<option value="">-- Pilih Kecamatan --</option>';

        if (!provinceId) return;

        try {
            const response = await fetch(
                `${regenciesUrl}?province_id=${encodeURIComponent(provinceId)}`,
                { headers: { 'Accept': 'application/json' } }
            );

            if (!response.ok) throw new Error('Gagal memuat kabupaten/kota');

            const data = await response.json();

            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;
                option.selected = String(item.id) === String(selectedId);
                regencySelect.appendChild(option);
            });
        } catch (error) {
            console.error(error);
            alert('Kabupaten/kota gagal dimuat. Silakan muat ulang halaman.');
        }
    }

    async function loadDistricts(regencyId, selectedId = '') {
        districtSelect.innerHTML =
            '<option value="">-- Pilih Kecamatan --</option>';

        if (!regencyId) return;

        try {
            const response = await fetch(
                `${districtsUrl}?regency_id=${encodeURIComponent(regencyId)}`,
                { headers: { 'Accept': 'application/json' } }
            );

            if (!response.ok) throw new Error('Gagal memuat kecamatan');

            const data = await response.json();

            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;
                option.selected = String(item.id) === String(selectedId);
                districtSelect.appendChild(option);
            });
        } catch (error) {
            console.error(error);
            alert('Kecamatan gagal dimuat. Silakan muat ulang halaman.');
        }
    }

    provinceSelect.addEventListener('change', function () {
        loadRegencies(this.value);
    });

    regencySelect.addEventListener('change', function () {
        loadDistricts(this.value);
    });

    // Muat kembali wilayah yang sudah tersimpan atau input lama.
    if (provinceSelect.value) {
        loadRegencies(provinceSelect.value, selectedRegency).then(() => {
            if (regencySelect.value) {
                loadDistricts(regencySelect.value, selectedDistrict);
            }
        });
    }

    // Preview foto baru.
    const photoInput = document.getElementById('new_images');
    const photoPreview = document.getElementById('new-image-preview');
    const photoCount = document.getElementById('new-image-count');

    photoInput.addEventListener('change', function () {
        photoPreview.innerHTML = '';

        const files = Array.from(this.files || []);
        photoCount.textContent = files.length
            ? `${files.length} foto baru dipilih.`
            : 'Pilih foto baru jika diperlukan. Tidak wajib jika tidak menambah foto.';

        files.forEach(file => {
            const card = document.createElement('div');
            card.className = 'overflow-hidden rounded-xl border border-gray-200';

            const image = document.createElement('img');
            image.className = 'h-32 w-full object-cover';
            image.alt = file.name;

            const caption = document.createElement('p');
            caption.className = 'truncate p-2 text-xs text-gray-600';
            caption.textContent = file.name;

            card.appendChild(image);
            card.appendChild(caption);
            photoPreview.appendChild(card);

            const reader = new FileReader();
            reader.onload = event => {
                image.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endpush
