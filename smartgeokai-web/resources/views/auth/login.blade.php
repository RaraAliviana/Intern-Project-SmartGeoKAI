<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmartGeo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { outfit: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#ecf3ff', 300: '#9cb9ff', 500: '#465fff', 600: '#3641f5' },
                        error: { 50: '#fef3f2', 200: '#fecdca', 600: '#d92d20', 700: '#b42318' },
                    },
                    boxShadow: {
                        'theme-lg': '0px 12px 16px -4px rgba(16,24,40,0.08), 0px 4px 6px -2px rgba(16,24,40,0.03)',
                    }
                }
            }
        }
    </script>
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>

<body class="flex min-h-screen items-center justify-center bg-gray-50 px-4">

    <div class="w-full max-w-md">

        <div class="mb-8 flex flex-col items-center">
            <div class="mb-3 flex h-16 w-auto items-center justify-center p-2">
                <img 
                    src="{{ asset('images/logo-kai.png') }}" 
                    alt="Logo KAI" 
                    class="h-12 w-auto object-contain"
                >
            </div>
            <h1 class="text-xl font-bold text-gray-800">SmartGeo</h1>
            <p class="text-sm text-gray-500">Panel Administrator</p>
        </div>

        {{-- ========================================================= --}}
        {{-- CARD FORM LOGIN --}}
        {{-- ========================================================= --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-theme-lg">

            <h2 class="mb-1 text-lg font-semibold text-gray-800">Masuk ke akun kamu</h2>
            <p class="mb-6 text-sm text-gray-500">Khusus untuk Admin. Petugas silakan gunakan aplikasi mobile.</p>

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Username</label>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        autofocus
                        placeholder="Masukkan username"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                    >
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10"
                    >
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    Ingat saya
                </label>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600"
                >
                    Masuk
                </button>

            </form>

        </div>

    </div>

</body>
</html>