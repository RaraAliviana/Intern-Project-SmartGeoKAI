<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SmartGeoKAI - @yield('title', 'Dashboard')</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome (untuk ikon) --}}
    <link href="{{ asset('vendor/fontawesome-7.3.1/css/all.min.css') }}" rel="stylesheet">

    {{-- Tailwind CSS (Play CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            25: '#f2f7ff', 50: '#ecf3ff', 100: '#dde9ff', 200: '#c2d6ff',
                            300: '#9cb9ff', 400: '#7592ff', 500: '#465fff', 600: '#3641f5',
                            700: '#2a31d8', 800: '#252dae', 900: '#262e89', 950: '#161950',
                        },
                        gray: {
                            25: '#fcfcfd', 50: '#f9fafb', 100: '#f2f4f7', 200: '#e4e7ec',
                            300: '#d0d5dd', 400: '#98a2b3', 500: '#667085', 600: '#475467',
                            700: '#344054', 800: '#1d2939', 900: '#101828', 950: '#0c111d',
                        },
                        success: {
                            25: '#f6fef9', 50: '#ecfdf3', 100: '#d1fadf', 200: '#a6f4c5',
                            300: '#6ce9a6', 400: '#32d583', 500: '#12b76a', 600: '#039855',
                            700: '#027a48', 800: '#05603a', 900: '#054f31',
                        },
                        error: {
                            25: '#fffbfa', 50: '#fef3f2', 100: '#fee4e2', 200: '#fecdca',
                            300: '#fda29b', 400: '#f97066', 500: '#f04438', 600: '#d92d20',
                            700: '#b42318', 800: '#912018', 900: '#7a271a',
                        },
                        warning: {
                            25: '#fffcf5', 50: '#fffaeb', 100: '#fef0c7', 200: '#fedf89',
                            300: '#fec84b', 400: '#fdb022', 500: '#f79009', 600: '#dc6803',
                            700: '#b54708', 800: '#93370d', 900: '#7a2e0e',
                        },
                    },
                    boxShadow: {
                        'theme-xs': '0px 1px 2px 0px rgba(16, 24, 40, 0.05)',
                        'theme-sm': '0px 1px 3px 0px rgba(16, 24, 40, 0.1), 0px 1px 2px 0px rgba(16, 24, 40, 0.06)',
                        'theme-md': '0px 4px 8px -2px rgba(16, 24, 40, 0.1), 0px 2px 4px -2px rgba(16, 24, 40, 0.06)',
                        'theme-lg': '0px 12px 16px -4px rgba(16, 24, 40, 0.08), 0px 4px 6px -2px rgba(16, 24, 40, 0.03)',
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Outfit', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #d0d5dd; border-radius: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
    </style>

    @livewireStyles
    @vite('resources/css/app.css')
    @stack('styles')

</head>

<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ expanded: window.innerWidth >= 1024, mobileOpen: false }">

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Mobile backdrop --}}
    <div
        x-show="mobileOpen"
        x-cloak
        @click="mobileOpen = false"
        class="fixed inset-0 z-40 bg-gray-900/40 lg:hidden"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    {{-- Page wrapper --}}
    <div
        class="transition-all duration-300 ease-in-out"
        :class="expanded ? 'lg:ml-[270px]' : 'lg:ml-[90px]'"
    >

        {{-- Header --}}
        @include('layouts.header')

        {{-- Content --}}
        <main class="p-4 md:p-6 2xl:p-10 max-w-[1800px] mx-auto">

            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-sm font-medium text-success-700">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-error-200 bg-error-50 px-4 py-3 text-sm font-medium text-error-700">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')

        </main>

    </div>

    {{-- JavaScript --}}
    <script src="{{ asset('vendor/bootstrap-5.3.8.bundle.min.js') }}"></script>
    <script src="{{ asset('js/main-vanilla.js') }}"></script>

    {{-- Livewire membawa Alpine.js bawaan — harus dimuat paling akhir --}}
    @livewireScripts

    @stack('scripts')

</body>

</html>