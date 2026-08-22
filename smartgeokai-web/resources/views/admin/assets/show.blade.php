@extends('layouts.app')

@section('title', 'Detail Aset')

@section('content')

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

    <div>

        <h1 class="text-2xl font-bold text-gray-800">
            Detail Aset
        </h1>

        <p class="text-sm text-gray-500">
            Informasi lengkap aset {{ $asset->id_asset }}
        </p>

    </div>


    <div class="flex gap-2">

        <a
            href="{{ route('admin.assets.edit', $asset) }}"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
        >
            <i class="fa-solid fa-pen"></i>
            Edit
        </a>

        <a
            href="{{ route('admin.assets.index') }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>

    </div>

</div>


{{-- ========================================================= --}}
{{-- FOTO ASET --}}
{{-- ========================================================= --}}

<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">

    <div class="mb-5">

        <h2 class="text-base font-semibold text-gray-800">
            Foto Aset
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Dokumentasi foto aset.
        </p>

    </div>


    @if($asset->images->count())

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">

            @foreach($asset->images as $image)

                <a
                    href="{{ asset('storage/' . $image->file_path) }}"
                    target="_blank"
                    class="group overflow-hidden rounded-xl border border-gray-200 bg-gray-50"
                >

                    <img
                        src="{{ asset('storage/' . $image->file_path) }}"
                        alt="{{ $image->file_name ?? 'Foto ' . $asset->id_asset }}"
                        class="h-40 w-full object-cover transition duration-300 group-hover:scale-105"
                    >

                    <div class="p-2">

                        <p class="truncate text-xs font-medium text-gray-600" title="{{ $image->file_name ?? 'Foto ' . $loop->iteration }}">
                            {{ $image->file_name ?? 'Foto ' . $loop->iteration }}
                        </p>

                    </div>

                </a>

            @endforeach

        </div>

    @else

        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">

            <i class="fa-solid fa-image text-4xl text-gray-300"></i>

            <p class="mt-3 text-sm text-gray-500">
                Belum ada foto untuk aset ini.
            </p>

        </div>

    @endif

</div>


{{-- ========================================================= --}}
{{-- INFORMASI ASET --}}
{{-- ========================================================= --}}

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">


    {{-- DATA --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">

        <h2 class="mb-5 border-b border-gray-100 pb-4 text-base font-semibold text-gray-800">
            Informasi Aset
        </h2>


        <div class="space-y-4">

            <div>
                <p class="text-xs text-gray-400">
                    ID Aset
                </p>

                <p class="mt-1 font-medium text-gray-800">
                    {{ $asset->id_asset }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-400">
                    Tipe Aset
                </p>

                <p class="mt-1 text-sm text-gray-700">
                    {{ $asset->asset_type }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-400">
                    Klasifikasi
                </p>

                <p class="mt-1 text-sm text-gray-700">
                    {{ $asset->classification }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-400">
                    Kelompok
                </p>

                <p class="mt-1 text-sm text-gray-700">
                    {{ $asset->asset_group }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-400">
                    Status
                </p>

                <div class="mt-1">

                    @if($asset->status === 'Clear')

                        <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600">
                            Clear
                        </span>

                    @elseif($asset->status === 'Proses')

                        <span class="rounded-full bg-warning-50 px-3 py-1 text-xs font-medium text-warning-600">
                            Proses
                        </span>

                    @else

                        <span class="rounded-full bg-error-50 px-3 py-1 text-xs font-medium text-error-600">
                            Masalah
                        </span>

                    @endif

                </div>

            </div>


            <div>
                <p class="text-xs text-gray-400">
                    Luas
                </p>

                <p class="mt-1 text-sm text-gray-700">
                    {{ number_format($asset->area_m2, 2, ',', '.') }} m²
                </p>
            </div>

        </div>

    </div>


    {{-- LOKASI --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">

        <h2 class="mb-5 border-b border-gray-100 pb-4 text-base font-semibold text-gray-800">
            Lokasi
        </h2>


        <div class="space-y-4">

            <div>
                <p class="text-xs text-gray-400">
                    Provinsi
                </p>

                <p class="mt-1 text-sm text-gray-700">
                    {{ $asset->province->name ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-400">
                    Kabupaten
                </p>

                <p class="mt-1 text-sm text-gray-700">
                    {{ $asset->regency->name ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-400">
                    Kecamatan
                </p>

                <p class="mt-1 text-sm text-gray-700">
                    {{ $asset->district->name ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-400">
                    Koordinat
                </p>

                <p class="mt-1 text-sm text-gray-700">
                    {{ $asset->latitude }},
                    {{ $asset->longitude }}
                </p>
            </div>


            @if($asset->gmaps_url)

                <a
                    href="{{ $asset->gmaps_url }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 text-sm font-medium text-brand-500 hover:underline"
                >
                    <i class="fa-solid fa-location-dot"></i>
                    Buka Google Maps
                </a>

            @endif

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- DESKRIPSI --}}
{{-- ========================================================= --}}

<div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">

    <h2 class="mb-4 border-b border-gray-100 pb-4 text-base font-semibold text-gray-800">
        Deskripsi
    </h2>

    @if($asset->description)

        <p class="whitespace-pre-line text-sm leading-6 text-gray-600">
            {{ $asset->description }}
        </p>

    @else

        <p class="text-sm italic text-gray-400">
            Belum ada deskripsi.
        </p>

    @endif

</div>

@endsection