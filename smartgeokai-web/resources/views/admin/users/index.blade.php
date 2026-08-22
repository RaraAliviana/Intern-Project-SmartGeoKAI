@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengguna</h1>
            <p class="text-sm text-gray-500">Kelola akun admin dan petugas SmartGeoKAI.</p>
        </div>
        
        <a 
            href="{{ route('admin.users.create') }}"
            class="inline-flex w-fit items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Pengguna
        </a>
    </div>

    @livewire('admin.user-table')
@endsection