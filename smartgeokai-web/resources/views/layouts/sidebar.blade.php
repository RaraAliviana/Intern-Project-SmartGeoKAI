<aside
    x-cloak
    class="fixed top-0 left-0 z-50 flex h-screen flex-col border-r border-gray-200 bg-white px-4 transition-all duration-300 ease-in-out"
    :class="[
        (expanded || mobileOpen) ? 'w-[270px]' : 'w-[90px]',
        mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    @mouseenter="if (!expanded && window.innerWidth >= 1024) expanded = true"
>

    {{-- ================================================================
         LOGO
    ================================================================= --}}

    <div
        class="flex h-20 items-center"
        :class="(expanded || mobileOpen) ? 'justify-start' : 'lg:justify-center'"
    >

        <!-- STRUKTUR LAMA (Ikon Lingkaran SG + Teks) -->
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3">
            <!-- Logo KAI -->
            <img src="{{ asset('images/logo-kai.png') }}" alt="Logo KAI" class="h-9 w-auto object-contain">
            
            <!-- Teks SmartGeo -->
            <div>
                <h1 class="text-base font-bold text-gray-800">SmartGeoKAI</h1>
                <p class="text-xs text-brand-500 font-medium">Asset Management</p>
            </div>
        </a>

    </div>


    {{-- ================================================================
         MENU
    ================================================================= --}}

    <div class="flex flex-1 flex-col overflow-y-auto overflow-x-hidden pb-6">

        <nav class="mb-6">

            {{-- Judul Menu --}}
            <h2
                class="mb-3 flex text-xs uppercase leading-5 text-gray-400"
                :class="(expanded || mobileOpen) ? 'justify-start' : 'lg:justify-center'"
            >

                <span
                    x-show="expanded || mobileOpen"
                    x-cloak
                >
                    Menu Utama
                </span>

                <i
                    x-show="!(expanded || mobileOpen)"
                    x-cloak
                    class="fa-solid fa-ellipsis"
                ></i>

            </h2>


            <ul class="flex flex-col gap-1">


                {{-- ========================================================
                     DASHBOARD
                ========================================================= --}}

                @php
                    $dashboardActive = request()->routeIs('admin.dashboard');
                @endphp

                <li>

                    <a
                        href="{{ route('admin.dashboard') }}"
                        title="Dashboard"
                        class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                        {{ $dashboardActive
                            ? 'bg-brand-50 text-brand-600'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'
                        }}"
                        :class="!(expanded || mobileOpen) && 'lg:justify-center'"
                    >

                        <i
                            class="fa-solid fa-gauge-high w-5 text-center
                            {{ $dashboardActive
                                ? 'text-brand-500'
                                : 'text-gray-400 group-hover:text-gray-600'
                            }}"
                        ></i>

                        <span
                            x-show="expanded || mobileOpen"
                            x-cloak
                            class="whitespace-nowrap"
                        >
                            Dashboard
                        </span>

                    </a>

                </li>


                {{-- ========================================================
                     PETA & ASET
                ========================================================= --}}

                @php
                    $assetMenuActive = request()->routeIs('admin.assets.*');
                @endphp

                <li
                    x-data="{
                        assetOpen: {{ $assetMenuActive ? 'true' : 'false' }}
                    }"
                >

                    {{-- Parent --}}
                    <button
                        type="button"
                        title="Peta & Aset"
                        @click="assetOpen = !assetOpen"
                        class="group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                        {{ $assetMenuActive
                            ? 'bg-brand-50 text-brand-600'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'
                        }}"
                        :class="!(expanded || mobileOpen) && 'lg:justify-center'"
                    >

                        <i
                            class="fa-solid fa-map-location-dot w-5 text-center
                            {{ $assetMenuActive
                                ? 'text-brand-500'
                                : 'text-gray-400 group-hover:text-gray-600'
                            }}"
                        ></i>


                        <span
                            x-show="expanded || mobileOpen"
                            x-cloak
                            class="flex-1 whitespace-nowrap text-left"
                        >
                            Peta & Aset
                        </span>


                        <i
                            x-show="expanded || mobileOpen"
                            x-cloak
                            class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200"
                            :class="assetOpen ? 'rotate-180' : ''"
                        ></i>

                    </button>


                    {{-- Submenu --}}
                    <div
                        x-show="assetOpen && (expanded || mobileOpen)"
                        x-cloak
                        class="mt-1 space-y-1 pl-8"
                    >

                        {{-- Peta Aset --}}
                        <a
                            href="{{ route('admin.assets.map') }}"
                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors
                            {{ request()->routeIs('admin.assets.map')
                                ? 'bg-brand-50 font-medium text-brand-600'
                                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800'
                            }}"
                        >

                            <i class="fa-solid fa-map text-xs"></i>

                            <span>
                                Peta Aset
                            </span>

                        </a>


                        {{-- Daftar Aset --}}
                        <a
                            href="{{ route('admin.assets.index') }}"
                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors
                            {{ request()->routeIs('admin.assets.index')
                                ? 'bg-brand-50 font-medium text-brand-600'
                                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800'
                            }}"
                        >

                            <i class="fa-solid fa-list text-xs"></i>

                            <span>
                                Daftar Aset
                            </span>

                        </a>

                    </div>

                </li>


                {{-- ========================================================
                     MENU LAINNYA
                ========================================================= --}}

                @php
                    $menuItems = [

                        [
                            'route' => 'admin.users.index',
                            'active' => 'admin.users.*',
                            'icon' => 'fa-users',
                            'label' => 'Manajemen Pengguna',
                        ],

                        [
                            'route' => 'admin.approvals.index',
                            'active' => 'admin.approvals.*',
                            'icon' => 'fa-circle-check',
                            'label' => 'Approval',
                        ],

                        [
                            'route' => 'admin.reports.index',
                            'active' => 'admin.reports.*',
                            'icon' => 'fa-chart-column',
                            'label' => 'Laporan',
                        ],

                        [
                            'route' => 'admin.import.index',
                            'active' => 'admin.import.*',
                            'icon' => 'fa-file-import',
                            'label' => 'Import Data',
                        ],

                        [
                            'route' => 'admin.notifications.index',
                            'active' => 'admin.notifications.*',
                            'icon' => 'fa-bell',
                            'label' => 'Notifikasi',
                        ],

                        [
                            'route' => 'admin.log-audit.index',
                            'active' => 'admin.log-audit.*',
                            'icon' => 'fa-clock-rotate-left',
                            'label' => 'Log Audit',
                        ],

                        [
                            'route' => 'admin.backup.index',
                            'active' => 'admin.backup.*',
                            'icon' => 'fa-database',
                            'label' => 'Backup',
                        ],

                    ];
                @endphp


                @foreach ($menuItems as $item)

                    @php
                        $isActive = request()->routeIs($item['active']);
                    @endphp

                    <li>

                        <a
                            href="{{ route($item['route']) }}"
                            title="{{ $item['label'] }}"
                            class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                            {{ $isActive
                                ? 'bg-brand-50 text-brand-600'
                                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'
                            }}"
                            :class="!(expanded || mobileOpen) && 'lg:justify-center'"
                        >

                            <i
                                class="fa-solid {{ $item['icon'] }} w-5 text-center
                                {{ $isActive
                                    ? 'text-brand-500'
                                    : 'text-gray-400 group-hover:text-gray-600'
                                }}"
                            ></i>

                            <span
                                x-show="expanded || mobileOpen"
                                x-cloak
                                class="whitespace-nowrap"
                            >
                                {{ $item['label'] }}
                            </span>

                        </a>

                    </li>

                @endforeach

            </ul>

        </nav>


        {{-- ================================================================
             PROFILE & LOGOUT
        ================================================================= --}}

        <div class="mt-auto flex flex-col gap-1 border-t border-gray-100 pt-4">

            {{-- Profil --}}
            @php
                $profileActive = request()->routeIs('admin.profile.*');
            @endphp

            <a
                href="{{ route('admin.profile.index') }}"
                title="Profil Saya"
                class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                {{ $profileActive
                    ? 'bg-brand-50 text-brand-600'
                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'
                }}"
                :class="!(expanded || mobileOpen) && 'lg:justify-center'"
            >

                <i
                    class="fa-solid fa-user w-5 text-center
                    {{ $profileActive
                        ? 'text-brand-500'
                        : 'text-gray-400 group-hover:text-gray-600'
                    }}"
                ></i>

                <span
                    x-show="expanded || mobileOpen"
                    x-cloak
                    class="whitespace-nowrap"
                >
                    Profil Saya
                </span>

            </a>


            {{-- Logout --}}
            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    title="Keluar"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-500 transition-colors hover:bg-error-50 hover:text-error-600"
                    :class="!(expanded || mobileOpen) && 'lg:justify-center'"
                >

                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>

                    <span
                        x-show="expanded || mobileOpen"
                        x-cloak
                        class="whitespace-nowrap"
                    >
                        Keluar
                    </span>

                </button>

            </form>

        </div>

    </div>

</aside>