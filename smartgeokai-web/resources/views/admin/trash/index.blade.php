
@extends('layouts.app')

@section('title', 'Tong Sampah Aset')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-2 flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('admin.assets.index') }}"
                   class="hover:text-brand-500">
                    Daftar Aset
                </a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span>Arsip Terhapus
            </div>

            <h1 class="text-2xl font-bold text-gray-800">
                Arsip Asset us
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kelola aset yang telah dihapus. Pulihkan aset atau hapus permanen.
            </p>
        </div>

        <a href="{{ route('admin.assets.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Daftar Aset
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Ringkasan --}}
    <div class="rounded-2xl border border-purple-100 bg-purple-50 p-5">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-purple-600 shadow-sm">
                <i class="fa-solid fa-trash-can text-xl"></i>
            </div>

            <div>
                <p class="text-sm font-medium text-purple-700">
                    Total Aset Terhapus
                </p>
                <p class="text-2xl font-bold text-purple-900">
                    {{ number_format($totalTrashed) }}
                </p>
            </div>
        </div>

        <p class="mt-3 text-xs leading-5 text-purple-700">
            Halaman ini hanya dapat diakses oleh Super Admin.
            Aset yang dipulihkan akan kembali ke daftar aset aktif.
        </p>
    </div>

    {{-- Pencarian --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs">
        <form method="GET"
              action="{{ route('admin.trash.index') }}"
              class="flex flex-col gap-3 sm:flex-row">

            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari ID aset, tipe, atau deskripsi..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
            </div>

            <button type="submit"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                <i class="fa-solid fa-filter"></i>
                Cari
            </button>

            @if (request('search'))
                <a href="{{ route('admin.trash.index') }}"
                   class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-300 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel aset terhapus --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">

        <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">
                    Daftar Aset Terhapus
                </h2>
                <p class="mt-1 text-xs text-gray-400">
                    Data yang ditampilkan sudah dipindahkan ke tong sampah.
                </p>
            </div>

            @if ($totalTrashed > 0)
                <form
                    method="POST"
                    action="{{ route('admin.trash.restore-all') }}"
                    onsubmit="return confirm('Pulihkan SEMUA aset yang ada di tong sampah?')"
                >
                    @csrf

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs font-medium text-green-700 hover:bg-green-100">
                        <i class="fa-solid fa-rotate-left"></i>
                        Pulihkan Semua
                    </button>
                </form>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="whitespace-nowrap px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            ID Aset
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Tipe
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Klasifikasi
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Wilayah
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Dihapus Pada
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($assets as $asset)
                        <tr class="transition hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="text-sm font-semibold text-gray-700">
                                    {{ $asset->id_asset }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                {{ $asset->asset_type ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                {{ $asset->classification ?? '-' }}
                            </td>

                            <td class="min-w-48 px-5 py-4">
                                <p class="text-sm font-medium text-gray-700">
                                    {{ $asset->district?->name ?? '-' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ $asset->regency?->name ?? '-' }},
                                    {{ $asset->province?->name ?? '-' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="text-sm text-gray-600">
                                    {{ $asset->deleted_at?->format('d/m/Y') ?? '-' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ $asset->deleted_at?->format('H:i') ?? '' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-2">

                                    {{-- Pulihkan --}}
                                    <form
                                        method="POST"
                                        action="{{ route('admin.trash.restore', $asset->id) }}"
                                        onsubmit="return confirm('Pulihkan aset {{ $asset->id_asset }}?')"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            title="Pulihkan aset"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-green-200 bg-green-50 text-green-700 transition hover:bg-green-100"
                                        >
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                    </form>

                                    {{-- Hapus permanen --}}
                                    <form
                                        method="POST"
                                        action="{{ route('admin.trash.force-delete', $asset->id) }}"
                                        onsubmit="return confirm('PERINGATAN: Aset {{ $asset->id_asset }} akan dihapus permanen dan tidak dapat dipulihkan. Lanjutkan?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Hapus permanen"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100"
                                        >
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                    <i class="fa-solid fa-trash-can text-xl"></i>
                                </div>

                                <h3 class="mt-4 text-sm font-semibold text-gray-700">
                                    Arsip Asset Kosong
                                </h3>

                                <p class="mt-1 text-sm text-gray-400">
                                    Tidak ada aset terhapus yang sesuai dengan pencarian.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($assets->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $assets->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
