@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Pengguna</h1>
        <p class="text-sm text-gray-500">Perbarui data pengguna.</p>
    </div>

    <div class="max-w-3xl rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">

        <form
            method="POST"
            action="{{ route('admin.users.update', $user) }}"
            class="space-y-5"
            x-data="{ role: '{{ old('role', $user->role) }}' }"
        >
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
                @error('full_name') <p class="mt-1 text-xs text-error-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Username</label>
                <input
                    type="text"
                    name="username"
                    value="{{ old('username', $user->username) }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                @error('username') <p class="mt-1 text-xs text-error-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">NIP</label>
                <input
                    type="text"
                    name="nip"
                    value="{{ old('nip', $user->nip) }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                @error('nip') <p class="mt-1 text-xs text-error-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Password Baru</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                @error('password') <p class="mt-1 text-xs text-error-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Role</label>

                @if ($user->id === auth()->id())
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500">
                        <i class="fa-solid fa-user-shield mr-2 text-brand-500"></i> Admin
                        <p class="mt-1 text-xs text-gray-400">Role akun sendiri tidak dapat diubah.</p>
                    </div>
                    <input type="hidden" name="role" value="admin">
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex cursor-pointer items-center gap-2 rounded-lg border px-4 py-3 text-sm font-medium transition"
                            :class="role === 'admin' ? 'border-brand-500 bg-brand-50 text-brand-600' : 'border-gray-300 text-gray-600 hover:bg-gray-50'"
                        >
                            <input type="radio" name="role" value="admin" x-model="role" class="hidden">
                            <i class="fa-solid fa-user-shield"></i> Admin
                        </label>
                        <label
                            class="flex cursor-pointer items-center gap-2 rounded-lg border px-4 py-3 text-sm font-medium transition"
                            :class="role === 'petugas' ? 'border-brand-500 bg-brand-50 text-brand-600' : 'border-gray-300 text-gray-600 hover:bg-gray-50'"
                        >
                            <input type="radio" name="role" value="petugas" x-model="role" class="hidden">
                            <i class="fa-solid fa-user"></i> Petugas
                        </label>
                    </div>
                @endif
                @error('role') <p class="mt-1 text-xs text-error-600">{{ $message }}</p> @enderror
            </div>

            <div x-show="role === 'petugas'" x-cloak x-transition>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Wilayah Kerja (Provinsi)</label>
                <select
                    name="province_id"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                    <option value="">Semua Wilayah (tidak dibatasi)</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->id }}" @selected(old('province_id', $user->province_id) == $province->id)>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
                @error('province_id') <p class="mt-1 text-xs text-error-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                    Batal
                </a>
            </div>

        </form>

    </div>

@endsection