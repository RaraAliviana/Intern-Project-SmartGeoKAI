@extends('layouts.app')

@section('title', 'Tambah Aset')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Tambah Aset
    </h1>

    <p class="text-sm text-gray-500">
        Lengkapi informasi aset dan unggah foto aset.
    </p>
</div>


@if ($errors->any())

    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">

        <div class="flex gap-3">

            <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>

            <div>

                <p class="font-semibold text-red-700">
                    Terdapat kesalahan pada form.
                </p>

                <ul class="mt-2 list-inside list-disc text-sm text-red-600">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        </div>

    </div>

@endif


<form
    action="{{ route('admin.assets.store') }}"
    method="POST"
    enctype="multipart/form-data"
>

@csrf


{{-- ========================================================= --}}
{{-- IDENTITAS ASET --}}
{{-- ========================================================= --}}

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

        {{-- ID ASET --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                ID Aset
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="id_asset"
                value="{{ old('id_asset') }}"
                placeholder="Contoh: 06.01.00001"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

        </div>


        {{-- TIPE --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Tipe Aset
                <span class="text-red-500">*</span>
            </label>

            <select
                name="asset_type"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

                <option value="">
                    -- Pilih Tipe --
                </option>

                <option
                    value="Tanah"
                    {{ old('asset_type') === 'Tanah' ? 'selected' : '' }}
                >
                    Tanah
                </option>

                <option
                    value="Bangunan"
                    {{ old('asset_type') === 'Bangunan' ? 'selected' : '' }}
                >
                    Bangunan
                </option>

            </select>

        </div>


        {{-- KLASIFIKASI --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Klasifikasi
                <span class="text-red-500">*</span>
            </label>

            <select
                name="classification"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

                <option value="">
                    -- Pilih Klasifikasi --
                </option>

                <option
                    value="ROW"
                    {{ old('classification') === 'ROW' ? 'selected' : '' }}
                >
                    ROW
                </option>

                <option
                    value="Fasilitas"
                    {{ old('classification') === 'Fasilitas' ? 'selected' : '' }}
                >
                    Fasilitas
                </option>

                <option
                    value="Operasional"
                    {{ old('classification') === 'Operasional' ? 'selected' : '' }}
                >
                    Operasional
                </option>

            </select>

        </div>


        {{-- KELOMPOK --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Kelompok Aset
                <span class="text-red-500">*</span>
            </label>

            <select
                name="asset_group"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

                <option value="">
                    -- Pilih Kelompok --
                </option>

                <option
                    value="Produksi"
                    {{ old('asset_group') === 'Produksi' ? 'selected' : '' }}
                >
                    Produksi
                </option>

                <option
                    value="Non Produksi"
                    {{ old('asset_group') === 'Non Produksi' ? 'selected' : '' }}
                >
                    Non Produksi
                </option>

            </select>

        </div>

    </div>

</div>



{{-- ========================================================= --}}
{{-- WILAYAH & KOORDINAT --}}
{{-- ========================================================= --}}

<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">

    <div class="mb-5 border-b border-gray-100 pb-4">

        <h2 class="text-base font-semibold text-gray-800">
            Lokasi & Wilayah
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Tentukan wilayah dan koordinat aset melalui form atau klik langsung pada peta.
        </p>

    </div>


    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

        {{-- PROVINSI --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Provinsi
                <span class="text-red-500">*</span>
            </label>

            <select
                name="province_id"
                id="province_id"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

                <option value="">
                    -- Pilih Provinsi --
                </option>

                @foreach($provinces as $province)

                    <option
                        value="{{ $province->id }}"
                        {{ old('province_id') == $province->id ? 'selected' : '' }}
                    >
                        {{ $province->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- KABUPATEN --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Kabupaten
                <span class="text-red-500">*</span>
            </label>

            <select
                name="regency_id"
                id="regency_id"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

                <option value="">
                    -- Pilih Kabupaten --
                </option>

                @foreach($regencies as $regency)

                    <option
                        value="{{ $regency->id }}"
                        {{ old('regency_id') == $regency->id ? 'selected' : '' }}
                    >
                        {{ $regency->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- KECAMATAN --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Kecamatan
                <span class="text-red-500">*</span>
            </label>

            <select
                name="district_id"
                id="district_id"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

                <option value="">
                    -- Pilih Kecamatan --
                </option>

                @foreach($districts as $district)

                    <option
                        value="{{ $district->id }}"
                        {{ old('district_id') == $district->id ? 'selected' : '' }}
                    >
                        {{ $district->name }}
                    </option>

                @endforeach

            </select>

        </div>

    </div>


    <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">

        {{-- LUAS --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Luas Aset (m²)
                <span class="text-red-500">*</span>
            </label>

            <input
                type="number"
                name="area_m2"
                value="{{ old('area_m2') }}"
                step="0.01"
                min="0"
                required
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

        </div>


        {{-- LATITUDE --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Latitude
                <span class="text-red-500">*</span>
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
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

        </div>


        {{-- LONGITUDE --}}
        <div>

            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Longitude
                <span class="text-red-500">*</span>
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
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

        </div>

    </div>


    {{-- PETA PEMILIHAN KOORDINAT --}}
    <div class="mt-5">
        <label class="mb-1.5 block text-sm font-medium text-gray-700">
            Pilih Titik Lokasi Peta
        </label>
        <div class="relative z-0 h-[350px] w-full overflow-hidden rounded-xl border border-gray-200">
            <div id="pickerMap" class="h-full w-full"></div>
        </div>
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
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
        >

    </div>

</div>



{{-- ========================================================= --}}
{{-- FOTO ASET --}}
{{-- ========================================================= --}}

<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">

    <div class="mb-5 border-b border-gray-100 pb-4">

        <h2 class="text-base font-semibold text-gray-800">
            Foto Aset
            <span class="text-red-500">*</span>
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Unggah minimal satu foto aset. Kamu dapat memilih beberapa foto sekaligus.
        </p>

    </div>


    <label
        for="images"
        class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center transition hover:border-brand-400 hover:bg-brand-50"
    >

        <i class="fa-solid fa-images text-4xl text-gray-400"></i>

        <p class="mt-3 text-sm font-medium text-gray-700">
            Pilih foto aset
        </p>

        <p class="mt-1 text-xs text-gray-500">
            JPG, JPEG, PNG, WEBP · Maksimal 5 MB per foto
        </p>

        <span class="mt-4 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white">
            Pilih Foto
        </span>

        <input
            id="images"
            name="images[]"
            type="file"
            multiple
            accept="image/jpeg,image/png,image/webp"
            required
            class="hidden"
        >

    </label>


    {{-- Preview --}}
    <div
        id="image-preview"
        class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4"
    ></div>

</div>



{{-- ========================================================= --}}
{{-- STATUS & DESKRIPSI --}}
{{-- ========================================================= --}}

<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">

    <div class="mb-5 border-b border-gray-100 pb-4">

        <h2 class="text-base font-semibold text-gray-800">
            Kondisi Aset
        </h2>

    </div>


    <div class="mb-5">

        <label class="mb-1.5 block text-sm font-medium text-gray-700">
            Status
            <span class="text-red-500">*</span>
        </label>

        <select
            name="status"
            required
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
        >

            <option value="">
                -- Pilih Status --
            </option>

            <option
                value="Clear"
                {{ old('status') === 'Clear' ? 'selected' : '' }}
            >
                Clear
            </option>

            <option
                value="Proses"
                {{ old('status') === 'Proses' ? 'selected' : '' }}
            >
                Proses
            </option>

            <option
                value="Masalah"
                {{ old('status') === 'Masalah' ? 'selected' : '' }}
            >
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
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
        >{{ old('description') }}</textarea>

    </div>

</div>



{{-- ========================================================= --}}
{{-- BUTTON --}}
{{-- ========================================================= --}}

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
@endpush


{{-- ================================================================
     LEAFLET JAVASCRIPT & LOGIK
================================================================ --}}
@push('scripts')
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin="">
</script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | DROPDOWN WILAYAH (AJAX)
    |--------------------------------------------------------------------------
    */

    const provinceSelect = document.getElementById('province_id');
    const regencySelect = document.getElementById('regency_id');
    const districtSelect = document.getElementById('district_id');

    const regenciesUrl = '{{ route("admin.assets.get-regencies") }}';
    const districtsUrl = '{{ route("admin.assets.get-districts") }}';

    if (provinceSelect) {
        provinceSelect.addEventListener('change', function () {
            const provinceId = this.value;

            regencySelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
            districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';

            if (!provinceId) return;

            fetch(`${regenciesUrl}?province_id=${provinceId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mengambil data kabupaten.');
                    return response.json();
                })
                .then(data => {
                    data.forEach(regency => {
                        const option = document.createElement('option');
                        option.value = regency.id;
                        option.textContent = regency.name;
                        regencySelect.appendChild(option);
                    });
                })
                .catch(error => console.error(error));
        });
    }

    if (regencySelect) {
        regencySelect.addEventListener('change', function () {
            const regencyId = this.value;

            districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';

            if (!regencyId) return;

            fetch(`${districtsUrl}?regency_id=${regencyId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mengambil data kecamatan.');
                    return response.json();
                })
                .then(data => {
                    data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                })
                .catch(error => console.error(error));
        });
    }


    /*
    |--------------------------------------------------------------------------
    | LEAFLET MAP PICKER KOORDINAT
    |--------------------------------------------------------------------------
    */

    const mapContainer = document.getElementById('pickerMap');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    const initialLat = parseFloat(latInput.value) || -7.5;
    const initialLng = parseFloat(lngInput.value) || 110.2;

    const jatengDIYBounds = L.latLngBounds(
        L.latLng(-8.80, 108.50), // Barat Daya
        L.latLng(-6.30, 111.60)  // Timur Laut
    );

    const pickerMap = L.map('pickerMap', {
        preferCanvas: true,
        maxBounds: jatengDIYBounds,
        maxBoundsViscosity: 1.0,
        minZoom: 8,
        maxZoom: 18
    }).setView([initialLat, initialLng], 9);

    // Esri World Imagery (Citra Satelit)
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        bounds: jatengDIYBounds,
        maxZoom: 18,
        attribution: 'Tiles &copy; Esri'
    }).addTo(pickerMap);

    // World Boundaries & Places (Label Wilayah/Jalan)
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
        bounds: jatengDIYBounds,
        maxZoom: 18,
        attribution: ''
    }).addTo(pickerMap);

    // Handling Auto Resize secara dinamik jika container berubah
    if (mapContainer) {
        const resizeObserver = new ResizeObserver(() => {
            pickerMap.invalidateSize();
        });
        resizeObserver.observe(mapContainer);
    }

    let marker;

    if (latInput.value && lngInput.value) {
        marker = L.marker([initialLat, initialLng]).addTo(pickerMap);
    }

    function updateMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(pickerMap);
        }
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
    }

    pickerMap.on('click', function (e) {
        updateMarker(e.latlng.lat, e.latlng.lng);
    });

    [latInput, lngInput].forEach(input => {
        input.addEventListener('change', function () {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                updateMarker(lat, lng);
                pickerMap.panTo([lat, lng]);
            }
        });
    });


    /*
    |--------------------------------------------------------------------------
    | PREVIEW FOTO
    |--------------------------------------------------------------------------
    */

    const imageInput = document.getElementById('images');
    const preview = document.getElementById('image-preview');

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            preview.innerHTML = '';
            const files = Array.from(this.files);

            files.forEach((file) => {
                const reader = new FileReader();

                reader.onload = function (event) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'overflow-hidden rounded-xl border border-gray-200 bg-white';

                    wrapper.innerHTML = `
                        <img src="${event.target.result}" class="h-32 w-full object-cover">
                        <div class="p-2">
                            <p class="truncate text-xs font-medium text-gray-700">${file.name}</p>
                            <p class="text-xs text-gray-400">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    `;

                    preview.appendChild(wrapper);
                };

                reader.readAsDataURL(file);
            });
        });
    }

});

</script>
@endpush