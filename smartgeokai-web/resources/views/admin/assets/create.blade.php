
@extends('layouts.app')

@section('title', 'Tambah Aset')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Aset</h1>
    <p class="text-sm text-gray-500">
        Lengkapi informasi aset dan unggah foto aset.
    </p>
</div>

@if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
        <p class="font-semibold text-red-700">
            Terdapat kesalahan pada form.
        </p>
        <ul class="mt-2 list-inside list-disc text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ route('admin.assets.store') }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf

    {{-- IDENTITAS ASET --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">
        <div class="mb-5 border-b border-gray-100 pb-4">
            <h2 class="text-base font-semibold text-gray-800">
                Identitas Aset
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Informasi dasar mengenai aset.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    ID Aset <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="id_asset"
                    value="{{ old('id_asset') }}"
                    placeholder="Contoh: 06.01.00001"
                    maxlength="20"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Tipe Aset <span class="text-red-500">*</span>
                </label>
                <select
                    name="asset_type"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Tanah" @selected(old('asset_type') === 'Tanah')>
                        Tanah
                    </option>
                    <option value="Bangunan" @selected(old('asset_type') === 'Bangunan')>
                        Bangunan
                    </option>
                </select>
            </div>

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
                    <option value="ROW" @selected(old('classification') === 'ROW')>
                        ROW
                    </option>
                    <option value="Fasilitas" @selected(old('classification') === 'Fasilitas')>
                        Fasilitas
                    </option>
                    <option value="Operasional" @selected(old('classification') === 'Operasional')>
                        Operasional
                    </option>
                </select>
            </div>

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
                    <option value="Produksi" @selected(old('asset_group') === 'Produksi')>
                        Produksi
                    </option>
                    <option value="Non Produksi" @selected(old('asset_group') === 'Non Produksi')>
                        Non Produksi
                    </option>
                </select>
            </div>
        </div>
    </div>

    {{-- WILAYAH DAN KOORDINAT --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">
        <div class="mb-5 border-b border-gray-100 pb-4">
            <h2 class="text-base font-semibold text-gray-800">
                Lokasi & Wilayah
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Pilih wilayah dan tentukan titik lokasi pada peta.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Provinsi <span class="text-red-500">*</span>
                </label>
                <select
                    name="province_id"
                    id="province_id"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach ($provinces as $province)
                        <option
                            value="{{ $province->id }}"
                            @selected(old('province_id') == $province->id)
                        >
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Kabupaten/Kota <span class="text-red-500">*</span>
                </label>
                <select
                    name="regency_id"
                    id="regency_id"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Kabupaten/Kota --</option>
                    @foreach ($regencies as $regency)
                        <option
                            value="{{ $regency->id }}"
                            @selected(old('regency_id') == $regency->id)
                        >
                            {{ $regency->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Kecamatan <span class="text-red-500">*</span>
                </label>
                <select
                    name="district_id"
                    id="district_id"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
                >
                    <option value="">-- Pilih Kecamatan --</option>
                    @foreach ($districts as $district)
                        <option
                            value="{{ $district->id }}"
                            @selected(old('district_id') == $district->id)
                        >
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Luas Aset (m²) <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    name="area_m2"
                    value="{{ old('area_m2') }}"
                    step="any"
                    min="0"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Latitude <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="latitude"
                    name="latitude"
                    value="{{ old('latitude') }}"
                    step="any"
                    min="-90"
                    max="90"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Longitude <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="longitude"
                    name="longitude"
                    value="{{ old('longitude') }}"
                    step="any"
                    min="-180"
                    max="180"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                >
            </div>
        </div>

        <div class="mt-5">
            <label class="mb-2 block text-sm font-medium text-gray-700">
                Pilih Titik Lokasi pada Peta
            </label>
            <div class="relative z-0 h-[350px] w-full overflow-hidden rounded-xl border border-gray-200">
                <div id="pickerMap" class="h-full w-full"></div>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                Klik peta untuk menentukan koordinat aset.
            </p>
        </div>

        <div class="mt-5">
            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Google Maps URL
            </label>
            <input
                type="url"
                name="gmaps_url"
                value="{{ old('gmaps_url') }}"
                placeholder="https://maps.google.com/..."
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
            >
        </div>
    </div>

    {{-- FOTO ASET --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">
        <div class="mb-5 border-b border-gray-100 pb-4">
            <h2 class="text-base font-semibold text-gray-800">
                Foto Aset <span class="text-red-500">*</span>
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Unggah minimal satu foto. Kamu dapat memilih beberapa foto sekaligus.
            </p>
        </div>

        <label
            for="images"
            class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center transition hover:bg-gray-100"
        >
            <i class="fa-solid fa-images text-4xl text-gray-400"></i>

            <p class="mt-3 text-sm font-medium text-gray-700">
                Pilih foto aset
            </p>

            <p class="mt-1 text-xs text-gray-500">
                JPG, JPEG, PNG, WEBP — maksimal 5 MB per foto
            </p>

            <span class="mt-4 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white">
                Pilih Foto
            </span>

            {{-- PENTING: harus bernama photos[], sesuai AssetController --}}
            <input
                id="images"
                name="photos[]"
                type="file"
                multiple
                accept="image/jpeg,image/png,image/webp"
                required
                class="hidden"
            >
        </label>

        <p id="image-count" class="mt-3 text-sm text-gray-600">
            Belum ada foto yang dipilih.
        </p>

        <div
            id="image-preview"
            class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4"
        ></div>
    </div>

    {{-- STATUS DAN DESKRIPSI --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">
        <div class="mb-5 border-b border-gray-100 pb-4">
            <h2 class="text-base font-semibold text-gray-800">
                Kondisi Aset
            </h2>
        </div>

        <div class="mb-5">
            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Status <span class="text-red-500">*</span>
            </label>
            <select
                name="status"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm"
            >
                <option value="">-- Pilih Status --</option>
                <option value="Clear" @selected(old('status') === 'Clear')>
                    Clear
                </option>
                <option value="Proses" @selected(old('status') === 'Proses')>
                    Proses
                </option>
                <option value="Masalah" @selected(old('status') === 'Masalah')>
                    Masalah
                </option>
            </select>
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Deskripsi
            </label>
            <textarea
                name="description"
                rows="5"
                placeholder="Masukkan deskripsi atau kondisi aset..."
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
            >{{ old('description') }}</textarea>
        </div>
    </div>

    {{-- TOMBOL --}}
    <div class="flex items-center justify-end gap-3">
        <a
            href="{{ route('admin.assets.index') }}"
            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50"
        >
            Batal
        </a>

        <button
            type="submit"
            class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
        >
            <i class="fa-solid fa-save mr-1"></i>
            Simpan Aset
        </button>
    </div>
</form>
@endsection

{{-- LEAFLET CSS --}}
@push('styles')
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    crossorigin=""
>
@endpush

{{-- LEAFLET JAVASCRIPT --}}
@push('scripts')
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    crossorigin=""
></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /*
    |--------------------------------------------------------------------------
    | DROPDOWN PROVINSI, KABUPATEN, KECAMATAN
    |--------------------------------------------------------------------------
    */

    const provinceSelect = document.getElementById('province_id');
    const regencySelect = document.getElementById('regency_id');
    const districtSelect = document.getElementById('district_id');

    const regenciesUrl = @json(route('admin.api.regencies'));
    const districtsUrl = @json(route('admin.api.districts'));

    const oldRegencyId = @json(old('regency_id'));
    const oldDistrictId = @json(old('district_id'));

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

            if (!response.ok) {
                throw new Error('Gagal memuat kabupaten/kota.');
            }

            const data = await response.json();

            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;

                if (String(item.id) === String(selectedId)) {
                    option.selected = true;
                }

                regencySelect.appendChild(option);
            });
        } catch (error) {
            console.error(error);
            alert('Data kabupaten/kota gagal dimuat. Silakan coba lagi.');
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

            if (!response.ok) {
                throw new Error('Gagal memuat kecamatan.');
            }

            const data = await response.json();

            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;

                if (String(item.id) === String(selectedId)) {
                    option.selected = true;
                }

                districtSelect.appendChild(option);
            });
        } catch (error) {
            console.error(error);
            alert('Data kecamatan gagal dimuat. Silakan coba lagi.');
        }
    }

    provinceSelect.addEventListener('change', function () {
        loadRegencies(this.value);
    });

    regencySelect.addEventListener('change', function () {
        loadDistricts(this.value);
    });

    // Pulihkan pilihan wilayah jika validasi sebelumnya gagal.
    if (provinceSelect.value) {
        loadRegencies(provinceSelect.value, oldRegencyId).then(() => {
            if (regencySelect.value) {
                loadDistricts(regencySelect.value, oldDistrictId);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PETA PEMILIHAN KOORDINAT
    |--------------------------------------------------------------------------
    */

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    const initialLat = parseFloat(latInput.value) || -7.5;
    const initialLng = parseFloat(lngInput.value) || 110.2;

    const mapBounds = L.latLngBounds(
        L.latLng(-8.80, 108.50),
        L.latLng(-6.30, 111.60)
    );

    const pickerMap = L.map('pickerMap', {
        maxBounds: mapBounds,
        maxBoundsViscosity: 1.0,
        minZoom: 8,
        maxZoom: 18
    }).setView([initialLat, initialLng], 9);

    L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            bounds: mapBounds,
            maxZoom: 18,
            attribution: 'Tiles &copy; Esri'
        }
    ).addTo(pickerMap);

    L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
        {
            bounds: mapBounds,
            maxZoom: 18,
            attribution: ''
        }
    ).addTo(pickerMap);

    setTimeout(() => pickerMap.invalidateSize(), 200);

    let marker = null;

    function updateMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(pickerMap);
        }

        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
    }

    if (latInput.value && lngInput.value) {
        marker = L.marker([initialLat, initialLng]).addTo(pickerMap);
    }

    pickerMap.on('click', function (event) {
        updateMarker(event.latlng.lat, event.latlng.lng);
    });

    function updateMapFromInputs() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);

        if (
            Number.isFinite(lat) &&
            Number.isFinite(lng) &&
            lat >= -90 && lat <= 90 &&
            lng >= -180 && lng <= 180
        ) {
            updateMarker(lat, lng);
            pickerMap.panTo([lat, lng]);
        }
    }

    latInput.addEventListener('change', updateMapFromInputs);
    lngInput.addEventListener('change', updateMapFromInputs);

    /*
    |--------------------------------------------------------------------------
    | PREVIEW FOTO
    |--------------------------------------------------------------------------
    */

    const imageInput = document.getElementById('images');
    const preview = document.getElementById('image-preview');
    const imageCount = document.getElementById('image-count');

    imageInput.addEventListener('change', function () {
        preview.innerHTML = '';

        const files = Array.from(this.files || []);
        imageCount.textContent = files.length
            ? `${files.length} foto dipilih.`
            : 'Belum ada foto yang dipilih.';

        files.forEach(file => {
            const wrapper = document.createElement('div');
            wrapper.className =
                'overflow-hidden rounded-xl border border-gray-200 bg-white';

            const img = document.createElement('img');
            img.className = 'h-32 w-full object-cover';
            img.alt = file.name;

            const info = document.createElement('div');
            info.className = 'p-2';

            const filename = document.createElement('p');
            filename.className = 'truncate text-xs font-medium text-gray-700';
            filename.textContent = file.name;

            const filesize = document.createElement('p');
            filesize.className = 'text-xs text-gray-400';
            filesize.textContent =
                `${(file.size / 1024 / 1024).toFixed(2)} MB`;

            info.appendChild(filename);
            info.appendChild(filesize);
            wrapper.appendChild(img);
            wrapper.appendChild(info);
            preview.appendChild(wrapper);

            const reader = new FileReader();
            reader.onload = event => {
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endpush
