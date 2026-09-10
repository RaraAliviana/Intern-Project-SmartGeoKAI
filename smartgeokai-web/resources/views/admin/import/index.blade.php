@extends('layouts.app')

@section('title', 'Import Data Aset')

@section('breadcrumb')
    <li class="breadcrumb-item active">Import Data Aset</li>
@endsection

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Import Aset Massal
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Unggah file CSV untuk menambahkan banyak data aset sekaligus ke dalam sistem.
            </p>
        </div>

        <div>
            <a href="{{ route('admin.import.template') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                <i class="fa-solid fa-download text-brand-500"></i>
                <span>Unduh Template CSV</span>
            </a>
        </div>
    </div>

    {{-- ALERT PESAN SYSTEM --}}
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- HASIL / RINGKASAN IMPORT --}}
    @if(session('import_summary'))
        @php $summary = session('import_summary'); @endphp
        <div class="space-y-6">

            {{-- STATISTIK IMPORT --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-green-100 bg-green-50/50 p-5 shadow-theme-xs">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-500 text-white">
                            <i class="fa-solid fa-check text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-green-600 uppercase">BERHASIL DIIMPOR</p>
                            <p class="text-2xl font-bold text-green-800">{{ $summary['success_count'] }} Data</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-red-100 bg-red-50/50 p-5 shadow-theme-xs">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-500 text-white">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-red-600 uppercase">GAGAL DIIMPOR</p>
                            <p class="text-2xl font-bold text-red-800">{{ $summary['failure_count'] }} Data</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABEL DAFTAR KEGAGALAN --}}
            @if(!empty($summary['failures']))
                <div class="overflow-hidden rounded-2xl border border-red-200 bg-white shadow-theme-xs">
                    <div class="bg-red-50 px-5 py-3.5 border-b border-red-100">
                        <h3 class="text-sm font-semibold text-red-800">
                            <i class="fa-solid fa-list-check mr-1.5"></i> Rincian Baris Data Yang Gagal Diimpor
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-5 py-3 font-medium">Baris Ke</th>
                                    <th class="px-5 py-3 font-medium">ID Asset</th>
                                    <th class="px-5 py-3 font-medium">Alasan Kegagalan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($summary['failures'] as $fail)
                                    <tr class="hover:bg-red-50/30">
                                        <td class="px-5 py-3 font-semibold text-gray-800">Baris {{ $fail['row'] }}</td>
                                        <td class="px-5 py-3 font-mono text-gray-700">{{ $fail['id_asset'] }}</td>
                                        <td class="px-5 py-3 text-red-600 font-medium">{{ $fail['reasons'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- TABEL SEKILAS DATA YANG BERHASIL DIIMPOR --}}
            @if(!empty($summary['imported_assets']))
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-theme-xs">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">
                                <i class="fa-solid fa-circle-check text-green-500 mr-1.5"></i> Sekilas Data Yang Berhasil Diimpor
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Menampilkan {{ count($summary['imported_assets']) }} data aset yang baru saja masuk ke database
                            </p>
                        </div>
                        <a href="{{ route('admin.assets.index') }}" class="text-xs font-semibold text-brand-500 hover:text-brand-600">
                            Lihat Semua Aset &rarr;
                        </a>
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
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($summary['imported_assets'] as $asset)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-5 py-3.5 font-semibold text-gray-800 font-mono">
                                            {{ $asset['id_asset'] }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if($asset['status'] === 'Clear')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                    Clear
                                                </span>
                                            @elseif($asset['status'] === 'Proses')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-50 px-2.5 py-0.5 text-xs font-semibold text-yellow-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                                    Proses
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                                    {{ $asset['status'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5">{{ $asset['province_name'] }}</td>
                                        <td class="px-5 py-3.5">{{ $asset['regency_name'] }}</td>
                                        <td class="px-5 py-3.5">{{ $asset['district_name'] }}</td>
                                        <td class="px-5 py-3.5">{{ $asset['asset_type'] }}</td>
                                        <td class="px-5 py-3.5 font-medium">{{ number_format($asset['area_m2'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    @endif

    {{-- CARD FORM UPLOAD --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-theme-xs">

        <h2 class="text-base font-semibold text-gray-800 mb-1">
            Unggah Berkas CSV
        </h2>
        <p class="text-xs text-gray-500 mb-6">
            Pastikan format kolom pada CSV kamu sudah sesuai dengan template yang disediakan.
        </p>

        <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Pilih File CSV</label>
                <input type="file"
                       name="csv_file"
                       accept=".csv"
                       class="w-full rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:bg-white @error('csv_file') border-red-500 @enderror">
                @error('csv_file')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2 flex items-center gap-3">
                <button type="submit"
                        class="inline-flex h-11 items-center gap-2 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    <i class="fa-solid fa-file-import"></i>
                    Mulai Proses Import
                </button>
            </div>
        </form>

    </div>

</div>

@endsection