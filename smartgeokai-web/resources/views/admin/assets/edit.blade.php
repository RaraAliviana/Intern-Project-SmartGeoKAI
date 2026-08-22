@extends('layouts.app')

@section('title', 'Edit Aset')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Aset</h1>
        <p class="text-sm text-gray-500">Perbarui informasi dan foto aset {{ $asset->id_asset }}.</p>
    </div>

    <div class="max-w-4xl rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">

        <form action="{{ route('admin.assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                <!-- ID Aset -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">ID Aset <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="id_asset"
                        value="{{ old('id_asset', $asset->id_asset) }}"
                        class="w-full rounded-lg border @error('id_asset') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        required
                    >
                    @error('id_asset')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Aset -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Jenis Aset <span class="text-red-500">*</span></label>
                    <select name="asset_type" class="w-full rounded-lg border @error('asset_type') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10" required>
                        <option value="Tanah" {{ old('asset_type', $asset->asset_type) == 'Tanah' ? 'selected' : '' }}>Tanah</option>
                        <option value="Bangunan" {{ old('asset_type', $asset->asset_type) == 'Bangunan' ? 'selected' : '' }}>Bangunan</option>
                    </select>
                    @error('asset_type')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Provinsi -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_id" name="province_id" class="w-full rounded-lg border @error('province_id') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10" required>
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->id }}" {{ old('province_id', $asset->province_id) == $province->id ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('province_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kabupaten/Kota -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kabupaten/Kota <span class="text-red-500">*</span></label>
                    <select id="regency_id" name="regency_id" class="w-full rounded-lg border @error('regency_id') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10" required>
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        @foreach ($regencies as $regency)
                            <option value="{{ $regency->id }}" {{ old('regency_id', $asset->regency_id) == $regency->id ? 'selected' : '' }}>
                                {{ $regency->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('regency_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kecamatan -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="district_id" name="district_id" class="w-full rounded-lg border @error('district_id') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10" required>
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->id }}" {{ old('district_id', $asset->district_id) == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('district_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Klasifikasi -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Klasifikasi <span class="text-red-500">*</span></label>
                    <select name="classification" class="w-full rounded-lg border @error('classification') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10" required>
                        <option value="ROW" {{ old('classification', $asset->classification) == 'ROW' ? 'selected' : '' }}>ROW</option>
                        <option value="Fasilitas" {{ old('classification', $asset->classification) == 'Fasilitas' ? 'selected' : '' }}>Fasilitas</option>
                        <option value="Operasional" {{ old('classification', $asset->classification) == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                    </select>
                    @error('classification')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kelompok Aset -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kelompok Aset <span class="text-red-500">*</span></label>
                    <select name="asset_group" class="w-full rounded-lg border @error('asset_group') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10" required>
                        <option value="Produksi" {{ old('asset_group', $asset->asset_group) == 'Produksi' ? 'selected' : '' }}>Produksi</option>
                        <option value="Non Produksi" {{ old('asset_group', $asset->asset_group) == 'Non Produksi' ? 'selected' : '' }}>Non Produksi</option>
                    </select>
                    @error('asset_group')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full rounded-lg border @error('status') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10" required>
                        <option value="Clear" {{ old('status', $asset->status) == 'Clear' ? 'selected' : '' }}>Clear</option>
                        <option value="Proses" {{ old('status', $asset->status) == 'Proses' ? 'selected' : '' }}>Proses</option>
                        <option value="Masalah" {{ old('status', $asset->status) == 'Masalah' ? 'selected' : '' }}>Masalah</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Luas (m2) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Luas (m²) <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        step="0.01"
                        name="area_m2"
                        value="{{ old('area_m2', $asset->area_m2) }}"
                        class="w-full rounded-lg border @error('area_m2') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        required
                    >
                    @error('area_m2')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Latitude -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Latitude <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="latitude"
                        value="{{ old('latitude', $asset->latitude) }}"
                        class="w-full rounded-lg border @error('latitude') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        required
                    >
                    @error('latitude')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Longitude -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Longitude <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="longitude"
                        value="{{ old('longitude', $asset->longitude) }}"
                        class="w-full rounded-lg border @error('longitude') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        required
                    >
                    @error('longitude')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Google Maps URL -->
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Link Google Maps</label>
                    <input
                        type="url"
                        name="gmaps_url"
                        value="{{ old('gmaps_url', $asset->gmaps_url) }}"
                        placeholder="https://maps.google.com/..."
                        class="w-full rounded-lg border @error('gmaps_url') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                    >
                    @error('gmaps_url')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Deskripsi / Keterangan</label>
                    <textarea
                        name="description"
                        rows="3"
                        class="w-full rounded-lg border @error('description') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                    >{{ old('description', $asset->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- FOTO ASET SAAT INI -->
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Foto Aset Saat Ini</label>
                    @if ($asset->images->count() > 0)
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            @foreach ($asset->images as $img)
                                <div class="relative group overflow-hidden rounded-xl border border-gray-200">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="Foto Aset" class="h-28 w-full object-cover">
                                    <label class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white cursor-pointer transition">
                                        <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="mb-1 h-4 w-4 rounded text-red-600 focus:ring-red-500">
                                        <span class="text-xs font-semibold text-red-300">Hapus Foto</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Arahkan kursor lalu centang foto yang ingin dihapus saat disimpan.</p>
                    @else
                        <p class="text-sm text-gray-400 italic">Belum ada foto terpilih.</p>
                    @endif
                </div>

                <!-- TAMBAH FOTO BARU -->
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Tambah Foto Baru</label>
                    <input
                        type="file"
                        name="new_images[]"
                        multiple
                        accept="image/png, image/jpeg, image/jpg, image/webp"
                        class="w-full rounded-lg border @error('new_images.*') border-red-500 @else border-gray-300 @enderror px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                    >
                    <p class="mt-1 text-xs text-gray-500">Bisa memilih lebih dari 1 file (JPG, PNG, WEBP, Maks 5MB).</p>
                    @error('new_images.*')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.assets.show', $asset) }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

    <!-- Script AJAX Dropdown Wilayah -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const provinceSelect = document.getElementById('province_id');
            const regencySelect = document.getElementById('regency_id');
            const districtSelect = document.getElementById('district_id');

            provinceSelect.addEventListener('change', function () {
                const provinceId = this.value;
                regencySelect.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';

                if (provinceId) {
                    fetch(`{{ route('admin.assets.get-regencies') }}?province_id=${provinceId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(regency => {
                                const option = document.createElement('option');
                                option.value = regency.id;
                                option.textContent = regency.name;
                                regencySelect.appendChild(option);
                            });
                        });
                }
            });

            regencySelect.addEventListener('change', function () {
                const regencyId = this.value;
                districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';

                if (regencyId) {
                    fetch(`{{ route('admin.assets.get-districts') }}?regency_id=${regencyId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.id;
                                option.textContent = district.name;
                                districtSelect.appendChild(option);
                            });
                        });
                }
            });
        });
    </script>

@endsection