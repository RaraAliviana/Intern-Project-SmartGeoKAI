<div wire:poll.15s>

    {{-- Filter tabs --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex gap-2">
            <button
                type="button"
                wire:click="$set('filter', '')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ $filter === '' ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50' }}"
            >
                Semua
            </button>
            <button
                type="button"
                wire:click="$set('filter', 'unread')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ $filter === 'unread' ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50' }}"
            >
                Belum Dibaca
                @if ($unreadCount > 0)
                    <span class="ml-1 rounded-full bg-error-500 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $unreadCount }}</span>
                @endif
            </button>
            <button
                type="button"
                wire:click="$set('filter', 'read')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ $filter === 'read' ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50' }}"
            >
                Sudah Dibaca
            </button>
        </div>

        @if ($unreadCount > 0)
            <button
                type="button"
                wire:click="markAllAsRead"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
            >
                <i class="fa-solid fa-check-double text-xs"></i>
                Tandai Semua Dibaca
            </button>
        @endif
    </div>

    {{-- List --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
        <div class="divide-y divide-gray-100">
            @forelse ($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $type = $notification->data['type'] ?? null;
                    $icon = match($type) {
                        'approval_request' => 'fa-file-circle-question',
                        'asset_problem' => 'fa-triangle-exclamation',
                        default => 'fa-bell',
                    };
                    $iconBg = match($type) {
                        'approval_request' => 'bg-brand-50 text-brand-500',
                        'asset_problem' => 'bg-error-50 text-error-500',
                        default => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <div
                    wire:key="notif-{{ $notification->id }}"
                    class="flex items-start gap-4 px-5 py-4 transition {{ $isUnread ? 'bg-brand-25' : '' }} hover:bg-gray-50"
                >
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $iconBg }}">
                        <i class="fa-solid {{ $icon }} text-sm"></i>
                    </div>

                    <a href="{{ $notification->data['url'] ?? '#' }}" wire:click="markAsRead('{{ $notification->id }}')" class="min-w-0 flex-1">
                        <p class="text-sm font-medium {{ $isUnread ? 'text-gray-800' : 'text-gray-500' }}">
                            {{ $notification->data['title'] ?? 'Notifikasi' }}
                            @if ($isUnread)
                                <span class="ml-1.5 inline-block h-2 w-2 rounded-full bg-brand-500 align-middle"></span>
                            @endif
                        </p>
                        <p class="mt-0.5 text-sm text-gray-500">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }} &middot; {{ $notification->created_at->format('d M Y, H:i') }}</p>
                    </a>

                    <div class="flex shrink-0 items-center gap-1">
                        @if ($isUnread)
                            <button
                                type="button"
                                wire:click="markAsRead('{{ $notification->id }}')"
                                class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-brand-500"
                                title="Tandai sudah dibaca"
                            >
                                <i class="fa-solid fa-check text-xs"></i>
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="markAsUnread('{{ $notification->id }}')"
                                class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                                title="Tandai belum dibaca"
                            >
                                <i class="fa-regular fa-envelope text-xs"></i>
                            </button>
                        @endif

                        <button
                            type="button"
                            wire:click="deleteNotification('{{ $notification->id }}')"
                            wire:confirm="Hapus notifikasi ini?"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-error-50 hover:text-error-500"
                            title="Hapus"
                        >
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center gap-2 px-5 py-16 text-center text-gray-400">
                    <i class="fa-solid fa-bell-slash text-3xl"></i>
                    <p class="text-sm">
                        @if ($filter === 'unread')
                            Tidak ada notifikasi yang belum dibaca.
                        @elseif ($filter === 'read')
                            Tidak ada notifikasi yang sudah dibaca.
                        @else
                            Belum ada notifikasi.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>