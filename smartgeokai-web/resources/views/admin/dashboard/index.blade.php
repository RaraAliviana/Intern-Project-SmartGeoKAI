@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="mb-6 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-500">Ringkasan kondisi sistem SmartGeoKAI secara keseluruhan.</p>
        </div>
        <p class="text-sm text-gray-400">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    @livewire('admin.dashboard-stats')

@endsection