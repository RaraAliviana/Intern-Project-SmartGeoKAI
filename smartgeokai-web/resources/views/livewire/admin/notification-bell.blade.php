
{{--
    wire:poll.15s = memperbarui notifikasi setiap 15 detik tanpa reload halaman.
--}}
<div class="relative" wire:poll.15s x-data="{ open: @entangle('open') }">

    {{-- TOMBOL LONCENG --}}
    <button
        type="button"
        wire:click="toggle"
        aria-label="Notifikasi"
        aria-expanded="{{ $open ? 'true' : 'false' }}"
        class="relative flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-50 hover:text-gray-700"
    >
        <i class="fa-solid fa-bell"></i>

        @if ($unreadCount > 0)
            <span
                class="absolute right-1.5 top-1.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-error-500 px-1 text-[10px] font-bold text-white ring-2 ring-white"
            >
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- DROPDOWN NOTIFIKASI --}}
    <div
        x-show="open"
        x-cloak
        x-transition
        @click.outside="$wire.set('open', false)"
        class="absolute right-0 z-50 mt-2 w-80 rounded-xl border border-gray-200 bg-white p-2 shadow-theme-lg"
    >

        {{-- HEADER DROPDOWN --}}
        <div class="flex items-center justify-between px-2 py-2">
            <p class="text-sm font-semibold text-gray-800">
                Notifikasi
            </p>

            @if ($unreadCount > 0)
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="text-xs font-medium text-brand-500 hover:underline"
                >
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        {{-- DAFTAR NOTIFIKASI --}}
        <div class="max-h-80 overflow-y-auto">

            @forelse ($notifications as $notification)

                @php
                    $notificationUrl = $notification->data['url'] ?? '#';
                    $isUnread = is_null($notification->read_at);
                @endphp

                {{-- TAG PEMBUKA <a> SUDAH DIPERBAIKI --}}
                <a
                    href="{{ $notificationUrl }}"
                    wire:click="markAsRead('{{ $notification->id }}')"
                    wire:navigate
                    class="flex items-start gap-3 rounded-lg px-2 py-2.5 transition hover:bg-gray-50 {{ $isUnread ? '' : 'opacity-60' }}"
                >
                    {{-- INDIKATOR BELUM DIBACA --}}
                    <span
                        class="mt-1 h-2 w-2 shrink-0 rounded-full {{ $isUnread ? 'bg-brand-500' : 'bg-gray-300' }}"
                    ></span>

                    {{-- ISI NOTIFIKASI --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-700">
                            {{ $notification->data['title'] ?? 'Notifikasi' }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ $notification->data['message'] ?? '' }}
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>

            @empty
                <p class="px-2 py-6 text-center text-sm text-gray-400">
                    Belum ada notifikasi.
                </p>
            @endforelse

        </div>
    </div>
</div>
