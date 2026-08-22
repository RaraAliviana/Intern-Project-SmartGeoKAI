@extends('layouts.app')

@section('title', 'Backup')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Backup Database</h1>
        <p class="text-sm text-gray-500">Pengelolaan pencadangan database SmartGeoKAI.</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
        <div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-50">
                <i class="fa-solid fa-database text-2xl text-brand-500"></i>
            </div>
            <h2 class="text-base font-semibold text-gray-800">Backup Database</h2>
            <p class="max-w-xs text-sm text-gray-500">Fitur backup akan dihubungkan kemudian.</p>
            <button
                type="button"
                class="mt-2 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600"
            >
                <i class="fa-solid fa-download"></i>
                Buat Backup
            </button>
        </div>
    </div>

@endsection
