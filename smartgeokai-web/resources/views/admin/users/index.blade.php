@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengguna</h1>
            <p class="text-sm text-gray-500">Kelola akun admin dan petugas SmartGeoKAI.</p>
        </div>
    </div>

    @livewire('admin.user-table')
@endsection