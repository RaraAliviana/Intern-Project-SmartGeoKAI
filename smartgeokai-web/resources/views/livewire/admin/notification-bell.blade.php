{{--
    wire:poll.15s = cek notifikasi baru setiap 15 detik tanpa reload halaman.
    Ini pengganti sederhana dari Reverb — cukup untuk requirement FR-09.
--}}
<div class="relative" wire:poll.15s x-data="{ open: @entangle('open') }">
    <button
        type="button"
        wire:click="toggle"
        @click.outside="open = false"
        aria-label="Notifikasi"
        class="relative flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-50 hover:text-gray-700"
    >
        <i class="fa-solid fa-bell"></i>
        @if ($unreadCount > 0)
            <span class="absolute right-1.5 top-1.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-error-500 px-1 text-[10px] font-bold text-white ring-2 ring-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open" x-cloak
        x-transition
        class="absolute right-0 z-50 mt-2 w-80 rounded-xl border border-gray-200 bg-white p-2 shadow-theme-lg"
    >
        <div class="flex items-center justify-between px-2 py-2">
            <p class="text-sm font-semibold text-gray-800">Notifikasi</p>
            @if ($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs font-medium text-brand-500 hover:underline">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse ($notifications as $notification)
                
                    href="{{ $notification->data['url'] ?? '#' }}"
                    wire:click="markAsRead('{{ $notification->id }}')"
                    class="flex items-start gap-3 rounded-lg px-2 py-2.5 transition hover:bg-gray-50 {{ is_null($notification->read_at) ? '' : 'opacity-60' }}"
                >
                    <span class="mt-1 h-2 w-2 shrink-0 rounded-full {{ is_null($notification->read_at) ? 'bg-brand-500' : 'bg-gray-300' }}"></span>
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                        <p class="text-xs text-gray-500">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </a>
            @empty
                <p class="px-2 py-6 text-center text-sm text-gray-400">Belum ada notifikasi.</p>
            @endforelse
        </div>
    </div>
</div>