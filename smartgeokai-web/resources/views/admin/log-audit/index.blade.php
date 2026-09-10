@extends('layouts.app')

@section('title', 'Log Audit & Aktivitas')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Log Audit & Aktivitas Petugas
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Rekam jejak seluruh riwayat perubahan data aset dan aktivitas petugas terdaftar.
            </p>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                <i class="fa-solid fa-lock text-gray-400"></i> Read-Only (Hanya Baca)
            </span>
        </div>
    </div>

    {{-- TAB CONTROL --}}
    <div class="border-b border-gray-200" x-data="{ activeTab: 'audit' }">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <button @click="activeTab = 'audit'"
                :class="activeTab === 'audit' ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                class="flex items-center gap-2 border-b-2 py-3 px-1 text-sm font-medium transition">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat Log Audit</span>
                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-600">
                    {{ $logs->total() }}
                </span>
            </button>

            <button @click="activeTab = 'officers'"
                :class="activeTab === 'officers' ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                class="flex items-center gap-2 border-b-2 py-3 px-1 text-sm font-medium transition">
                <i class="fa-solid fa-user-check"></i>
                <span>Aktivitas Petugas Aktif</span>
                <span class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-600">
                    {{ $officers->count() }}
                </span>
            </button>
        </nav>

        {{-- CONTENT TAB 1: RIWAYAT LOG AUDIT --}}
        <div x-show="activeTab === 'audit'" class="mt-6 space-y-6">

            {{-- FILTER LOG AUDIT --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs">
                <form method="GET" action="{{ route('admin.log-audit.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700">Pengguna</label>
                        <select name="user_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brand-500">
                            <option value="">Semua Pengguna</option>
                            @foreach($usersList as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->full_name }} ({{ ucfirst($u->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700">Aksi</label>
                        <select name="action" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brand-500">
                            <option value="">Semua Aksi</option>
                            <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Tambah (Create)</option>
                            <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Ubah (Update)</option>
                            <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Hapus (Delete)</option>
                            <option value="import" {{ request('action') === 'import' ? 'selected' : '' }}>Import Massal</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700">Pencarian</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID Asset, user, field..."
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brand-500">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex h-9 flex-1 items-center justify-center gap-1.5 rounded-lg bg-brand-500 text-xs font-medium text-white transition hover:bg-brand-600">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.log-audit.index') }}" class="inline-flex h-9 items-center justify-center rounded-lg border border-gray-300 bg-white px-3 text-xs font-medium text-gray-600 transition hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- TABEL LOG AUDIT --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-5 py-3.5 font-semibold">Waktu</th>
                                <th class="px-5 py-3.5 font-semibold">Pengguna</th>
                                <th class="px-5 py-3.5 font-semibold">ID Asset</th>
                                <th class="px-5 py-3.5 font-semibold">Aksi</th>
                                <th class="px-5 py-3.5 font-semibold">Kolom Diubah</th>
                                <th class="px-5 py-3.5 font-semibold">Nilai Lama</th>
                                <th class="px-5 py-3.5 font-semibold">Nilai Baru</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="whitespace-nowrap px-5 py-3.5 text-xs text-gray-500">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-gray-800">{{ $log->user->full_name ?? 'Sistem' }}</p>
                                        <p class="text-xs text-gray-400 capitalize">{{ $log->user->role ?? '-' }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 font-mono font-semibold text-gray-800">
                                        {{ $log->asset->id_asset ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($log->action === 'create')
                                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">Create</span>
                                        @elseif($log->action === 'update')
                                            <span class="inline-flex items-center rounded-full bg-yellow-50 px-2 py-0.5 text-xs font-medium text-yellow-700">Update</span>
                                        @elseif($log->action === 'delete')
                                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">Delete</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">Import</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-xs font-mono text-gray-700">
                                        {{ $log->field_name ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-red-600 bg-red-50/30 max-w-[150px] truncate">
                                        {{ $log->old_value ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-green-600 bg-green-50/30 max-w-[150px] truncate">
                                        {{ $log->new_value ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-400">
                                        Belum ada data log audit tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="border-t border-gray-100 px-5 py-4">
                        {{ $logs->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- CONTENT TAB 2: AKTIVITAS PETUGAS AKTIF --}}
        <div x-show="activeTab === 'officers'" class="mt-6">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-800">Daftar Petugas Lapangan & Aktivitas Terakhir</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-5 py-3.5 font-semibold">Nama Petugas</th>
                                <th class="px-5 py-3.5 font-semibold">Email</th>
                                <th class="px-5 py-3.5 font-semibold">Aset Terakhir Ditangani</th>
                                <th class="px-5 py-3.5 font-semibold">Aktivitas Terakhir</th>
                                <th class="px-5 py-3.5 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($officers as $officer)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-4 font-semibold text-gray-800">
                                        {{ $officer->full_name }}
                                    </td>
                                    <td class="px-5 py-4 text-xs text-gray-500">
                                        {{ $officer->email }}
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($officer->last_asset)
                                            <a href="{{ route('admin.assets.show', $officer->last_asset) }}" class="font-mono text-xs font-bold text-brand-500 hover:underline">
                                                {{ $officer->last_asset->id_asset }}
                                            </a>
                                            <span class="text-xs text-gray-400 block">{{ $officer->last_asset->asset_type }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">Belum ada aset</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-xs text-gray-500">
                                        {{ $officer->last_activity_at ? \Carbon\Carbon::parse($officer->last_activity_at)->diffForHumans() : '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Aktif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-gray-400">
                                        Tidak ada petugas aktif.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection