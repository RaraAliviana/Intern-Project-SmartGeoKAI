<header class="sticky top-0 z-30 flex w-full border-b border-gray-200 bg-white">

    <div class="flex w-full items-center justify-between gap-3 px-4 py-3 md:px-6">

        {{-- ================================================================
             LEFT SECTION
        ================================================================= --}}
        <div class="flex items-center gap-3">

            {{-- Sidebar toggle --}}
            <button
                type="button"
                @click="window.innerWidth >= 1024
                    ? (expanded = !expanded)
                    : (mobileOpen = !mobileOpen)"
                aria-label="Toggle sidebar"
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700"
            >
                <i class="fa-solid fa-bars"></i>
            </button>

            {{-- Search (desktop) --}}
            <form onsubmit="return false;" class="hidden lg:block">
                <div class="relative">

                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                    <input
                        type="search"
                        placeholder="Cari aset, petugas..."
                        class="h-11 w-[320px] rounded-lg border border-gray-200 bg-gray-50 pl-11 pr-4 text-sm text-gray-700 placeholder:text-gray-400 outline-none transition focus:border-brand-300 focus:bg-white focus:ring-3 focus:ring-brand-500/10"
                    >

                </div>
            </form>

        </div>


        {{-- ================================================================
             RIGHT SECTION
        ================================================================= --}}
        <div class="flex items-center gap-2 sm:gap-4">

            {{-- Search icon (mobile) --}}
            <button
                type="button"
                aria-label="Search"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-50 lg:hidden"
            >
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>


            {{-- ============================================================
                 NOTIFICATION
                 Hanya Admin yang dapat melihat lonceng notifikasi.
            ============================================================= --}}
            @if (auth()->user()?->role !== 'superadmin')
                @livewire('admin.notification-bell')
            @endif


            {{-- ============================================================
                 USER DROPDOWN
            ============================================================= --}}
            <div class="relative" x-data="{ open: false }">

                <button
                    type="button"
                    @click="open = !open"
                    @click.outside="open = false"
                    class="flex items-center gap-3 rounded-lg py-1.5 pl-1.5 pr-2 transition hover:bg-gray-50"
                >

                    {{-- Avatar --}}
                    <img
                        src="{{ asset('images/icon/avatar-01.jpg') }}"
                        alt="User"
                        class="h-9 w-9 rounded-full object-cover"
                    >

                    {{-- User information --}}
                    <span class="hidden text-left sm:block">

                        {{-- Nama pengguna --}}
                        <span class="block text-sm font-medium leading-tight text-gray-700">
                            {{ auth()->user()->full_name ?? 'Admin' }}
                        </span>

                        {{-- Role pengguna --}}
                        <span class="block text-xs leading-tight text-gray-400">
                            {{ auth()->user()->role === 'superadmin'
                                ? 'Super Admin'
                                : 'Administrator'
                            }}
                        </span>

                    </span>

                    <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>

                </button>


                {{-- ========================================================
                     DROPDOWN CONTENT
                ========================================================= --}}
                <div
                    x-show="open"
                    x-cloak
                    x-transition
                    class="absolute right-0 mt-2 w-56 rounded-xl border border-gray-200 bg-white p-2 shadow-theme-lg"
                >

                    {{-- Profile --}}
                    <a
                        href="{{ route('admin.profile.index') }}"
                        class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-50"
                    >
                        <i class="fa-solid fa-user w-4 text-gray-400"></i>
                        Profil Saya
                    </a>

                    <hr class="my-2 border-gray-100">


                    {{-- Logout --}}
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-error-600 hover:bg-error-50"
                        >
                            <i class="fa-solid fa-right-from-bracket w-4"></i>
                            Keluar
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</header>
