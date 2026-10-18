
<div>
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="mb-5 rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-sm font-medium text-success-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 rounded-xl border border-error-200 bg-error-50 px-4 py-3 text-sm font-medium text-error-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Pencarian dan filter --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 sm:max-w-sm">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama, username, atau NIP..."
                class="h-11 w-full rounded-lg border border-gray-300 bg-white pl-11 pr-9 text-sm text-gray-700 placeholder:text-gray-400 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
            >

            @if ($search)
                <button
                    type="button"
                    wire:click="$set('search', '')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    title="Hapus pencarian"
                >
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            @endif
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative w-full sm:w-52">
                <select
                    wire:model.live="roleFilter"
                    class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 pr-9 text-sm text-gray-700 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                >
                    <option value="">Semua Role</option>
                    <option value="superadmin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                </select>

                <i class="fa-solid fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            </div>

            <a
                href="{{ route('admin.users.create') }}"
                class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Pengguna
            </a>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Nama</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Username</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">NIP</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Role</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Wilayah Kerja</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        @php
                            $roleLabels = [
                                'superadmin' => 'Super Admin',
                                'admin' => 'Admin',
                                'petugas' => 'Petugas',
                            ];

                            $roleClasses = [
                                'superadmin' => 'bg-purple-50 text-purple-700',
                                'admin' => 'bg-brand-50 text-brand-600',
                                'petugas' => 'bg-gray-100 text-gray-600',
                            ];

                            $role = strtolower(trim($user->role ?? ''));
                        @endphp

                        <tr
                            class="hover:bg-gray-50"
                            wire:key="user-{{ $user->id }}"
                        >
                            {{-- Nama --}}
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-600">
                                        {{ strtoupper(substr($user->full_name ?? 'U', 0, 2)) }}
                                    </div>

                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $user->full_name }}
                                    </span>
                                </div>
                            </td>

                            {{-- Username --}}
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                {{ $user->username }}
                            </td>

                            {{-- NIP --}}
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                {{ $user->nip }}
                            </td>

                            {{-- Role --}}
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $roleClasses[$role] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $roleLabels[$role] ?? ucfirst($role ?: 'Tidak diketahui') }}
                                </span>
                            </td>

                            {{-- Wilayah kerja --}}
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                @if (in_array($role, ['superadmin', 'admin'], true))
                                    Semua Wilayah
                                @else
                                    {{ $user->province?->name ?? 'Semua Wilayah' }}
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $user->is_active ? 'bg-success-50 text-success-600' : 'bg-error-50 text-error-600' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a
                                        href="{{ route('admin.users.edit', $user->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50"
                                        title="Edit pengguna"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    @if ($user->id !== auth()->id())
                                        <button
                                            type="button"
                                            wire:click="toggleStatus({{ $user->id }})"
                                            wire:confirm="Yakin ingin mengubah status akun ini?"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-warning-300 hover:text-warning-500"
                                            title="Aktifkan/Nonaktifkan"
                                        >
                                            <i class="fa-solid fa-power-off text-xs"></i>
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="Yakin ingin menghapus akun ini?"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-error-300 hover:text-error-500"
                                            title="Hapus"
                                        >
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">
                                @if ($search || $roleFilter)
                                    Tidak ada pengguna yang cocok dengan pencarian.
                                @else
                                    Belum ada data pengguna.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
