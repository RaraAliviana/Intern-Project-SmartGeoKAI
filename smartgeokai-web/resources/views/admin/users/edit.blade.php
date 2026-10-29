@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Pengguna</h1>
        <p class="text-sm text-gray-500">Perbarui data pengguna SmartGeoKAI.</p>
    </div>

    <div class="max-w-3xl rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs">
        <form
            method="POST"
            action="{{ route('admin.users.update', $user) }}"
            class="space-y-5"
        >
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Nama Lengkap
                </label>
                <input
                    type="text"
                    name="full_name"
                    value="{{ old('full_name', $user->full_name) }}"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                @error('full_name')
                    <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    value="{{ old('username', $user->username) }}"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                @error('username')
                    <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    NIP
                </label>
                <input
                    type="text"
                    name="nip"
                    value="{{ old('nip', $user->nip) }}"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                @error('nip')
                    <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Password Baru
                </label>
                <input
                    type="password"
                    name="password"
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                @error('password')
                    <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Konfirmasi Password Baru
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    minlength="8"
                    placeholder="Ulangi password baru"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
            </div>

            {{-- Role --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Role
                </label>

                @if ($user->id === auth()->id())
                    @php
                        $selfRoleLabels = [
                            'superadmin' => 'Super Admin',
                            'admin' => 'Admin',
                            'petugas' => 'Petugas',
                        ];
                    @endphp

                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                        <i class="fa-solid fa-user-shield mr-2 text-brand-500"></i>
                        {{ $selfRoleLabels[$user->role] ?? ucfirst($user->role) }}

                        <p class="mt-1 text-xs text-gray-400">
                            Role akun sendiri tidak dapat diubah.
                        </p>
                    </div>

                    <input type="hidden" name="role" value="{{ $user->role }}">
                @elseif (count($availableRoles) === 1)
                    <input type="hidden" name="role" value="petugas">
                    <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5">
                        <i class="fa-solid fa-user text-gray-400"></i>
                        <span class="text-sm text-gray-600">Petugas</span>
                        <span class="ml-auto rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">
                            Hanya role ini yang tersedia
                        </span>
                    </div>
                @else
                    <select
                        name="role"
                        id="role"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                        onchange="toggleProvinceField(this.value)"
                    >
                        @foreach($availableRoles as $value => $label)
                            <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                @endif

                @error('role')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Wilayah Kerja --}}
            <div id="province-field" class="{{ old('role', $user->role) === 'admin' ? 'hidden' : '' }}">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Wilayah Kerja
                </label>

                <select
                    name="province_id"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                    <option value="">-- Semua Wilayah --</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p->id }}" {{ old('province_id', $user->province_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>

                <p class="mt-1 text-xs text-gray-400">
                    Kosongkan jika petugas bisa akses semua wilayah
                </p>

                @error('province_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button
                    type="submit"
                    class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600"
                >
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('admin.users.index') }}"
                    class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
function toggleProvinceField(role) {
    const field = document.getElementById('province-field');
    if (!field) return;
    field.classList.toggle('hidden', role === 'admin');
}

// Jalankan saat halaman load untuk kondisi edit
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
        toggleProvinceField(roleSelect.value);
    }
});
</script>
@endpush