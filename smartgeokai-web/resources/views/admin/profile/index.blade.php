@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
        <p class="text-sm text-gray-500">Kelola informasi akun dan keamanan kamu.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Ringkasan akun --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs lg:col-span-1">
            <div class="flex flex-col items-center text-center">
                <img
                    src="{{ asset('images/icon/avatar-01.jpg') }}"
                    alt="Avatar"
                    class="h-24 w-24 rounded-full object-cover ring-4 ring-brand-50"
                >
                <h2 class="mt-4 text-base font-bold text-gray-800">{{ $user->full_name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->role === 'admin' ? 'Administrator' : 'Petugas' }}</p>

                <span class="mt-3 inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                    {{ $user->is_active ? 'bg-success-50 text-success-600' : 'bg-error-50 text-error-600' }}">
                    <i class="fa-solid fa-circle text-[6px]"></i>
                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>

                <hr class="my-5 w-full border-gray-100">

                <dl class="w-full space-y-3 text-left">
                    <div class="flex items-center justify-between">
                        <dt class="text-xs text-gray-400">Username</dt>
                        <dd class="text-sm font-medium text-gray-700">{{ $user->username }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-xs text-gray-400">NIP</dt>
                        <dd class="text-sm font-medium text-gray-700">{{ $user->nip }}</dd>
                    </div>
                    @if ($user->province)
                        <div class="flex items-center justify-between">
                            <dt class="text-xs text-gray-400">Wilayah Kerja</dt>
                            <dd class="text-sm font-medium text-gray-700">{{ $user->province->name }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6 lg:col-span-2">

            {{-- Edit data diri --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">
                <div class="mb-5 border-b border-gray-100 pb-4">
                    <h2 class="text-base font-semibold text-gray-800">Informasi Akun</h2>
                    <p class="mt-1 text-sm text-gray-500">Perbarui nama lengkap dan NIP kamu.</p>
                </div>

                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input
                            type="text"
                            name="full_name"
                            value="{{ old('full_name', $user->full_name) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        >
                        @error('full_name')
                            <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Username</label>
                        <input
                            type="text"
                            value="{{ $user->username }}"
                            disabled
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500"
                        >
                        <p class="mt-1 text-xs text-gray-400">Username tidak dapat diubah sendiri. Hubungi administrator bila perlu.</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">NIP</label>
                        <input
                            type="text"
                            name="nip"
                            value="{{ old('nip', $user->nip) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        >
                        @error('nip')
                            <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            {{-- Ganti password --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">
                <div class="mb-5 border-b border-gray-100 pb-4">
                    <h2 class="text-base font-semibold text-gray-800">Ganti Password</h2>
                    <p class="mt-1 text-sm text-gray-500">Masukkan password lama untuk konfirmasi sebelum mengganti password baru.</p>
                </div>

                <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Password Lama</label>
                        <input
                            type="password"
                            name="current_password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        >
                        @error('current_password')
                            <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Password Baru</label>
                        <input
                            type="password"
                            name="password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        >
                        <p class="mt-1 text-xs text-gray-400">Minimal 8 karakter.</p>
                        @error('password')
                            <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        >
                    </div>

                    <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                        Ganti Password
                    </button>
                </form>
            </div>

        </div>

    </div>

@endsection