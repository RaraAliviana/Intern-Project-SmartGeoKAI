
@extends('layouts.app')

@section('title', 'Detail Log Audit')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Detail Log Audit
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Informasi lengkap aktivitas perubahan data aset.
            </p>
        </div>

        <a
            href="{{ route('admin.log-audit.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">
        <h2 class="mb-5 text-base font-semibold text-gray-800">
            Informasi Aktivitas
        </h2>

        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">

            <div>
                <dt class="text-xs font-medium text-gray-500">Waktu</dt>
                <dd class="mt-1 text-sm text-gray-800">
                    {{ $log->created_at?->format('d/m/Y H:i:s') ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-gray-500">Pengguna</dt>
                <dd class="mt-1 text-sm text-gray-800">
                    {{ $log->user->full_name ?? 'Sistem' }}
                    ({{ $log->user->role ?? '-' }})
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-gray-500">ID Aset</dt>
                <dd class="mt-1 text-sm font-mono text-gray-800">
                    {{ $log->asset->id_asset ?? 'Aset tidak tersedia' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-gray-500">Aksi</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-800">
                    {{ ucfirst($log->action) }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-gray-500">Kolom Diubah</dt>
                <dd class="mt-1 text-sm text-gray-800">
                    {{ $log->field_name ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-gray-500">Alamat IP</dt>
                <dd class="mt-1 text-sm text-gray-800">
                    {{ $log->ip_address ?? '-' }}
                </dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="text-xs font-medium text-gray-500">Nilai Lama</dt>
                <dd class="mt-1 whitespace-pre-wrap break-words rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $log->old_value ?? '-' }}</dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="text-xs font-medium text-gray-500">Nilai Baru</dt>
                <dd class="mt-1 whitespace-pre-wrap break-words rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ $log->new_value ?? '-' }}</dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="text-xs font-medium text-gray-500">User Agent</dt>
                <dd class="mt-1 break-all text-xs text-gray-600">
                    {{ $log->user_agent ?? '-' }}
                </dd>
            </div>

        </dl>
    </div>
</div>
@endsection
