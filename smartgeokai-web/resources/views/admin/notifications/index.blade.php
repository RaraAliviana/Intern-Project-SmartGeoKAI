@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
        <p class="text-sm text-gray-500">Semua notifikasi terkait pengajuan perubahan status dan aset bermasalah.</p>
    </div>

    @livewire('admin.notification-list')

@endsection